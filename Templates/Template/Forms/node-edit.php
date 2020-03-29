<?php 

if( $nodes ) {

	$allow_delete = count($nodes) === 1 ? true : null;

}
else {

	$allow_delete = null;

}

$form_parameters_html_help  = '<ul class="revolver__allowed-files-description-table">';
$form_parameters_html_help .= '<li class="revolver__table-header">';
$form_parameters_html_help .= '<span class="revolver__allowed-files-description">'. TRANSLATIONS[ $ipl ]['File description'] .'</span>';
$form_parameters_html_help .= '<span class="revolver__allowed-files-extension">'. TRANSLATIONS[ $ipl ]['Extension'] .'</span>';
$form_parameters_html_help .= '<span class="revolver__allowed-files-size">'. TRANSLATIONS[ $ipl ]['Maximum allowed file size'] .'</span>';
$form_parameters_html_help .= '<li>';

foreach( $D::$file_descriptors as $allowed_files ) {

	$form_parameters_html_help .= '<li>';
	$form_parameters_html_help .= '<span class="revolver__allowed-files-description">'. $allowed_files['description'] .'</span>';
	$form_parameters_html_help .= '<span class="revolver__allowed-files-extension">'. $allowed_files['extension'] .'</span>';

	$form_parameters_html_help .= '<span class="revolver__allowed-files-size">'. round(

		(int)$allowed_files['max-size'] / 1024, 1, PHP_ROUND_HALF_ODD

	) .' Kb</span>';

	$form_parameters_html_help .= '</li>';

}

$form_parameters_html_help .= '</ul>';

// Node Edit Form Structure
$form_parameters = [

	// main parameters
	'id' 	  => 'node-create-form',
	'class'	  => 'revolver__node-create-form revolver__new-fetch',
	'enctype' => 'multipart/form-data',
	'action'  => '/contents-d/',
	'method'  => 'post',
	'encrypt' => true,
	'captcha' => true,
	'submit'  => 'Submit',

	// tabs
	'tabs' => [

		'tab_1' => [

			// tab title
			'title'  => 'Node editor',
			'active' => true,

			// included fieldsets
			'fieldsets' => [

				// fieldset contents parameters
				'fieldset_1' => [

					'title:html' => TRANSLATIONS[ $ipl ]['Editor'] .' &#8226; '. $n['id'],

					// wrap fields into label
					'labels' => [

						'label_1' => [

							'title'  => 'Node title',
							'access' => 'node',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:text',
									'name' 			=> 'revolver_node_edit_title',
									'placeholder'	=> 'Node title',
									'required'		=> true,
									'value'			=> $n['title']

								],

							],

						],

						'label_2' => [

							'title'  => 'Node description',
							'access' => 'node',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:text',
									'name' 			=> 'revolver_node_edit_description',
									'placeholder'   => 'Node description',
									'required'		=> true,
									'value'			=> $n['description']

								],

							],

						],

						'label_3' => [

							'title'  => 'Node route',
							'access' => 'node',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:text',
									'name' 			=> 'revolver_node_edit_route',
									'placeholder'	=> 'Node address',
									'required'		=> true,
									'readonly'		=> true,
									'value'			=> $n['route']

								],

							],

						],

						'label_4' => [

							'title'  => 'Node contents',
							'access' => 'node',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type' 			=> 'textarea:text',
									'name' 			=> 'revolver_node_edit_content',
									'placeholder'	=> 'Node contents',
									'required'		=> true,
									'rows'			=> 20,
									'value:html'	=> $markup::Markup(

										html_entity_decode(

											htmlspecialchars_decode(

												$n['contents']

											)

										), [ 'xhash' => 0 ]

									),

								],

							],

						],

						'label_hiddens_0' => [

							'no-label' => true,
							'access' => 'node',
							'auth' => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_node_edit_id',
									'required'		=> true,
									'readonly'		=> true,
									'value'			=> preg_replace("/[^0-9]/", '', $n['id'])

								],

								1 => [

									'type' 			=> 'input:hidden',
									'name' 			=> 'revolver_node_country',
									'required'		=> true,
									'readonly'		=> true,
									'value'			=> $n['language']['cipher']

								],

							],

						],

						'label_chechboxes_0' => [

							'title' => 'Show on mainpage',
							'access' => 'node',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:checkbox:'. ((bool)$n['mainpage'] ? 'checked' : 'unchecked'),
									'name' 			=> 'revolver_node_mainpage',
									'value'			=> 1

								],

							],

						],

						'label_chechboxes_1' => [

							'title' => 'Publish node',
							'access' => 'node',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:checkbox:'. ((bool)$n['published'] ? 'checked' : 'unchecked'),
									'name' 			=> 'revolver_node_published',
									'value'			=> 1

								],

							],

						],

						'label_chechboxes_2' => [

							'title' => 'Delete node',
							'access' => 'node',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type' 			=> 'input:checkbox:unchecked',
									'name' 			=> 'revolver_node_edit_delete',
									'value'			=> 'delete',
									'disabled'		=> $allow_delete

								],

							],

						],

					],

				],

			],

		], // #tab 1

		'tab_2' => [

			// tab title
			'title' => 'Category',

			// included fieldsets
			'fieldsets' => [
				
				// fieldset contents parameters
				'fieldset_2' => [

					'title' => 'Category',
					
					// wrap fields into label
					'labels' => [

						'label_5' => [

							'title'  => 'Choose node category',
							'access' => 'node',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type' 			=> 'select',
									'name' 			=> 'revolver_node_edit_category',
									'required'		=> true

								],

							],

						],

					],

				],

			],

		], // #tab 2

		'tab_3' => [

			// tab title
			'title' => 'Attachements',

			// included fieldsets
			'fieldsets' => [
				
				// fieldset contents parameters
				'fieldset_3' => [

					'title' => 'Attached Files',
					'labels' => [

						'label_6' => [

							'title'  => 'Choose files to upload',
							'access' => 'node',
							'auth'	 => 1,
							'fields' => [

								0 => [

									'type' 			=> 'input:file',
									'name' 			=> 'revolver_node_files',
									'multiple'		=> true

								],

							],

						],

						'label_7' => [

							'title'  => 'Allowed files',
							'access' => 'node',
							'auth'   => 1,
							'collapse' => true,

							'fields' => [

								0 => [

									'html:contents' => $form_parameters_html_help

								],

							],

						],

					],

				],

			],

		], // #tab 3

	]

];

// TAB-2 Category Choose
$categories_options_list = '';

foreach( iterator_to_array(

		$model::get( 'categories', [

			'criterion' => 'id::*',
			'course'	=> 'forward',
			'sort' 		=> 'id'

		])

	)['model::categories'] as $k => $v ) {

	$categories_options_list .= '<option value="'. $v['id'] .'"'. ( $v['id'] === $n['category'] ? ' selected="selected"' : '') .'>'. $v['title'] .'</option>';

}

$form_parameters['tabs']['tab_2']['fieldsets']['fieldset_2']['labels']['label_5']['fields'][0]['value:html'] = $categories_options_list;

$render_node_html_files .= '<dl>';

$file_limit = 0;
$file_count = 0;

$files_list = iterator_to_array(

		$model::get( 'files', [

			'criterion' => 'node::'. explode('/edit/', RQST)[0] . '/',
			'course'	=> 'forward',
			'sort' 		=> 'id'

		])

	)['model::files'];

if( isset( $files_list[0] ) ) {

	foreach( $files_list as $file ) {

		$attached_file = '/public/uploads/'. $file['name'];

		foreach( $D::$file_descriptors as $attachement ) {

			if( pathinfo(basename($attached_file), PATHINFO_EXTENSION) === $attachement['extension'] ) {

				$message_files = $attachement['description'] .' .'. $attachement['extension'] .' '. round(((int)filesize(ltrim( $attached_file, '/')) / 1024), 3, PHP_ROUND_HALF_ODD) .' Kb';

				$render_node_html_files .= '<dt><label>'. ($file_limit + 1) .' &#8226; '. $file['name'];
				$render_node_html_files .= '<span>'. TRANSLATIONS[ $ipl ]['delete'] .': <input type="checkbox" name="revolver_delete_attached_file_'. $file_limit .'" value="'. $file['id'] .':'. $file['name'] .'" /></span></label>';
				$render_node_html_files .= '</dt>';
				$render_node_html_files .= '<dd style="padding: 0 20px 20px;">';
				$render_node_html_files .= TRANSLATIONS[ $ipl ]['file path'];
				$render_node_html_files .= ': <a style="color: #5e5c5d; text-decoration: none; border-bottom: 1px dashed #000; padding-bottom: 3px;" href="'. $attached_file .'" target="_blank">'. $attached_file .'</a> : ';
				$render_node_html_files .= $message_files .'.</dd>';

			}

		}

		$file_limit++;

	}

}

$render_node_html_files  .= '</dl>';

if( (bool)$file_limit ) {

	$form_parameters['tabs']['tab_3']['fieldsets']['fieldset_3']['labels']['label_10']['title'] = 'Attachements';
	$form_parameters['tabs']['tab_3']['fieldsets']['fieldset_3']['labels']['label_10']['access'] = 'node';
	$form_parameters['tabs']['tab_3']['fieldsets']['fieldset_3']['labels']['label_10']['auth'] = 1;
	$form_parameters['tabs']['tab_3']['fieldsets']['fieldset_3']['labels']['label_10']['fields'][0]['html:contents'] = $render_node_html_files;	

}

$render_node  = '';
$render_node .= '<article class="revolver__article article-id-'. $n['id'] .'-edit">';
$render_node .= '<header class="revolver__article-header">'; 
$render_node .= '<h2>'. $n['title'] .'<span style="float:right"> &#8226; '. TRANSLATIONS[ $ipl ]['language'] .' [ '. $n['language']['code_length_3'] .' :: '. $n['language']['hreflang'] .' ]</span></h2>';
$render_node .= '</header>';

if( isset($n['warning']) ) {

	$render_node .= $n['warning'];	

}

$render_node .= $form::build( $form_parameters, true );

$render_node .= '</article>';

?>
