<?php

/**
  *
  * Extension :: Conversations
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
$extensionsRoutes[ etranslations[ $ipl ]['Conversations'] ] = [

	'title' => etranslations[ $ipl ]['Conversations'],
	'descr'	=> etranslations[ $ipl ]['Conversations hub'],

	'param_check'	=> [

		'installed'	=> 1,
		'auth'		=> 0,
		'menu'		=> 1

	],

	'route'	=> '/conversations/',
	'node'	=> '#conversations',
	'type'	=> 'node',
	'id'	=> 'conversations',
	'ext'	=> 1

];

?>
