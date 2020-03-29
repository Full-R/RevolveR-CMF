<?php

$form_parameters = [

	// Main parameters
	'id' 	  => 'comment-add-form',
	'class'	  => 'revolver__comment-add-form revolver__new-fetch',
	'action'  => '/comments-d/',
	'method'  => 'post',
	'encrypt' => true,
	'captcha' => true,
	'submit'  => 'Submit',

	// Include fieldsets
	'fieldsets' => [

		// fieldset contents parameters
		'fieldset_1' => [

			// wrap fields into label
			'labels' => [

				'label_1' => [

					'title'  =>  'Name',
					'access' => 'comment',
					'auth'	 => 0,

					'fields' => [

						0 => [

							'type' 			=> 'input:text',
							'name' 			=> 'revolver_comment_user_name',
							'placeholder'	=> 'Type your name',
							'required'		=> true

						],

					],

				],

				'label_2' => [

					'title'  =>  'Email',
					'access' => 'comment',
					'auth'	 => 0,

					'fields' => [

						0 => [

							'type' 			=> 'input:email',
							'name' 			=> 'revolver_comment_user_email',
							'placeholder'	=> 'Type your email here',
							'required'		=> true

						],

					],

				],

				'label_3' => [

					'title'  =>  'Name',
					'no-label' => true,
					'access' => 'comment',
					'auth'   => 'all',

					'fields' => [

						1 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_node_id',
							'value'			=> preg_replace("/[^0-9]/", '', $n['id'])

						],

						2 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_comment_user_id',
							'value'			=> USER['id']

						],

						3 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_comment_time',
							'value'			=> date('d.m.Y H:i')

						],

						4 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_comment_node_author',
							'value'			=> $n['author']

						],

						5 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_comment_node_route',
							'value'			=> $n['route']

						],

						6 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_comment_node_country',
							'value'			=> $n['language']['cipher']

						],

					],

				],

				'label_4' => [

					'title'  => 'Comment',
					'access' => 'comment',
					'auth'	 => 'all',

					'fields' => [

						0 => [

							'type' 			=> 'textarea:text',
							'name' 			=> 'revolver_comment_content',
							'placeholder'	=> 'Comment contents',
							'required'		=> true,
							'rows'			=> 10

						],

					],

				],

				'label_5' => [

					'title'  =>  'Subscribe',
					'access' => 'comment',
					'auth'	 => 'all',

					'fields' => [

						0 => [

							'type' 			=> 'input:checkbox:unchecked', //( (int)$n['subscription'] > 0 ? 'checked' : 'unchecked' ),
							'name' 			=> 'revolver_comment_subscription',
							'value'			=> ''

						],

					],

				],

				'label_6' => [

					'title'  =>  'Name',
					'no-label' => true,
					'access'   => 'comment',
					'auth'     => 1,

					'fields' => [

						0 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_comment_user_name',
							'value'			=> USER['name']

						],

						1 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_comment_user_email',
							'value'			=> USER['email']

						],

					],

				],

			],

		],

	],

];

if( FORM_ACCESS === 'none' || (bool)FORM_ACCESS['permissions']['auth'] ) {

	$form_parameters['fieldsets']['fieldset_1']['title'] = 'Add a comment as guest';

}
else {

	$form_parameters['fieldsets']['fieldset_1']['title:html'] = TRANSLATIONS[ $ipl ]['Add a comment as'] .' '. USER['name'];

}

$render_node .= '<div class="revolver__comments_add">';

$render_node .= $form::build( $form_parameters );

$render_node .= '</div>';

?>
