<?php

 /* 
  * 
  * RevolveR Captcha Class
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

final class Captcha {

  protected static $patterns = [

    '0:0:0|0:25:0|0:50:0|0:75:0|1:0:25|1:25:25|0:50:25|0:75:25|1:0:50|1:25:50|0:50:50|0:75:50|0:0:75|0:25:75|0:50:75|0:75:75',
    '0:0:0|0:25:0|0:50:0|0:75:0|0:0:25|1:25:25|0:50:25|0:75:25|0:0:50|1:25:50|1:50:50|0:75:50|0:0:75|0:25:75|0:50:75|0:75:75',
    '0:0:0|0:25:0|0:50:0|0:75:0|0:0:25|1:25:25|1:50:25|0:75:25|0:0:50|1:25:50|1:50:50|0:75:50|0:0:75|0:25:75|0:50:75|0:75:75',
    '0:0:0|0:25:0|0:50:0|0:75:0|0:0:25|1:25:25|1:50:25|0:75:25|0:0:50|0:25:50|1:50:50|0:75:50|0:0:75|0:25:75|0:50:75|0:75:75',
    '0:0:0|0:25:0|0:50:0|1:75:0|0:0:25|1:25:25|0:50:25|0:75:25|0:0:50|0:25:50|1:50:50|0:75:50|1:0:75|0:25:75|0:50:75|0:75:75',
    '1:0:0|0:25:0|0:50:0|1:75:0|0:0:25|0:25:25|1:50:25|0:75:25|0:0:50|1:25:50|0:50:50|0:75:50|1:0:75|0:25:75|0:50:75|1:75:75',
    '1:0:0|0:25:0|0:50:0|1:75:0|0:0:25|1:25:25|1:50:25|0:75:25|0:0:50|1:25:50|1:50:50|0:75:50|1:0:75|0:25:75|0:50:75|1:75:75',
    '1:0:0|0:25:0|0:50:0|1:75:0|0:0:25|0:25:25|0:50:25|0:75:25|0:0:50|0:25:50|0:50:50|0:75:50|1:0:75|0:25:75|0:50:75|1:75:75',
    '1:0:0|0:25:0|0:50:0|1:75:0|0:0:25|0:25:25|0:50:25|0:75:25|0:0:50|0:25:50|0:50:50|0:75:50|1:0:75|0:25:75|0:50:75|1:75:75',
    '0:0:0|1:25:0|1:50:0|0:75:0|0:0:25|0:25:25|0:50:25|0:75:25|0:0:50|0:25:50|0:50:50|0:75:50|0:0:75|1:25:75|1:50:75|0:75:75',

  ];

  public static $random;

  public static $notify;

  public static $lang;

  function __construct( string $lang = 'EN', Notifications $notify ) {

    self::$notify = $notify;

    self::$lang = $lang;

  }

  protected function __clone() {

  }

  public static function randomize(string $route, ?string $dsp = null): string {

    self::$random = array_rand(self::$patterns, 1);

    $crypted = [];

    $result  = '';

    $c = 0;

    $symbols = [

      0  => 'A', 1  => 'B', 2  => 'C',
      3  => 'D', 4  => 'E', 5  => 'F',
      6  => 'G', 7  => 'H', 8  => 'I',
      9  => 'J', 10 => 'K', 11 => 'L',
      12 => 'M', 13 => 'N', 14 => 'O',
      15 => 'P'

    ];

    foreach( explode('|', self::$patterns[self::$random]) as $payload ) {

      $crypted[] = $symbols[ $c++ ] .'-'. $payload;

    }

    shuffle($crypted);

    foreach( $crypted as $payload ) {

      $result .= $payload .'|';

    }

    $session_hash = md5(self::$patterns[self::$random] .'#'. date('l dS of F Y h:i:s A'));

    self::setCookie('SecureHash', $session_hash, time() + 1800, ( $dsp ? $dsp : str_replace(['^', '|'], ['.', '/'], base64_decode($route) ) ) );

    return base64_encode( self::$random ).'*'. base64_encode(rtrim($result, '|')) .'*'. base64_encode($session_hash);

  }

  public static function setCookie(string $name, string $value, string $exp, string $path): void {

    $cst = 'Set-Cookie: '. $name .'='. rawurlencode($value) .'; Expires='. date('D, d M Y H:i:s', $exp) . 'GMT' .'; Path='. $path .'; Domain='. $_SERVER['HTTP_HOST'] .';';

    header( (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || (int)$_SERVER['SERVER_PORT'] === 443) ? $cst . ' SameSite=Strict; Secure; httpOnly;' : $cst .' httpOnly;') );

  }

  protected static function decode( string $str ): string {

    $decoded = '';

    for( $i = 0; $i < strlen($str); $i++ ) {

      $a = ord( $str[$i] ) ^ 51;

      $decoded .= chr( $a );

    }

    return $decoded;

  }

  private static function symbols( string $str ): string {

    $str = explode('|', $str);
    $res = '';

    foreach($str as $s) {

      $res .= chr( $s );

    }

    return $res;

  }

  public static function verify( string $hash_string ): ?bool {

    $captcha_stage_1 = explode('*', $hash_string);
    $captcha_stage_2 = self::decode(self::symbols(base64_decode($captcha_stage_1[0])));
    $captcha_stage_3 = explode('*', json_decode( $captcha_stage_2, true )['value'] );

    return self::confirm( $captcha_stage_3[1], $captcha_stage_3[0], $captcha_stage_1[1], $captcha_stage_1[2] );

  }

  private static function confirm( string $val, string $id, string $hash, string $route ): ?bool {

    if( !isset($_COOKIE['SecureHash']) || empty($hash) ) {

      self::$notify::set('notice', 'Security check not pass');

      return null;

    }

    $result = null;

    if( $val === self::$patterns[$id] && base64_decode($hash) === $_COOKIE['SecureHash'] ) {

      $result = true;

    }

    if( !$result ) {

      self::$notify::set('notice', 'Security check not pass');

    }

    return $result;

  }

}

?>
