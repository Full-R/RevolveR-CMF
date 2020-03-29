<?php

 /*
  * 
  * Aggregator Route :: Generate ATOM
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

$feed  = '<?xml version="1.0" encoding="UTF-8" ?>'. "\n";
$feed .= '<feed xmlns="http://www.w3.org/2005/Atom">'. "\n";

$feed .= ' <title>'. BRAND .'</title>'. "\n";
$feed .= ' <description>'. DESCRIPTION .'</description>'. "\n";
$feed .= ' <link href="'. site_host .'/aggregator/" rel="self" type="application/atom+xml" />'. "\n";
$feed .= ' <link>'. site_host .'/</link>'. "\n\n";

foreach( iterator_to_array(

    $model::get( 'nodes', [

      'criterion' => 'id::*',

      'bound'   => [

        20,   // limit

      ],

      'course'  => 'backward', // backward
      'sort'    => 'time',

    ]),

  )['model::nodes'] as $node => $n) {

  if( $n['published'] ) {

    $feed .= ' <entry>'. "\n";
    $feed .= '  <title>'.  $n['title'] .'</title>'. "\n";
    $feed .= '  <description>'. $n['description'] .'</description>'. "\n";
    $feed .= '  <link>'. site_host . $n['route'] .'</link>'. "\n";
    $feed .= '  <id>'. site_host . $n['route'] .'</id>'. "\n";

    $feed .= '  <updated>'. date(

      DATE_RFC822, strtotime(

        $n['time']

      )

    ) .'</updated>'. "\n";

    $feed .= ' </entry>'. "\n\n";

  }

}

$feed .= '</feed>'. "\n";

print $feed;

define('serviceOutput', [

  'ctype'     => 'application/atom+xml',
  'route'     => '/aggregator/'

]);

?>
