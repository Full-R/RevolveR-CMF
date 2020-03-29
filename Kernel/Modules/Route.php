<?php

 /* 
  * 
  * RevolveR Route class  
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

final class Route {

	function __construct( iterable $n ) {

		$pathway = '/';

		$p = [];

		$c = 0;

		foreach( PASS as $w ) {

			if( $c++ > 0 && self::isSegment($w) ) {

				$p[] = $w;

			}

		}

		foreach( $p as $fk ) {

			if( self::isArguments($fk) ) {

				$pathway .= $fk .'/';

			}

		}

		foreach( $n as $r ) {

			if( self::matchSegment( $r['route'], $pathway ) ) { 

				if( isset( $r['ext'] ) ) {

					$ext = (bool)$r['ext'] ? true : null; 

				}
				else {

					$ext = null;

				}

				define(

					'ROUTE', [

						'route' => $r['route'], 
						'node'  => $r['node'],
						'type'  => $r['type'],

						'ext'	=> $ext // isExtension

					]

				);

			}

		}

		// Route addition pathway exception
		if( !defined('ROUTE') ) {

			// Add edit ROUTE to make possible merge into Switch		
			if( PASS[ count(PASS) - 2 ] === 'edit' ) {

				foreach( main_nodes as $mnx ) {

					if( PASS[ 1 ] === ltrim($mnx['node'], '#') ) {

						define( 

							'ROUTE', [

								'route' => $mnx['route'], 
								'node'  => $mnx['node'],
								'type'  => 'node',
								'edit'	=> 1

							]

						);

						break;

					}

				}

			}

		}

	}

	protected function __clone() {

	}

	private static function isSegment( string $c ): ?bool {

		return (bool)strlen($c) ? true : null;

	}

	protected static function isArguments( string $c ): ?bool {

		preg_match('/\?/i', $c, $m);

		return isset( $m[0] ) ? null : true;

	}

	private static function matchSegment( string $f1, string $f2 ): ?bool {

		return $f1 === $f2 ? true : null;

	}

}

?>
