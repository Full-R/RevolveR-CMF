<?php
 
 /*
  * 
  * RevolveR Notifications class
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

final class Notifications {

  public static $notifications = [];

  public static $lang;

  protected function __clone() {

  }

  function __construct( string $lang = 'EN' ) {

    self::$lang = $lang;

  }

  public static function get(): ?iterable {

    return self::$notifications;

  }

  public static function Conclude(): string {

    $result = '';

    if( (bool)count(self::$notifications) ) {

      $c = 0;

      $duplicate = [];

      foreach( self::$notifications as $s => $m ) {

        $result .= '<div class="revolver__status-notifications revolver__'. $s .'">';

        $result .= '<div class="revolver__statuses-heading">... '. ucfirst($s) .'<i>+</i></div>';

        foreach( $m as $t ) {

          if( !isset( $duplicate[ $t ] ) ) {

            if( $s !== 'applied' && $s !== 'status' ) {

              //$result .= '<span class="small-number">'. ++$c .'.</span> ';

            }

            $result .= $t .'<br />';

            $duplicate[ $t ] = true;

          }

        }

        $result .= '</div>';

      }

    }

    return $result;

  }

  public static function set( string $type = 'status', string $notify, ?bool $translation = true ): void {

    if( $translation ) {

      if(defined('ROUTE')) {

        if( isset(ROUTE['ext'] ) ) {

          if( ROUTE['ext'] ) {

            $translate = etranslations;

          }

        }
        else {

          $translate = TRANSLATIONS;

        }

      }
      else {

        $translate = TRANSLATIONS;

      }

    }

    self::$notifications[ $type ][] = $translation ? $translate[ self::$lang ][ $notify ] .'.' : $notify;

  }

}
