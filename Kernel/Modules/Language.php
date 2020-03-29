<?php

 /*
  * 
  * RevolveR Countries Language
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
  *
  */

final class Language {

	protected function __clone() {

	}

	// Returns language code description by country code
	public static function getLanguageData( string $c = '*' ): iterable {

		$list = [];

		foreach( country_list as $cn ) {

			if( $cn['country_code']['cipher'] === $c ) {

				return [

					'code_length_2'  => $cn['country_code']['latin_2'],
					'code_length_3'  => $cn['country_code']['latin_3'],
					'cipher'		     => $cn['country_code']['cipher'],
					'name'			     => $cn['country_name'],
					'hreflang'		   => $cn['country_tail']

				];

				break;

			}
			else if( $c === '*' ) {

				$list[] = [

					'code_length_2' => $cn['country_code']['latin_2'],
					'code_length_3' => $cn['country_code']['latin_3'],
					'cipher'		    => $cn['country_code']['cipher'],
					'name'			    => $cn['country_name'],
					'hreflang'		  => $cn['country_tail']

				];

			}

		}

		return $list;

	}

}

?>
