<?php

 /*
  * 
  * RevolveR Cipher Class
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

final class Cipher {

  const ALGORITHM = 'AES-256-CBC';
  const SHA = 'sha256';

  protected function __clone() {

  }

	public static function crypt( string $m, string $d ): ?string {
		
		$key = file_get_contents( $_SERVER['DOCUMENT_ROOT'] .'/private/Domain.key', true );

		if( (bool)strlen($key) ) {
		
    	$secret_key = $key . base64_decode('UmVWb0x2RXIjeDM0NiFAKg==');
		
    } 
    else {

      return null;

    }

		$k = hash( self::SHA, $secret_key );

		$v = substr(

      hash( self::SHA, '!IV@_$2' ), 0, 16

    );

		switch( $m ) {
			
      case 'encrypt': 

				return self::encrypt( $d, self::ALGORITHM, $k, 0, $v );
				
				break;

			case 'decrypt':

				return self::decrypt( $d, self::ALGORITHM, $k, 0, $v );
			
				break;
		
    }

	}

  // Encrypt 
  protected static function encrypt( string $d, string $m, string $k, int $n, string $v ): string {

    return base64_encode( 

      openssl_encrypt( $d, $m, $k, $n, $v )

    );

  } 

  // Decrypt
  protected static function decrypt( string $d, string $m, string $k, int $n, string $v ): string {

    return openssl_decrypt(

      base64_decode( $d ), $m, $k, $n, $v

    );

  }

}

?>
