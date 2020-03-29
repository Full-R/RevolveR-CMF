<?php

 /* 
  * RevolveR Statistics Class
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

final class Statistics {

	protected static $model; 

	function __construct( Model $model ) {

		self::$model = $model; 

	}

	protected function __clone() {

	}

	public static function writeHit( string $track ): void {

		$struct = [

			'ip'		 => self::getRealIP(),
			'date'		 => self::getRealTime()['date'],
			'time'		 => self::getRealTime()['time'],
			
			'track'		 => $track,
			
			'route'		 => $_SERVER['REQUEST_URI'],
			'user_agent' => $_SERVER['HTTP_USER_AGENT']

		];

		if( isset($_SERVER['HTTP_REFERER']) ) {

			if( in_array(explode('/', $_SERVER['HTTP_REFERER'])[2], [ ltrim(site_host, 'http://'), ltrim(site_host, 'https://') ], true) ) {

				$struct['referer'] = 'straight';

			}
			else {

				$struct['referer'] = parse_url( $_SERVER['HTTP_REFERER'] )['host'] . parse_url( $_SERVER['HTTP_REFERER'] )['path'];

			}

		}
		else {

			$struct['referer'] = 'straight';

		}

		// Exception for localhost
		if( $struct['ip'] !== '127.0.0.1' &&

			!in_array(

				// Dispatch routes and Service nodes exceptions
				$struct['route'], [

					'/secure/', 
					'/comments-d/', 
					'/contents-d/', 
					'/category-d/', 
					'/user-d/', 
					'/favicon.ico', 
					'/apple-touch-icon-precomposed.png', 
					'/apple-touch-icon.png'

				], true)

		) {

			self::$model::set('statistics', $struct);

		}

	}

	protected static function getRealTime(): array {

		return [

			'date' => date('Y/m/d'),
			'time' => date('H:i:s'),

		];

	}

	protected static function getRealIP(): string {

		if( !empty($_SERVER['HTTP_CLIENT_IP']) ) {

			return $_SERVER['HTTP_CLIENT_IP'];

		}
		else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

			return $_SERVER['HTTP_X_FORWARDED_FOR'];

		}
		else {

			return $_SERVER['REMOTE_ADDR'];

		}

	}

}

?>
