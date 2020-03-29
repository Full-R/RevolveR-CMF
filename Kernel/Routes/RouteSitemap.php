<?php

 /*
  * 
  * Sitemap Route :: Generate sitemap
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

$sitemap = '<?xml version="1.0" encoding="UTF-8" ?>'. "\n";
$sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'. "\n";

foreach( iterator_to_array(

		$model::get( 'nodes', [

			'criterion' => 'id::*',

			'bound'		=> [

				0,   // limit

			],

			'course'	=> 'backward', // backward
			'sort' 		=> 'time',

		]),

	)['model::nodes'] as $node => $n) {

  $sitemap .= ' <url>'. "\n";

    $sitemap .= '<loc>'. site_host . $n['route'] .'</loc>'. "\n";
    $sitemap .= '<lastmod>'. date(

    	DATE_RFC822, strtotime( $n['time'] )

    ) .'</lastmod>'. "\n";

    $sitemap .= '<changefreq>monthly</changefreq>'. "\n";
    $sitemap .= '<priority>.9</priority>'. "\n";

  $sitemap .= ' </url>'. "\n\n";

}

$sitemap .= '</urlset>';

print $sitemap;

define('serviceOutput', [

  'ctype'     => 'application/xml',
  'route'     => '/search/'

]);

?>
