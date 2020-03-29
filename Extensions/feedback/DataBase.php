<?php

/**
  *
  * Extension :: Feedback Data Base Structure
  *
  * v.1.9.0
  *
  */

$STRUCT_FEEDBACK = [

	'field_id' => [

		'type'   => 'bignum', // bigint
		'auto'	 => true,
		'length' => 255,
		'fill'   => true

	],

	'field_message_hash' => [

		'type'   => 'text', // varchar
		'length' => 100,
		'fill'   => true

	],


	'field_message_processed' => [

		'type'   => 'minnum', // smallint
		'length' => 1,
		'fill'   => true

	],

	'field_message_title' => [

		'type'   => 'text', // varchar
		'length' => 150,
		'fill'   => true,

		'index'	 => [

			'type' => 'simple'

		]

	],

	'field_message_text' => [

		'type'   => 'text', // varchar
		'length' => 10000,
		'fill'   => true,

		'index'	 => [

			'type' => 'full'

		]

	],

	'field_message_time' => [

		'type'   => 'text', // varchar
		'length' => 20,
		'fill'   => true

	],

	'field_sender_name' => [

		'type'   => 'text', // varchar
		'length' => 150,
		'fill'   => true,

		'index'	 => [

			'type' => 'simple'

		]


	],

	'field_sender_email' => [

		'type'   => 'text', // varchar
		'length' => 80,
		'fill'   => true,

		'index'	 => [

			'type' => 'simple'

		]

	],

	'field_sender_phone' => [

		'type'   => 'text', // varchar
		'length' => 15,
		'fill'   => null

	]

];

$STRUCT_FEEDBACK_FILES = [

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

	'field_message_hash' => [

		'type'		=> 'text', // varchar
		'length'	=> 100,
		'fill'		=> null

	]

];

// Compile DBX Extension Schema
$DBX_KERNEL_SCHEMA['feedback'] = $STRUCT_FEEDBACK;
$DBX_KERNEL_SCHEMA['feedback_files'] = $STRUCT_FEEDBACK_FILES;

?>
