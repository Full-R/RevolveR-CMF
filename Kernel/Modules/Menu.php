<?php

 /* 
  * 
  * RevolveR Navigation Menu Class
  *
  * v.1.9.0
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
  *
  */

final class Menu {

	// Store markup of navigation menu
	public static $markup;

	function __construct( iterable $nodes, string $wrap_style ) {

		$output = '';

		switch ( $wrap_style ) {

			case 'ul':

				$style = 'list';

				$list_wrap = 'ul';

				$list_item_wrap = 'li';

				break;

			case 'span': 

				$style =  'plain';

				$list_wrap = null;

				$list_item_wrap = 'span';

				break;

		}

		$output = '';

		foreach( $nodes as $link => $v ) {

			if( isset( $v['ext'] ) ) {

				if( (bool)$v['ext'] ) {

					if( defined('EXTENSIONS_SETTINGS') ) {

						if( ROLE === 'Admin' ) {

							//unset( $v['param_check']['hidden'] );

						} 
						else {

							//$v['param_check']['hidden'] = 1;

						}

						foreach( EXTENSIONS_SETTINGS as $e ) {

							if( $e['name'] === $v['id'] ) {

								if( (bool)$e['installed'] && (bool)$e['enabled'] ) {

									unset( $v['param_check']['hidden'] );

								}

								break;

							}

						}

					}

				}

			}

			unset($v['descr'], $v['ext']);

			$geta = isset($v['geta']) ? '?'. $v['geta'] : '';

			unset( $v['geta'] );

			$attr = '';

			$wrp_class = null;

			if( ACCESS !== 'none' ) {

				if( isset($v['param_check']['isAdmin']) ) {

					if( isset(ACCESS['role']) ) {

						$v['param_check']['menu'] = (bool)$v['param_check']['isAdmin'] && ACCESS['role'] === 'Admin' ? 1 : 0;

					}

				}

				if( isset($v['param_check']['isWriter']) ) {

					if( isset(ACCESS['role']) ) {

						if( (bool)$v['param_check']['isWriter'] && ACCESS['role'] === 'Writer' ) {

							$v['param_check']['menu'] = 1;

						}

					}

				}

			}

			if( (bool)$v['param_check']['menu'] && !isset($v['param_check']['hidden']) ) {

				foreach( $v as $x => $t ) {

					if( !in_array($x, ['param_check', 'id', 'node', 'type'], true) ) {

						if( $x === 'route' ) {

							$x = 'href';

							$t = $t . $geta;

						}

						if( $x === 'class' ) {

							$wrp_class = self::attr( $x, $t );

						}
						else {

							if( !in_array($x, ['node', 'id', 'param_check', 'type'], true) ) {

								$attr .= self::attr( $x, $t );

							}

						}

					}

				}

				if( isset( $v['param_check']['auth'] ) ) {

					if( (int)$v['param_check']['auth'] === (int)SV['c']['authorization'] ) {

						$output .= self::wrap('<a '. $attr .'>'. $link .'</a>', [ $list_item_wrap, $wrp_class ]);

					}

				}
				else {

					$output .= self::wrap('<a '. $attr .'>'. $link .'</a>', [ $list_item_wrap, $wrp_class ]);

				}

			}

		}

		if( $style === 'list' ) {

			$output = self::wrap($output, [ $list_wrap, null ]);

		}

		self::$markup = $output;

	}

	protected function __clone() {

	}

	private static function attr( string $a, string $v ): string {

		return $a .'="'. $v .'" ';

	}

	private static function wrap( string $s, iterable $w ): string {

		return isset($w[ 1 ]) ? '<'. $w[ 0 ] .' '. $w[ 1 ] .'>'. $s .'</'. $w[ 0 ] .'>' : '<'. $w[ 0 ] .'>'. $s .'</'. $w[ 0 ] .'>';

	}

}

?>
