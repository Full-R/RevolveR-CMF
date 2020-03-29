<?php

 /*
  * 
  * RevolveR Kernel Extensions 
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
  */

// Empty stacks 
$extensionsScripts = []; 
$extensionsStyles  = [];
$extensionsRoutes  = [];

$extensions = [];

foreach( array_diff(

            scandir('./Extensions/', 1), [ '..', '.' ]

          ) as $e ) {

  if( is_readable('./Extensions/'. $e .'/Extension.php') ) {


    require_once('./Extensions/'. $e .'/Extension.php');

    $extensions[] = $e;

  }

}

// Register extension
define('REGISTERED_EXTENSIONS', $extensions);




?>
