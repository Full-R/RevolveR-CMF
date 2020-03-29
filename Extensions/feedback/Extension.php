<?php

/**
  *
  * Extension :: Feedback
  *
  * v.1.9.0
  *
  */

/*
$extensionsScripts[] = [

		'path' => '/Interface/feedback.js',
		'name' => 'revolver feedback',
		'part' => 'module',
		'alg'  => 256,
		'min'  => 1

];

$extensionsStyles[] = [

		'path'  => '/Interface/feedback.css',
		'name'  => 'revolver feedback',
		'part'  => 'module',
		'alg'   => 256,
		'min'   => 1,
		'defer' => 1

];
*/

// Feedback routing
$extensionsRoutes[ etranslations[ $ipl ]['Feedback'] ] = [

	'title' => etranslations[ $ipl ]['Request feedback'],
	'descr'	=> etranslations[ $ipl ]['Contact to feedback'],

	'param_check'	=> [

		'installed'	=> 1,
		'auth'		=> 0,
		'menu'		=> 1

	],

	'route'	=> '/feedback/',
	'node'	=> '#feedback',
	'type'	=> 'node',
	'id'	=> 'feedback',
	'ext'	=> 1

];

?>
