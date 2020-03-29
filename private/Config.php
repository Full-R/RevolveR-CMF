<?php

 /*
  * 
  * RevolveR Kernel configuration
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
  */

// Website defaults
$name  = 'New website';
$title = 'Homepage';
$descr = 'Homepage index';

// Nodes per page 
$nodes_per_page = 5;

// Kernel scripts
define('scripts', array_merge([

		[

			'path' => '/Interface/revolver.js',
			'name' => 'revolver kernel',
			'part' => 'kernel',
			'alg'  => 512,
			'min'  => 1

		],

		[

			'path' => '/Interface/interface.js',
			'name' => 'revolver interface',
			'part' => 'module',
			'alg'  => 384,
			'min'  => 1

		]

	], $extensionsScripts )

);

// Interface styles
define('styles', array_merge([

		[

			'path'  => '/Interface/revolver.css',
			'name'  => 'revolver kernel',
			'part'  => 'kernel',
			'alg'   => 256,
			'min'   => 1,
			'defer' => 1

		],

		[

			'path'  => '/Interface/revolver-interface.css',
			'name'  => 'revolver interface',
			'part'  => 'kernel',
			'alg'   => 256,
			'min'   => 1,
			'defer' => 1

		],

		[

			'path'  => '/Interface/state-attribution.css',
			'name'  => 'revolver interface',
			'part'  => 'kernel',
			'alg'   => 256,
			'min'   => 1,
			'defer' => 0

		],

		[

			'path'  => '/Interface/interface-icons.css',
			'name'  => 'revolver interface',
			'part'  => 'kernel',
			'alg'   => 256,
			'min'   => 1,
			'defer' => 0

		]

	], $extensionsStyles )

);

/* Main framework routing */
define('main_nodes', array_merge([

		// Homepage
		TRANSLATIONS[ $ipl ]['Home'] => [

			'title' => TRANSLATIONS[ $ipl ]['Home'],
			'route' => '/',
			'node'	=> '#homepage',
			'id'	=> 0,

			'param_check' => [

				'menu' => 1

			],

			'type'	=> 'node'

		],

		// Create node
		TRANSLATIONS[ $ipl ]['Create'] => [

			'title' => TRANSLATIONS[ $ipl ]['Create node'],
			'route' => '/node/create/',
			'node'  => '#create',
			'id'	=> 'create',

			'param_check' => [

				'auth'     => 1,
				'menu'     => 1,
				'isAdmin'  => 1,
				'isWriter' => 1

			],

			'type'	=> 'node',

		],

		// Create node
		TRANSLATIONS[ $ipl ]['Moderation'] => [

			'title' => TRANSLATIONS[ $ipl ]['Moderation'],
			'route' => '/moderation/',
			'node'  => '#moderation',
			'id'	=> 'moderation',

			'param_check' => [

				'auth'     => 1,
				'menu'     => 1,
				'isAdmin'  => 1,
				'isWriter' => 1

			],

			'type'	=> 'node',

		],

		// User profile
		TRANSLATIONS[ $ipl ]['User'] => [

			'title' => TRANSLATIONS[ $ipl ]['Account profile'],
			'descr'	=> 'User area',
			'rel'	=> 'bookmark',

			'param_check' => [

				'auth' => 1,
				'menu' => 1

			],

			'route' => '/user/',
			'node'  => '#user',
			'type'	=> 'node',
			'id'	=> 1

		],

		// Messages
		TRANSLATIONS[ $ipl ]['Messages'] => [

			'title' => TRANSLATIONS[ $ipl ]['Private Messages'],
			'rel'	=> 'bookmark',

			'param_check' => [

				'auth' 	=> 1,
				'menu' 	=> 1

			],

			'route' => '/user/messages/',
			'node'  => '#user',
			'type'	=> 'node',
			'id'	=> 'messages'

		],

		// Authorization
		TRANSLATIONS[ $ipl ]['Login'] => [

			'title' => TRANSLATIONS[ $ipl ]['Login'],
			'descr'	=> 'User login area',

			'param_check' => [

				'installed' => 1,
				'auth' 		=> 0,
				'menu' 		=> 1

			],

			'route' => '/user/auth/',
			'node'  => '#user',
			'type'	=> 'node',
			'id'	=> 2

		],

		// Registration
		TRANSLATIONS[ $ipl ]['Register'] => [

			'title' => TRANSLATIONS[ $ipl ]['Account registration'],
			'descr'	=> 'User register area',

			'param_check' => [

				'installed' => 1,
				'auth' 		=> 0,
				'menu' 		=> 1

			],

			'route' => '/user/register/',
			'node'  => '#user',
			'type'	=> 'node',
			'id'	=> 3

		],

		// Recovery account
		TRANSLATIONS[ $ipl ]['Recovery'] => [

			'title' => TRANSLATIONS[ $ipl ]['Account recovery'],
			'descr'	=> 'User recovery area',

			'param_check' => [

				'installed' => 1,
				'auth' 		=> 0,
				'menu' 		=> 1

			],

			'route' => '/user/recovery/',
			'node'  => '#user',
			'type'	=> 'node',
			'id'	=> 4

		],

		// Dashboard
		TRANSLATIONS[ $ipl ]['Dashboard'] => [

			'title' => TRANSLATIONS[ $ipl ]['Dashboard'],

			'param_check' => [

				'isAdmin' => 1,
				'auth'    => 1,
				'menu'    => 1

			],

			'route'	=> '/dashboard/',
			'node' 	=> '#dashboard',
			'type'	=> 'node',
			'id'  	=> 5

		],

		// Dashboard
		TRANSLATIONS[ $ipl ]['Terminal'] => [

			'title' => TRANSLATIONS[ $ipl ]['Terminal'],

			'param_check' => [

				'installed' => 1,
				'isAdmin'	=> 1,
				'auth'		=> 1,
				'menu'		=> 0,
				'hidden'	=> 1

			],

			'route'		=> '/terminal/',
			'node'		=> '#terminal',
			'id'		=> 'terminal',
			'type'		=> 'node',

		],

		// Attendance
		TRANSLATIONS[ $ipl ]['Attendance'] => [

			'title' => TRANSLATIONS[ $ipl ]['Attendance'],

			'param_check' => [

				'installed' => 1,
				'isAdmin'   => 1,
				'auth'      => 1,
				'menu'      => 1

			],

			'route'	=> '/attendance/',
			'node' 	=> '#attendance',
			'id'   	=> 'attendance',
			'type'	=> 'node',

		],

		// Categories
		TRANSLATIONS[ $ipl ]['Categories'] => [

			'title' => TRANSLATIONS[ $ipl ]['Categories'],
			'descr'	=> 'Categories',

			'param_check' => [

				'installed' => 1,
				'menu'      => 1

			],

			'route'	=> '/categories/',
			'node' 	=> '#categories',
			'type'	=> 'node',
			'id'   	=> 'categories'

		],

		// Privacy notes
		TRANSLATIONS[ $ipl ]['Privacy notes'] => [

			'title' => TRANSLATIONS[ $ipl ]['Privacy notes'],
			'descr'	=> 'Privacy notes',

			'param_check' => [

				'menu' 		=> 0,
				'hidden' 	=> 1

			],

			'route'	=> '/privacy/',
			'node' 	=> '#privacy',
			'type'	=> 'node',
			'id'   	=> 'privacy-notes',


		],

		// Logout
		TRANSLATIONS[ $ipl ]['Logout'] => [

			'title' => TRANSLATIONS[ $ipl ]['Account logout'],

			'param_check' => [

				'installed' => 1,
				'auth' 		=> 1,
				'menu' 		=> 1

			],

			'route' => '/logout/',
			'geta'  => 'notification=soon',
			'node'  => '#user',
			'type'	=> 'node',
			'id'	=> 6

		],

		/* Service routes */

		// Terminal service
		'terminal-s' => [

			'title' => 'Terminal service',

			'param_check' => [

				'menu'		=> 0,
				'hidden'	=> 1

			],

			'route'  => '/terminal-s/',
			'node'   => '#terminal-s',
			'type'	 => 'service',
			'id'	 => 'terminal-s',

		],

		/* Dispatch routes */

		// Comments dispatch
		'comments-d' => [

			'title' => 'Comment dispatch service',

			'param_check' => [

				'menu'		=> 0,
				'hidden'	=> 1

			],

			'route'  => '/comments-d/',
			'node'   => '#comments-d',
			'type'	 => 'service',
			'id'	 => 'comments-d',

		],

		// Contents dispatch
		'contents-d' => [

			'title' => 'Contents dispatch service',

			'param_check' => [

				'menu'		=> 0,
				'hidden'	=> 1

			],

			'route'  => '/contents-d/',
			'node'   => '#contents-d',
			'type'	 => 'service',
			'id'	 => 'contents-d',

		],

		// Category dispatch
		'category-d' => [

			'title' => 'Category dispatch service',

			'param_check' => [

				'menu'		=> 0,
				'hidden'	=> 1

			],

			'route'  => '/category-d/',
			'node'   => '#category-d',
			'type'	 => 'service',
			'id'	 => 'category-d',

		],

		// Category dispatch
		'user-d' => [

			'title' => 'User dispatch service',

			'param_check' => [

				'menu'		=> 0,
				'hidden'	=> 1

			],

			'route'  => '/user-d/',
			'node'   => '#user-d',
			'type'	 => 'service',
			'id'	 => 'user-d',

		],

		/* Service routes */

		// secure service
		'secure' => [

			'title' => 'Secure service',

			'param_check' => [

				'menu'		=> 0,
				'hidden' 	=> 1

			],

			'route'  => '/secure/',
			'node'   => '#secure',
			'type'	 => 'service',
			'id'	 => 'secure',

		],

		// search service
		'search' => [

			'title' => 'Search service',

			'param_check' => [

				'menu'		=> 0,
				'hidden' 	=> 1

			],

			'route'  => '/search/',
			'node'   => '#search',
			'type'	 => 'service',
			'id'	 => 'search',

		],


		// preview service
		'preview' => [

			'title' => 'Preview service',

			'param_check' => [

				'menu' 		=> 0,
				'hidden'	=> 1

			],

			'route'  => '/preview/',
			'node'   => '#preview',
			'type'	 => 'preview',
			'id'	 => 'preview',

		],

		// sitemap service
		'sitemap' => [

			'title' => 'Sitemap service',

			'param_check' => [

				'menu' 		=> 0,
				'hidden' 	=> 1

			],

			'route'  => '/sitemap/',
			'node'   => '#sitemap',
			'type'	 => 'service',
			'id'	 => 'sitemap',

		],

		// Recent contents aggregator service
		'aggregator' => [

			'title' => 'Aggregator service',

			'param_check' => [

				'menu' 		=> 0,
				'hidden' 	=> 1

			],

			'route'  => '/aggregator/',
			'node'   => '#aggregator',
			'type'	 => 'service',
			'id'	 => 'aggregator',

		],

		'install' => [

			'title' => 'RevolveR CMF Setup',

			'param_check' => [

				'auth' => 0,
				'menu' => 0

			],

			'route' => '/setup/',
			'node'	=> '#setup',
			'type'	=> 'node',
			'id'	=> 'setup'

		]

	], $extensionsRoutes )

);

?>
