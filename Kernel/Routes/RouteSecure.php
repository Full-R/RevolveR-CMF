<?php

 /*
  * 
  * Secure Route :: Generates Captcha
  * 
  * v.1.9.0
  *
  *
  *
  *
  *               ^
  *              | |
  *            @#####@
  *          (###   ###)-.
  *        .(###     ###) \
  *       /  (###   ###)   )
  *      (=-  .@#####@|_--"
  *      /\    \_|l|_/ (\
  *     (=-\     |l|    /
  *      \  \.___|l|___/
  *      /\      |_|   /
  *     (=-\._________/\
  *      \             /
  *        \._________/
  *          #  ----  #
  *          #   __   #
  *          \########/
  *
  *
  *
  * Developer: Dmitry Maltsev
  *
  * License: Apache 2.0
  *
  *
  */

$keys = json_encode(['key' => base64_encode('0*A-0:0:0|A-0:00:0|A-0:0:0|A-0:0:0|A-0:0:0|A-0:0:0|A-0:0:00|0-0:0:00|A-0:0:0|A-0:0:0|A-0:0:0|A-0:0:0|A-0:0:0|A-0:0:0|O-0:0:0|A-0:0:0*00000000000000000000')]);

if( isset( SV['g']['route'] ) ) {

  if( !empty($_SERVER['HTTP_REFERER']) && ( $_SERVER['HTTP_HOST'] === parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ) ) {

    $dsp = null;

    $psw = explode('/', SV['g']['route']['value'] );

    if( $psw[ 1 ] === 'terminal' ) {

      $dsp = '/terminal-s/';

    }

    if( $psw[ count($psw) - 2 ] === 'edit' ) {

      if( $psw[ 1 ] === 'comment' && is_numeric( $psw[ count($psw) - 3 ] ) ) {

        $dsp = '/comments-d/';

      }

      if( $psw[ 1 ] === 'categories' && is_numeric( $psw[ count($psw) - 3 ] ) ) {

        $dsp = '/category-d/';

      }

      if( !is_numeric( $psw[ count($psw) - 3 ] ) ) {

        $dsp = '/contents-d/';

      }

      if( $psw[ 1 ] === 'user' && is_numeric( $psw[ count($psw) - 3 ] ) ) {

        $dsp = '/user-d/';

      }

    }

    $keys = json_encode([

      'key' => $captcha::randomize(

          base64_encode( SV['g']['route']['value'] ), $dsp 

        )

      ]);

  }

}

if( !isset( SV['g']['policy'] ) ) {

  print $keys;

} 
else {

  if( isset(SV['c']['accepted-privacy-policy']) ) {

    if( SV['c']['accepted-privacy-policy'] === session_id() ) {

      print json_encode([

        'privacy' => base64_encode( 'accepted::'. session_id() )

      ]);

    }
    else {

      print json_encode([

        'privacy' => base64_encode('null')

      ]);

    }

  }
  else {

      print json_encode([

        'privacy' => base64_encode('null')

      ]);

  }

}

define('serviceOutput', [

  'ctype'     => 'application/json',
  'route'     => '/secure/'

]);

?>
