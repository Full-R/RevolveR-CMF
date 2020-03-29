<?php

/**
  *
  * Extension :: Conversations Data Base Structure
  *
  * v.1.9.0
  *
  */

$STRUCT_CONVERSATIONS_HEADINGS = [

	'field_id' => [

		'type'		=> 'bignum', // bigint
		'auto'		=> true,
		'length'	=> 255,
		'fill'		=> true

	],

	'field_heading_hash' => [

		'type'		=> 'text', // varchar
		'length'	=> 100,
		'fill'		=> null

	],

	'field_user_id' => [

		'type'   => 'text', // varchar
		'length' => 150,
		'fill'   => true

	],

	'field_heading' => [

		'type'	 => 'text', // varchar
		'length' => 150,
		'fill'   => true,

		'index'	 => [

			'type' => 'simple'

		]

	],

	'field_heading_description' => [

		'type'	 => 'text', // varchar
		'length' => 500,
		'fill'   => true,

		'index'	 => [

			'type' => 'full'

		]

	],

	'field_heading_text' => [

		'type'   => 'text', // varchar
		'length' => 10000,
		'fill'   => true,

		'index'	 => [

			'type' => 'full'

		]

	],

	'field_heading_time' => [

		'type'   => 'text', // varchar
		'length' => 20,
		'fill'   => true

	]

];

$STRUCT_CONVERSATIONS_HEADING_FILES = [

	'field_id' => [

		'type'		=> 'bignum', // bigint
		'auto'		=> true,
		'length'	=> 255,
		'fill'		=> true

	],

	'field_file' => [

		'type'		=> 'text', // varchar
		'length'	=> 255,
		'fill'		=> true,

		'index'	 => [

			'type' => 'simple'

		]

	],

	'field_heading_hash' => [

		'type'		=> 'text', // varchar
		'length'	=> 100,
		'fill'		=> null

	]

];

$STRUCT_CONVERSATIONS_POSTS = [

	'field_id' => [

		'type'		=> 'bignum', // bigint
		'auto'		=> true,
		'length'	=> 255,
		'fill'		=> true

	],

	'field_post_hash' => [

		'type'		=> 'text', // varchar
		'length'	=> 100,
		'fill'		=> null

	],

	'field_user_id' => [

		'type'   => 'text', // varchar
		'length' => 150,
		'fill'   => true,

		'index'	 => [

			'type' => 'simple'

		]

	],

	'field_post_text' => [

		'type'   => 'text', // varchar
		'length' => 10000,
		'fill'   => true,

		'index'	 => [

			'type' => 'full'

		]

	],

	'field_post_time' => [

		'type'   => 'text', // varchar
		'length' => 20,
		'fill'   => true

	]

];

$STRUCT_CONVERSATIONS_POST_FILES = [

	'field_id' => [

		'type'		=> 'bignum', // bigint
		'auto'		=> true,
		'length'	=> 255,
		'fill'		=> true

	],

	'field_file' => [

		'type'		=> 'text', // varchar
		'length'	=> 255,
		'fill'		=> true,

		'index'	 => [

			'type' => 'simple'

		],

	],

	'field_post_hash' => [

		'type'		=> 'text', // varchar
		'length'	=> 100,
		'fill'		=> null

	]

];

// Compile DBX Extension Schema
$DBX_KERNEL_SCHEMA['conversations_headings'] = $STRUCT_CONVERSATIONS_HEADINGS;
$DBX_KERNEL_SCHEMA['conversations_headings_files'] = $STRUCT_CONVERSATIONS_HEADING_FILES;

$DBX_KERNEL_SCHEMA['conversations_post'] = $STRUCT_CONVERSATIONS_POSTS;
$DBX_KERNEL_SCHEMA['conversations_post_files'] = $STRUCT_CONVERSATIONS_POST_FILES;

?>
