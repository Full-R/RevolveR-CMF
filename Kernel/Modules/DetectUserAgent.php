<?php

 /* 
  * RevolveR User Agent
  *
  * v.1.8.0
  *
  *
  *
  *
  *
  *			          ^
  *			         | |
  *			       @#####@
  *			     (###   ###)-.
  *			   .(###     ###) \
  *			  /  (###   ###)   )
  *			 (=-  .@#####@|_--"
  *			 /\    \_|l|_/ (\
  *			(=-\     |l|    /
  *			 \  \.___|l|___/
  *			 /\      |_|   /
  *			(=-\._________/\
  *			 \             /
  *			   \._________/
  *			     #  ----  #
  *			     #   __   #
  *			     \########/
  *
  *
  *
  * Developer: Dmitry Maltsev
  *
  * License: Apache 2.0
  *
  */

final class DetectUserAgent {

	public static $lbs = [];

	protected function __clone() {

	}

	public static function getInfo( ?string $ua = null ): iterable {

		$platform	= null;
		$browser	= null;
		$version	= null;

		// lowercase browser stack
		self::$lbs = [];

		$empty = [

			'platform' => $platform,
			'browser'  => $browser,
			'version'  => $version

		];

		if( !$ua ) {

			return $empty;

		}

		if( preg_match('/\((.*?)\)/im', $ua, $parent_matches) ) {

			preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|(Open|Net|Free)BSD|Macintosh|Windows(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|X11|(New\ )?Nintendo\ (WiiU?|3?DS|Switch)|Xbox(\ One)?)(?:\ [^;]*)?(?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);

			$result['platform'] = array_unique($result['platform']);

			if( count($result['platform']) > 1 ) {

				if( $keys = array_intersect(['Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android', 'FreeBSD', 'NetBSD', 'OpenBSD', 'CrOS', 'X11'], $result['platform']) ) {

					$platform = reset($keys);

				}
				else {

					$platform = $result['platform'][0];

				}

			}
			else if( isset($result['platform'][0]) ) {

				$platform = $result['platform'][0];

			}

		}

		if( $platform === 'linux-gnu' || $platform === 'X11' ) {

			$platform = 'Linux';

		}
		else if( $platform === 'CrOS' ) {

			$platform = 'Chrome OS';

		}

		preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|PrivacyBrowser|Iceweasel|IceCat|Safari|MSIE|Trident|AppleWebKit|
					TizenBrowser|Chrome|Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|UCBrowser|Puffin|SamsungBrowser|
					Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|Valve\ Steam\ Tenfoot|
					NintendoBrowser|PLAYSTATION\ (\d|Vita)+)(?:\)?;?)(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix', $ua, $result, PREG_PATTERN_ORDER);

		// If nothing matched, return null (to avoid undefined index errors)
		if( !isset($result['browser'][0]) || !isset($result['version'][0]) ) {

			if( preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $ua, $result) ) {

				return [

					'platform' => $platform, 
					'browser'  => $result['browser'], 
					'version'  => $result['version'] ?? null,

				];

			}

			return $empty;

		}

		if( preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $ua, $rv_result) ) {

			$rv_result = $rv_result['version'];

		}


		$browser = $result['browser'][0] === 'AppleWebKit' ? 'Chromium' : $result['browser'][0]; //$result['browser'][0];

		$version = $result['version'][0];

		$test_for_edge = explode(' ', $ua);
		$test_for_edge = explode('/', $test_for_edge[ count( $test_for_edge ) - 1 ] );

		if( $test_for_edge[0] === 'Edg' ) {

			$browser = 'Microsoft Edge';

			$version = $test_for_edge[1];

		}

		self::$lbs = array_map('strtolower', $result['browser']);

		if( $result['browser'][0] === 'PrivacyBrowser' ) {

			$platform = 'Privacy';

		}

		if( $browser === 'Iceweasel' || strtolower($browser) === 'icecat' ) {

			$browser = 'Firefox';

		}
		else if( self::checkAgent(['Playstation Vita']) ) {

			$platform = 'PlayStation Vita';

			$browser  = 'Browser';

		}
		else if( self::checkAgent(['Kindle Fire', 'Silk']) ) {

			$platform = 'Kindle Fire';

			$browser  = 'Silk';

			if( !($version = $result['version'][0]) || !is_numeric($version[0]) ) {

				$version = $result['version'][array_search('Version', $result['browser'])];

			}

		}
		else if( self::checkAgent(['NintendoBrowser']) || $platform === 'Nintendo 3DS' ) {

			$version = $result['version'][0];

			$browser = 'NintendoBrowser';

		}
		else if( self::checkAgent(['Kindle']) ) {

			$browser = $result['browser'][0];

			$version = $result['version'][0];

		}
		else if( self::checkAgent(['OPR']) ) {

			$version = $result['version'][0];

			$browser = 'Opera Next';

		}
		else if( self::checkAgent(['Opera']) ) {

			$version = $result['version'][0];

		}
		else if( self::checkAgent(['Puffin']) ) {

			$version = $result['version'][0];

			if( strlen($version) > 3 ) {

				$part = substr($version, -2);

				if( ctype_upper($part) ) {

					$version = substr($version, 0, -2);

					$flags = [

						'IP' => 'iPhone', 
						'IT' => 'iPad', 
						'AP' => 'Android', 
						'AT' => 'Android', 
						'WP' => 'Windows Phone', 
						'WT' => 'Windows' 

					];

					if( isset($flags[$part]) ) {

						$platform = $flags[ $part ];

					}

				}

			}

		}
		else if( self::checkAgent(['IEMobile', 'Edge', 'Midori', 'Vivaldi', 'SamsungBrowser', 'Valve Steam Tenfoot', 'Chrome']) ) {

			$version = $result['version'][0];

		}
		else if( $rv_result && self::checkAgent(['Trident']) ) {

			$version = $rv_result;

			$browser = 'MSIE';

		}
		else if( self::checkAgent(['UCBrowser']) ) {

			$version = $result['version'][0];

			$browser = 'UC Browser';

		}
		else if( self::checkAgent(['CriOS']) ) {

			$version = $result['version'][0];

			$browser = 'Chrome';

		}
		else if( $browser === 'AppleWebKit' ) {

			if( $platform === 'Android' ) {

				$browser = 'Android Browser';

			}
			else if( !(bool)strpos($platform, 'BB') ) {

				$browser  = 'BlackBerry Browser';

				$platform = 'BlackBerry';

			}
			else if( $platform === 'BlackBerry' || $platform === 'PlayBook' ) {

				$browser = 'BlackBerry Browser';

			}

			$version = $result['version'][0];

		}
		else if( $pKey = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser'])) ) {

			$platform = 'PlayStation '. preg_replace('/[^\d]/i', '', reset($pKey));

			$browser  = 'NetFront';

		}

		return [

			'platform' => $platform,
			'browser'  => $browser,
			'version'  => $version

		];

	}

	// Is user agent match
	protected static function checkAgent( ?iterable $search = null ): ?bool {

		$match = null;

		if( $search ) {

			foreach( $search as $v ) {

				if( (bool)preg_grep('/'. $v .'/i', self::$lbs )[1] ) {

					$match = true;

				}

			}

		}

		return $match;

	}

}

?>
