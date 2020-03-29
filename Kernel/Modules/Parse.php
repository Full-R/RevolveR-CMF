<?php

 /*
  * 
  * RevolveR Markup Parser
  *
  * v.1.8.0
  *
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
  */

final class Parse {

  protected function __clone() {

  }

  public static function extract( ?string $common = null ): iterable {

    if( $common ) {

      preg_match_all('/<([\w]+)([^>]*?)(([\s]*\/>)|(>((([^<]*?|<\!\-\-.*?\-\->)|(?R))*)<\/\\1[\s]*>))/mi', $common, $matches, PREG_OFFSET_CAPTURE);

      $i = 0;

      foreach ($matches[0] as $key => $match) {

        $child_nodes = null;

        if( isset($matches[6][$key][0]) ) {

          if( preg_match('/<([\w]+)([^>]*?)([\s]*\>)/mi', $matches[6][$key][0] ) ) {

            $child_nodes = iterator_to_array( self::extract($matches[6][$key][0]) );

          }

        }

        $attrs = trim($matches[2][$key][0]);

        $currentNode = [

          'omittag'     => $matches[4][$key][1] > -1 ? 1 : 0,
          'tagname'     => $matches[1][$key][0],
          'pointer'     => $match[1],
          'common'      => $match[0],
          'explode'     => self::explode( $match[0] ),

        ];

        if( !empty($attrs) ) {

          $currentNode['attrs'] = $attrs;

        }

        if( !empty($matches[6][$key][0]) ) {

          $currentNode['markup'] = $matches[6][$key][0];
          $currentNode['string'] = strip_tags( $matches[6][$key][0] ); 

        }

        if( $child_nodes ) {

          $currentNode['child'] = $child_nodes; 

        }

        yield $i++ => $currentNode;

      }

    }
    else {

      yield 0 => null;

    }

  }

  private static function explode(string $common): ?iterable {

    return preg_split('/(<[^>]*[^\/]>)/i', $common, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

  }

}

?>
