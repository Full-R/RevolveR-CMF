<?php

 /* 
  * 
  * RevolveR Authentification
  *
  * v.1.9.0
  *
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

final class Auth {

  protected static $model;

  protected static $cipher;

  protected function __clone() {

  }

  function __construct( Model $model, Cipher $cipher ) {

    self::$model  = $model;
    self::$cipher = $cipher;

  }

  public static function login( string $token, string $id ): void {

    $te = explode( '|', $token );

    if( isset($te[0]) && isset($te[1]) && isset($te[2]) ) {

      if( !in_array(session_status(), [ PHP_SESSION_DISABLED, PHP_SESSION_NONE ], true) ) { 

        session_destroy();

      }

      self::setCookie([

        [ 'usertoken', self::$cipher::crypt('encrypt', $token), time() + 86400, '/' ],

        [ 'accepted-privacy-policy', 'accepted', time() + 86400, '/' ],

        [ 'authorization', 1, time() + 86400, '/' ]

      ]);

      session_start();

      session_regenerate_id(true);

      $session = md5( uniqid() .'|'. $token[0] .'|'. $token[1] .'|'. $token[2] );

      $_SESSION['session_token'] = $session;

      if( is_numeric($id) ) {

        self::$model::set('users', [

          'id'          => $id,
          'session_id'  => $session,
          'criterion'   => 'id'

        ]);

      }

      self::setCookie([

        [ 'accepted-privacy-policy', session_id(), time() + 86400, '/' ]

      ]);

    }

  }

  public static function logout(): void {

    session_destroy(); 

    if( (bool)count( SV['c'] ) ) {

      foreach( SV['c'] as $n => $v ) {

        self::setCookie([

          [ $n, null, -1, '/' ]

        ]);

      }

    } 

  }

  private static function constructCookie( iterable $c ): string {

    $s = 'Set-Cookie: '. $c[0] .'='. rawurlencode( $c[1] ) .'; Expires='. date('D, d M Y H:i:s', $c[2]) . 'GMT' .'; Path='. $c[3] .'; Domain='. $_SERVER['HTTP_HOST'] .';';

    if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (int)$_SERVER['SERVER_PORT'] === 443 ) {

      $s .= ' SameSite=Strict; Secure; httpOnly;';

    } 

    return $s;

  }

  public static function setCookie( iterable $d ): void {

    if( isset($d[0]) ) {

      foreach( $d as $dc ) {

        if( is_array($dc) ) {

          header(

            self::constructCookie($dc), false

          );

        }

      }

    }

  }

}

?>
