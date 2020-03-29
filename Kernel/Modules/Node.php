<?php

 /* 
  * 
  * RevolveR Node Class
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

final class Node {

  protected function __clone() {

  }

  public static function singleNode( iterable $nodes ): iterable {

    foreach( $nodes as $node => $fg ) {

      foreach( $fg as $k => $v ) {

        if( ROUTE['route'] === $v ) {

          $node_params = [

            'route'  => $v,                  // path
            'node'   => $fg['node'],         // node
            'id'     => $fg['id'],           // node id
            'title'  => $fg['title'],        // node title
            'params' => $fg['param_check']   // params

          ];

        }

      }

    }

    return self::nodePrepare($node_params);

  }

  private static function nodePrepare( iterable $node ): iterable {

    $node['canonical'] = (int)$node['params']['auth'] === (int)SV['c']['authorization'] ? site_host . $node['route'] : site_host;

    unset( $node['params']['auth'] );

    if( !empty($node['params']['isAdmin']) ) {

      $node['canonical'] = !(bool)USER['id'] && (bool)SV['c']['authorization'] ? site_host . $node['route'] : site_host; 

      unset( $node['params']['isAdmin'] );

    }

    unset( $node['params'] );

    return $node;

  }

}

?>
