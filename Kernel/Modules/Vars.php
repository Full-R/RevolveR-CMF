<?php

 /* 
  * 
  * RevolveR Secure Variables Dispatch
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
  */

//var_dump($_SERVER['DOCUMENT_ROOT']);

//'xxd '. $f[0] .' | head -1'

final class SecureVariablesDispatcher {

	public static $file_descriptors = [

		'jpg' => [

			'mime'			=> 'image/jpeg',
			'extension'		=> 'jpg',
			'max-size'		=> '1000000',
			'description'	=> 'Photo file',

			'identity' 		=> [

				'U' => 'ffd8',

			]

		],

		'jpeg' => [

			'mime'			=> 'image/jpeg',
			'extension'		=> 'jpeg',
			'max-size'		=> '1000000',
			'description'	=> 'Photo file',

			'identity' 		=> [

				'U' => 'ffd8',

			]

		],

		'png' => [

			'mime'			=> 'image/png',
			'extension'		=> 'png',
			'max-size'		=> '5000000',
			'description'	=> 'Image with tranceparencity support',

			'identity' 		=> [

				'U' => '8950',

			]

		],

		'webp' => [

			'mime'			=> 'image/webp',
			'extension'		=> 'webp',
			'max-size'		=> '300000',
			'description'	=> 'Google Image file',

			'identity' 		=> [

				'U' => '5249',

			]

		],

		'pdf' => [

			'mime'		=> 'application/pdf',
			'extension' => 'pdf',
			'max-size'	=> '1500000',
			'description' => 'Portable Document Format file',

			'identity' 		=> [

				'U' => '2550',

			]

		],

		'doc' => [

			'mime'			=> 'application/msword',
			'extension' 	=> 'doc',
			'max-size'		=> '500000',
			'description' 	=> 'MS Word document',
			'identity' 		=> [

				'U' => 'd0cf',

			]

		],

		'docx' => [

			'mime'			=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'extension'		=> 'docx',
			'max-size'		=> '500000',
			'description'	=> 'MS Word document',
			'identity' 		=> [

				'U' => '504b',

			]

		],

		'rtf' => [

			'mime'			=> 'application/rtf',
			'extension'		=> 'rtf',
			'max-size'		=> '800000',
			'description'	=> 'Rich Text document',

			'identity'		=> [

				'U' => '7b5c',

			]

		],

/*
		'xls' => [

			'mime'		=> 'application/vnd.ms-excel',
			'extension' => 'xls',
			'max-size'	=> '500000',
			'description' => 'MS Excel document',

			
			'identity'		=> [

				'U' => '7b5c',

			]

		],

		'xlsx' => [

			'mime'		=> 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'extension' => 'xlsx',
			'max-size'	=> '500000',
			'description' => 'MS Excel document'

		],

		'ppt' => [

			'mime'		=> 'application/vnd.ms-powerpoint',
			'extension' => 'ppt',
			'max-size'	=> '10000000',
			'description' => 'MS PowerPoint document'

		],

		'pptx' => [

			'mime'		=> 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'extension' => 'pptx',
			'max-size'	=> '10000000',
			'description' => 'MS PowerPoint document'

		],
*/
		'odt' => [

			'mime'      => 'application/vnd.oasis.opendocument.text',
			'extension' => 'odt',
			'max-size'	=> '10000000',
			'description' => 'OpenDocument Text file',

			'identity'		=> [

				'U' => '504b',

			]

		],

		'zip' => [

			'mime'		=> 'application/zip',
			'extension' => 'zip',
			'max-size'	=> '150000000',
			'description' => 'Archived files',

			'identity'		=> [

				'U' => '504b',

			]

		],

		'tar' => [

			'mime'		=> 'application/tar',
			'extension' => 'tar',
			'max-size'	=> '150000000',
			'description' => 'Archived files',

			'identity'		=> [

				'U' => '7461',

			]
 
		],

		'tar.gz' => [

			'mime'		=> 'application/tar+gzip',
			'extension' => 'gz',
			'max-size'	=> '150000000',
			'description' => 'Archived files',

			'identity'		=> [

				'U' => '1f8b',

			]

		],

		'txt' => [

			'mime'		=> 'text/plain',
			'extension' => 'txt',
			'max-size'	=> '500000',
			'description' => 'Text document',

			'identity'		=> [

				'U' => 'd09a',

			]

		]

	];

	public static $notify;

	protected function __clone() {

	}

	function __construct( Notifications $notify ) {

		self::$notify = $notify;

	}

	public static function get(): iterable {

		$vars = [];

		if( (bool)count( $_FILES ) ) {

			foreach( $_FILES as $F ) {

				if( !(bool)$F['error'] ) {

					$ext = explode('.', self::escape( trim($F['name']) ));

					$isFileValid = self::isValidFile( [$F['tmp_name'], $F['size'], [$F['type'], $ext[count($ext) - 1]], self::escape(trim($F['name']))] );

					if( !(bool)$isFileValid ) {

						self::$notify::set('notice', 'Invalid FILE in POST');

					}

					$vars['f'][ base64_decode( key($_FILES) ) ][] = [

						'name'  => self::escape(trim($F['name'])),
						'size'  => round((int)$F['size'] / 1024, 1), // Kb
						'type'  => explode('/', $F['type']),
						'temp'  => $F['tmp_name'],
						'valid' => $isFileValid

					];

				}

			}

		}

		// [SESSION]
		if( (bool)count($_SESSION) ) {

			foreach( $_SESSION as $sn => $sv ) {

				$vars['s'][ self::escape($sn) ] = self::escape($sv);

			}

		}

		// [COOKIES]
		if( (bool)count($_COOKIE) ) {

			foreach( filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_STRING | FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_ENCODED, FILTER_REQUIRE_ARRAY) as $cn => $cv ) {

				$vars['c'][ self::escape($cn) ] = self::escape($cv);

			}

		}

		// [POST && GET] variables
		$get_and_post_vars = iterator_to_array(

			self::perform()

		);

		$identity_index = 0;

		foreach( $get_and_post_vars as $r ) {

			$cv = explode('~:::~', $r);

			$op = [];

			if( $cv[3] === 'option') {

				$nm = explode( '***', $cv[1] );

				if( $cv[0] === 'p' ) {

					$validity = self::isValid( $cv[3], $cv[2], $cv[4] );

					if( !(bool)$validity ) {

						if( $field_name === 'revolver_captcha' ) {

							self::$notify::set('notice', 'Security check not pass');

						}

						if( $cv[3] !== 'hidden' && $cv[3] !== 'radio' && $cv[3] !== 'checkbox' ) {

							self::$notify::set('notice', 'Invalid POST '. $field_name .' :: [ input type '. $cv[3] .'; length: '. $cv[4] .' ]', null);

						}

					}

					$testEncode = explode('-_-_-', base64_decode($nm[0]));

					$field_name = isset( $testEncode[1] ) ? $testEncode[1] : $nm[0];

					$vars[ $cv[0] ][ $field_name ]['name'] = $field_name;

					$vars[ $cv[0] ][ $field_name ]['type'] = $cv[3];

					$vars[ $cv[0] ][ $field_name ]['value'][ $nm[count($nm) - 1] ] = $cv[2];

					$vars[ $cv[0] ][ $field_name ]['valid'] = $validity;

					if( (bool)$validity ) {

						$identity_index++;

					}

					$vars[ $cv[0] ]['identity']['validity'] = $identity_index === count($get_and_post_vars) ? 1 : 0;

					$vars[ $cv[0] ]['identity']['validity_count'] = $identity_index;

					$vars[ $cv[0] ]['identity']['fields_total'] = count($get_and_post_vars);

				}
				else {

					$vars[ $cv[0] ][ $nm[0] ]['name'] = $nm[0];

					$vars[ $cv[0] ][ $nm[0] ]['value'][ $nm[count($nm) - 1] ] = $cv[2];

				}

			}
			else {

				if( $cv[0] === 'p' ) {

					$validity = self::isValid($cv[3], $cv[2], $cv[4]);

					if( !(bool)$validity ) {

						if( $field_name === 'revolver_captcha' ) {

							self::$notify::set('notice', 'Security check not pass');

						}

						if( $cv[3] !== 'hidden' && $cv[3] !== 'radio' && $cv[3] !== 'checkbox' ) {

							self::$notify::set('notice', 'Invalid POST '. $field_name .' :: [ input type '. $cv[3] .'; length: '. $cv[4] .' ]', null);

						}

					}

					$testEncode = explode('-_-_-', base64_decode($cv[1]));

					$field_name = isset( $testEncode[1] ) ? $testEncode[1] : $cv[1];

					$vars[ $cv[0] ][ $field_name ]['name'] = $field_name;

					$vars[ $cv[0] ][ $field_name ]['value'] = $cv[2];

					$vars[ $cv[0] ][ $field_name ]['type'] = $cv[3];

					$vars[ $cv[0] ][ $field_name ]['valid'] = $validity;

					if( (bool)$validity ) {

						$identity_index++;

					}

					$vars[ $cv[0] ]['identity']['validity'] = $identity_index === count($get_and_post_vars) ? 1 : 0;

					$vars[ $cv[0] ]['identity']['validity_count'] = $identity_index;

					$vars[ $cv[0] ]['identity']['fields_total'] = count($get_and_post_vars);

				}
				else {

					$vars[ $cv[0] ][ $cv[1] ]['name'] = $cv[1];

					$vars[ $cv[0] ][ $cv[1] ]['value'] = $cv[2];

				}

			}

		}

		return $vars;

	}

	public static function setCookie(string $name, string $value, string $exp, string $path): void {

		$cst = 'Set-Cookie: '. $name .'='. rawurlencode($value) .'; Expires='. date('D, d M Y H:i:s', $exp) . 'GMT' .'; Path='. $path .'; Domain='. ltrim('.', $_SERVER['HTTP_HOST']) .';';

		header((((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (int)$_SERVER['SERVER_PORT'] === 443) ? $cst . ' SameSite=Strict; Secure=true;' : $cst));

	}

	protected static function perform(): iterable {

		$methods = (string)$_SERVER['REQUEST_METHOD'];

		$vars_dl = [];

		if( in_array( $methods, ['POST', 'GET'] ) ) {

			switch( $methods ) {

				case 'POST':

					$post_vars = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING | FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_ENCODED, FILTER_REQUIRE_ARRAY ) ?? [];

					$vars_dl = [ $post_vars, 'p' ];

					break;

				case 'GET':

					$get_vars = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING | FILTER_SANITIZE_SPECIAL_CHARS | FILTER_SANITIZE_ENCODED, FILTER_REQUIRE_ARRAY ) ?? [];

					$vars_dl = [ $get_vars, 'g' ]; 

					break;

			}

			$c = 0;

			foreach( $vars_dl[0] as $v => $f ) {

				if( !empty($f) ) {

					yield $c++ => ( $vars_dl[1] === 'p' ) ? $vars_dl[1] .'~:::~'. self::escape( base64_decode($v) ) .'~:::~'. self::escape(urldecode(base64_decode($f))) : $vars_dl[1] .'~:::~'. self::escape($v) .'~:::~'. self::escape($f);

				}

			}

		}
		else {

			exit('<h1>ACCESS Exception :: method '. $methods .' blocked!</h1>');

		}

	}

	protected static function isValidFile( iterable $f ): int {

		$valid = 0;

		if( isset(self::$file_descriptors[ $f[2][1] ]) ) { // check extension is correct

			if( explode(' ', str_replace( ['00000000: '], [''], shell_exec('xxd '. $f[0] .' | head -1')))[0] === self::$file_descriptors[ $f[2][1] ]['identity']['U'] ) { // check binarie mask is correct

			//if( self::$file_descriptors[ $f[2][1] ]['mime'] === $f[2][0] ) { // check mime type is correct

				//if( //!stripos(

					//shell_exec('xxd '. $f[0] .' | head -1')//, '7f45') 

				//) 

				if( (int)self::$file_descriptors[ $f[2][1] ]['max-size'] >= (int)$f[1] ) { // check file size possible

					if( preg_match('/(^[a-zA-Z0-9]+([a-zA-Z\_0-9\.-]*))$/', $f[3]) ) { // check file name is correct

						$valid = 1;

					}

				}

			}

		}

		return $valid;

	}

	protected static function isValid( string $t, string $v, string $l ): int {

		$flag = 0;

		switch( $t ) {

			case 'text':
			case 'radio':
			case 'search':
			case 'hidden':
			case 'option':
			case 'checkbox':
			case 'password':

				$flag = (bool)strlen($v) ? 1 : 0;

				break;

			case 'color':

				if( preg_match('/#?([[:xdigit:]]{3}){1,2}\b/i', $v) && (bool)strlen($v) ) {

					$flag = 1;

				}

				break;

			case 'email':

				if( filter_var($v, FILTER_VALIDATE_EMAIL) && (bool)strlen($v) ) {

					$flag = 1;

				}

				break;

			case 'url':

				if( filter_var($v, FILTER_VALIDATE_URL)  && (bool)strlen($v) ) {

					$flag = 1;

				}

			break;

			case 'number':
			case 'range':

				if( ( filter_var($v, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND) || !(bool)$v ) && (bool)strlen($v) ) {

					$flag = 1;

				}

				break;

			case 'tel':

				if( preg_match('/^[\+0-9\-\(\)\s]*$/', $v) && (bool)strlen($v) ) {

					$flag = 1;

				}

				break;

			case 'date':

				$d = explode('-', $v);

				if( checkdate($d[1], $d[2], $d[0]) && (bool)strlen($v) ) { 

					$flag = 1;

				}

				break;

			case 'time':

				if( preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $v) && (bool)strlen($v) ) {

					$flag = 1;

				}

				break;

		}

		if( $t === 'text' && (int)$l === -1 ) {

			return 1;

		}
		else {

			return ( (int)$l === -1 || strlen(trim($v)) <= (int)$l ? true : null ) && $flag ? 1 : 0;

		}

	}

	protected static function escape( string $v ): string {

	return trim(

			stripslashes(

				strip_tags(

					htmlspecialchars(

						str_ireplace(

							['/*', '--+', 'qq'], // clean some SQL injections

							['', '', ''],

							$v),

							ENT_IGNORE, 'utf-8')

				)

			)

		);

	}

}

?>
