<?php

if( PASS[ 1 ] === 'comment' && PASS[ 3 ] === 'edit' ) {

	if( in_array( ACCESS['role'], ['Admin', 'Writer', 'User'], true ) ) { 

		$cid = PASS[ 2 ];

		$comment = iterator_to_array(

				$model::get( 'node->comment', [

					'criterion' => 'comments::id::'. $cid

				]),

			)['model::node->comment'][0];

		$form_parameters = [

			// main parameters
			'id'		=> 'comment-edit-form',
			'class'		=> 'revolver__comment-edit-form revolver__new-fetch',
			'action'	=> '/comments-d/',
			'method'	=> 'post',
			'encrypt'	=> true,
			'captcha'	=> true,	
			'submit'	=> 'Submit',

			// included fieldsets
			'fieldsets' => [

				// fieldset contents parameters
				'fieldset_1' => [

					'title:html' => TRANSLATIONS[ $ipl ]['Edit comment'] .' &#8226; '. $comment['comments']['id'] .' '. TRANSLATIONS[ $ipl ]['by'] .' '. $comment['comments']['user_name'],

					// wrap fields into label
					'labels' => [

						'label_1' => [

							'title'  => 'Comment',
							'access' => 'comment',
							'auth'	 => 1,

							'fields' => [

								0 => [

									'type' 			=> 'textarea:text',
									'name' 			=> 'revolver_comment_content',
									'placeholder'	=> 'Comment contents',
									'required'		=> true,
									'rows'			=> 5,

									'value:html' 	=> $markup::Markup( 

										html_entity_decode(

											htmlspecialchars_decode(

												$comment['comments']['content']

											)

										), [ 'xhash' => 0 ]

									),

								],

							],

						],

						'label_4' => [

							'title'		=> 'Name',
							'no-label'	=> true,
							'access'	=> 'comment',
							'auth'		=> 1,

							'fields' => [

								1 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_node_id',
									'required'		=> true,
									'value'			=> $comment['comments']['node_id']

								],

								2 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_comment_user_id',
									'required'		=> true,
									'value'			=> $comment['comments']['user_id']

								],

								3 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_comment_time',
									'required'		=> true,
									'value'			=> date('d.m.Y H:i')

								],

								4 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_comment_id',
									'required'		=> true,
									'value'			=> $cid

								],

								5 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_comment_user_name',
									'required'		=> true,
									'value'			=> $comment['comments']['user_name']

								],

								6 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_comment_node_route',
									'required'		=> true,
									'value'			=> $comment['nodes']['route']

								],

								7 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_comments_action_edit',
									'required'		=> true,
									'value'			=> 1,

								],

							],

						],

						'label_6' => [

							'title'  => 'Published',
							'access' => 'comment',
							'auth'	 => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:checkbox:'. ( (bool)$comment['comments']['published'] ? 'checked' : 'unchecked' ),
									'name' 			=> 'revolver_comments_published',
									'value'			=> 1

								],

							],

						],

						'label_7' => [

							'title'  => 'Delete comment',
							'access' => 'comment',
							'auth'	 => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:checkbox:unchecked',
									'name' 			=> 'revolver_comments_action_delete',
									'value'			=> 1

								],

							],

						],

					],

				],

			]

		];

		$render_node .= '<article class="revolver__article">';
		$render_node .= '<header class="revolver__article-header">'; 
		$render_node .= '<h2>'. TRANSLATIONS[ $ipl ]['Edit comment'] .' '. TRANSLATIONS[ $ipl ]['by'] .' '. $comment['comments']['user_name'] .'</h2>';

		$render_node .= '<time datetime="2019-12-31T19:20">'. $comment['comments']['time'] .'</time>';
		$render_node .= '</header>';

		$render_node .= $form::build( $form_parameters );

		$render_node .= '</article>';

	}

}

?>
