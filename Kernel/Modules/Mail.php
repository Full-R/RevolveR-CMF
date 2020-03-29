<?php
 
 /*
  * 
  * RevolveR eMail class
  *
  * v.1.8.0
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

final class eMail {

  protected static $escape;
  protected static $parse;
  protected static $from;

  public static $status = null;

  function __construct() {

    self::$escape = new Markup();
    self::$parse  = new Parse();
    self::$from   = default_email;

  }

  protected function __clone() {

  }

  public static function send( string $to, string $subject, string $message, ?iterable $attachments = null ): void {

    // Unique parts boundaries
    $main_m = 'RevolveR_message_parts_'.  md5(str_replace(' ', '', date('l jS \of F Y h i s A')));

    // Sign
    $sign  = '<p style="color: #b00000;">King regards,</p>'; 
    $sign .= '<p style="color: #500050;"><a href="'. site_host .'" title="Go to '. BRAND .' website" target="_blank">'. BRAND .'</a> eMail agent.</p>';

    if( self::isEmail(self::$from) && self::isEmail($to) ) {

      $headers  = 'MIME-Version: 1.0' ."\n";

      $headers .= 'Date: '. date('d.m.Y h:i:s') ."\n";
      $headers .= 'Importance: High' ."\n";

      $headers .= 'Return-Path: =?utf-8?B?'. self::encode(self::$from) .'?='. "\n";

      $headers .= 'From: =?utf-8?B?'. self::encode(BRAND) .'?= <'. self::$from .">\n";
      $headers .= 'To: =?utf-8?B?'. self::encode('Subscriber') .'?= <'. $to .">\n";

      $headers .= 'Content-Type: multipart/related; boundary="'. $main_m .'"' ."\n\n"; 

      // Start parts
      $html_template .= '--'. $main_m ."\n";
      $html_template .= 'Content-type: text/html; charset=utf-8' ."\n";
      $html_template .= 'Content-Transfer-Encoding: 8bit' ."\n";
      $html_template .= 'Content-Disposition: inline'. "\n\n";

      $html_template .= '<!DOCTYPE html>' ."\n";
      $html_template .= '<html>' ."\n"; 
      $html_template .= '<head>' ."\n";

      $html_template .= '<meta charset="utf-8" />' ."\n";
      $html_template .= '<title>'. BRAND .'</title>' ."\n";

      $html_template .= '</head>' ."\n";
      $html_template .= '<body>' ."\n";

      $html_template .= self::stringifyLimiter($message . $sign) ."\n";

      $html_template .= '</body>' ."\n";
      $html_template .= '</html>' ."\n";

      // Attachements
      if( is_array($attachments) ) {

      foreach( $attachments as $a ) {

        $file = file_get_contents($a, true);

          if( $file ) {

            foreach( allowed_uploads as $attachement ) {

              if( pathinfo(basename($a), PATHINFO_EXTENSION) === $attachement['extension'] ) {

                // Start attachements parts
                $html_template .= '--'. $main_m ."\n";
                $html_template .= 'Content-Type: application/octet-stream; name='. basename($a) ."\n"; 
                $html_template .= 'Content-Description: '. basename($a) ."\n";
                $html_template .= 'Content-Transfer-Encoding: base64' . "\n";
                $html_template .= 'Content-Disposition: attachment; size='. strlen($file) .'; filename='. basename($a) ."\n\n";
                $html_template .= chunk_split(base64_encode($file), 68) ."\n";

              }

            }

          } 

        }

      }

      // End of parts
      $html_template .= '--'. $main_m .'--';

      self::$status = mail($to, '=?utf-8?B?'. self::encode($subject) .'?=', $html_template, $headers, '-f '. self::$from);

    }

  }

  protected static function stringifyLimiter( string $message ): string {

    $chunks = '';

    foreach( iterator_to_array(self::$parse::extract( self::$escape::Markup($message, [ 'xhash' => 1 ] ))) as $nx ) {

      foreach( $nx['explode'] as $ex ) {

        if( preg_match_all('/<([\w]+)([^>]*?)*>/mi', $ex, $m) ) {

          $exp = iterator_to_array( self::$parse::extract($ex . '</'. $m[1][0] .'>') );

          $chunks .= '<'. $exp[0]['tagname'] ."\n";

          if( isset($exp[0]['attrs']) ) {

            $segments = preg_replace('/"(.+?)"/', "\"$1$2\"\n", $exp[0]['attrs']);

            foreach (explode("\n", $segments) as $sgm) {

              $s = explode('url(', trim($sgm));

              if( isset($s[1]) ) {

                $s[0]  = $s[0] ."\nurl(";

              }

              foreach( $s as $sgx ) {

                if( preg_match('/xhash/i', $sgx) ) {

                  $chunks .= 'data-xhash=' ."\n". explode('data-xhash=', $sgx )[1] ."\n";

                }
                else {

                  $chunk = str_split($sgx, 68);

                  $delim = isset($s[1]) ? "\\\n" : "\n";

                  $i = 0;

                  foreach( $chunk as $c ) {

                    if( ( count($chunk) - 1 !== $i ) ) {

                      $chunks .= trim($c) . $delim;

                    }
                    else {

                      $chunks .= trim($c) ."\n";

                    }

                    $i++;

                  }

                }

              }

            }

          }

          $chunks = rtrim(trim($chunks), '\\') . '>' ."\n";

        }
        else {

          foreach( str_split($ex, 69) as $sx ) {

            $chunks .= $sx ."\n"; 

          }

        }

      }

    }

    return $chunks;

  }

  protected static function encode( string $s ): string {

    $chunks = str_split( self::$escape::Markup( ltrim($s, '.'), [ 'xhash' => 0 ] ), 69);
    $result = '';

    foreach( $chunks as $c ) {

      $result .= $c ."\n";

    }

    return base64_encode( $result );

  }

  protected static function isEmail( string $email ): ?string {

    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;

  }

}

?>
