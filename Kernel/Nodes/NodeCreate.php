<?php

 /* 
  * 
  * RevolveR Create new node
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
  *
  */

if( Auth ) { 

	if( in_array(ROLE, ['Admin', 'Writer'], true) ) {

		$index_language = '840';

		if( !empty(SV['p']) ) {

			$node_title = $node_content = $node_description = $node_route = '';

			$node_category = 0;

			if( isset(SV['p']['revolver_node_edit_title']) ) {

				if( (bool)SV['p']['revolver_node_edit_title']['valid'] ) {

					$node_title = strip_tags( SV['p']['revolver_node_edit_title']['value'] );

				}

			}

			if( isset(SV['p']['revolver_node_edit_content']) ) {

				if( (bool)SV['p']['revolver_node_edit_content']['valid'] ) {

					$node_content = $markup::Markup(

						SV['p']['revolver_node_edit_content']['value'], [ 'xhash' => 0 ]

					);

				}

			}

			if( isset(SV['p']['revolver_node_edit_description']) ) {

				if( (bool)SV['p']['revolver_node_edit_description']['valid'] ) {

					$node_description = strip_tags( SV['p']['revolver_node_edit_description']['value'] );

				}

			}

			if( isset(SV['p']['revolver_node_edit_route']) ) {

				if( (bool)SV['p']['revolver_node_edit_route']['valid'] ) {

					$node_route = preg_replace("/\/+/", '/', preg_replace("/ +/", '-', trim( SV['p']['revolver_node_edit_route']['value'] )));

				}

			}

			if( isset(SV['p']['revolver_node_edit_category']) ) {

				if( (bool)SV['p']['revolver_node_edit_category']['valid'] ) {

					$node_category = SV['p']['revolver_node_edit_category']['value'][0];

				}

			}

			if( isset(SV['p']['revolver_country_code']) ) {

				if( (bool)SV['p']['revolver_country_code']['valid'] ) {

					$index_language = SV['p']['revolver_country_code']['value'];

					$hreflang = $lang::getLanguageData( $index_language )['hreflang'];

				}

			}

			if( isset(SV['p']['revolver_captcha']) ) {

				if( (bool)SV['p']['revolver_captcha']['valid'] ) {

					if( $captcha::verify(SV['p']['revolver_captcha']['value']) ) {

						define('form_pass', 'pass');

					}

				}

			}

			$upload_allow = true;

			$passed = true;

			foreach( main_nodes as $k => $v ) {

				if( trim($v['route']) === trim($node_route) ) {

					$passed = null;

					break;

				}

			}

			if( strlen( $node_route ) !== strlen( utf8_decode( $node_route ) ) ) {

				$passed = null;

			}

			$route_fix = ltrim(

				rtrim(

					$node_route, '/'

				), '/'

			);

			$nodes = iterator_to_array(

				$model::get( 'nodes', [

					'criterion' => 'route::'. '/'. $hreflang .'/'. $route_fix .'/',

					'bound'		=> [

						1

					],

					'course'	=> 'backward',
					'sort' 		=> 'id'

				])

			)['model::nodes'];

			if( $nodes ) {

				$passed = null;

			}

			if( defined('form_pass') ) {

				if( $passed && form_pass === 'pass' && (bool)SV['p']['identity']['validity'] ) {

					$node_route = '/'. $hreflang .'/'. urlencode( $route_fix ) .'/';

					$model::set('nodes', [

						'title'			=> $node_title,
						'content'		=> $node_content,
						'description'	=> $node_description,
						'route'			=> $node_route,
						'category'		=> $node_category,
						'user'			=> USER['name'],
						'time'			=> date('d.m.Y h:i'),
						'country'		=> $index_language,
						'published'		=> 0,
						'mainpage'		=> 0

					]);

					if( (bool)count(SV['f']) ) {

						foreach( SV['f'] as $file ) {

							foreach( $file as $f ) {

								$upload_allow = null;

								if( !is_readable($_SERVER['DOCUMENT_ROOT'] .'/public/uploads/'. $f['name']) ) {

									if( (bool)$f['valid'] ) {

										$upload_allow = true;

									}

								}

								if( $upload_allow ) {

									$model::set('files', [

										'node'			=> $node_route,
										'name'			=> $f['name']

									]);

									move_uploaded_file( $f['temp'], $_SERVER['DOCUMENT_ROOT'] .'/public/uploads/'. $f['name'] );

								}

							}

						}

					}

					header( 'Location: '. site_host . $node_route .'?notification=node-created' );

				}
				else {

					$notify::set('notice', 'Route exist or security check not pass');

				}

			}
			else {

				$notify::set('notice', 'Security check not pass');

			}

		}

		$title = TRANSLATIONS[ $ipl ]['Create Node'];

		$form_parameters_html_help .= '<ul class="revolver__allowed-files-description-table">';
		$form_parameters_html_help .= '<li class="revolver__table-header">';
		$form_parameters_html_help .= '<span class="revolver__allowed-files-description">'. TRANSLATIONS[ $ipl ]['File description'] .'</span>';
		$form_parameters_html_help .= '<span class="revolver__allowed-files-extension">'. TRANSLATIONS[ $ipl ]['Extension'] .'</span>';
		$form_parameters_html_help .= '<span class="revolver__allowed-files-size">'. TRANSLATIONS[ $ipl ]['Maximum allowed file size'] .'</span>';
		$form_parameters_html_help .= '<li>';

		foreach( $D::$file_descriptors as $allowed_files ) {

			$form_parameters_html_help .= '<li>';
			$form_parameters_html_help .= '<span class="revolver__allowed-files-description">'. $allowed_files['description'] .'</span>';
			$form_parameters_html_help .= '<span class="revolver__allowed-files-extension">'. $allowed_files['extension'] .'</span>';
			$form_parameters_html_help .= '<span class="revolver__allowed-files-size">'. round((int)$allowed_files['max-size'] / 1024, 1, PHP_ROUND_HALF_ODD) .' Kb</span>';
			$form_parameters_html_help .= '</li>';

		}

		$form_parameters_html_help .= '</ul>';

		// User Profile Form Structure
		$form_parameters = [

			// main parameters
			'id'		=> 'node-create-form',
			'class'		=> 'revolver__node-create-form revolver__new-fetch',
			'action'	=> '/node/create/',
			'method'	=> 'post',
			'encrypt'	=> true,
			'captcha'	=> true,
			'submit'	=> 'Submit',

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

							'title' => 'New node editor',

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
											'value'			=> $node_title

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
											'placeholder'	=> 'Node description',
											'required'		=> true,
											'value'			=> $node_description

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
											'value'			=> $node_route

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
											'value:html'	=> $node_content

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

											'type' 		=> 'select',
											'name' 		=> 'revolver_node_edit_category',
											'required'	=> true

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

											'type' 		=> 'input:file',
											'name' 		=> 'revolver_node_files',
											'multiple'	=> true

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

				'tab_4' => [

					// tab title
					'title' => 'Choose language for current node',

					// included fieldsets
					'fieldsets' => [

						// fieldset contents parameters
						'fieldset_4' => [

							'title' => 'Choose language for current node',

							'labels' => [

								'label_8' => [

									'title'  => 'Choose files to upload',
									'access' => 'node',
									'auth'	 => 1,

									'fields' => [

										0 => [

											'type' 		=> 'input:file',
											'name' 		=> 'revolver_node_files',
											'multiple'	=> true

										],

									],

								],

							],

						],

					],

				], // #tab 4

			]

		];

		// TAB-2 Category Choose
		$categories_options_list = '';

		$c = 0;

		foreach( iterator_to_array(

				$model::get('categories', [

					'criterion' => 'id::*',
					'course'	=> 'forward',
					'sort' 		=> 'id'

				]),

			)['model::categories'] as $k => $v ) {

			if( !(bool)$c && !(bool)$node_category ) {

				$categories_options_list .= '<option value="'. $v['id'] .'" selected="selected">'. $v['title'] .'</option>';

			} else if( (int)$node_category === (int)$v['id'] ) {

				$categories_options_list .= '<option value="'. $v['id'] .'" selected="selected">'. $v['title'] .'</option>';

			}
			else {

				$categories_options_list .= '<option value="'. $v['id'] .'">'. $v['title'] .'</option>';

			}

			$c++;

		}

		$form_parameters['tabs']['tab_2']['fieldsets']['fieldset_2']['labels']['label_5']['fields'][0]['value:html'] = $categories_options_list;

		// TAB-4 Language
		$labels_count = 7;

		foreach( $lang::getLanguageData('*') as $country => $c ) {

			$labels_count++;

			$form_parameters['tabs']['tab_4']['fieldsets']['fieldset_4']['labels']['label_'. $labels_count]['title:html'] = TRANSLATIONS[ $ipl ]['Language'] .' <span class="revolver__stats-system">[ '. $c['code_length_3'] .' :: '. $c['code_length_2'] .' :: '. $c['hreflang'] .' ]</span> <i class="state-attribution laguage-list-item revolver__sa-iso-'. strtolower( $c['code_length_2'] ) .'"></i>'. TRANSLATIONS[ $ipl ]['contents country'] .' <span class="revolver__stats-country">['. $c['name'] .']</span>';

			$form_parameters['tabs']['tab_4']['fieldsets']['fieldset_4']['labels']['label_'. $labels_count]['access'] = 'node';
			$form_parameters['tabs']['tab_4']['fieldsets']['fieldset_4']['labels']['label_'. $labels_count]['auth'] = 1;

			$form_parameters['tabs']['tab_4']['fieldsets']['fieldset_4']['labels']['label_'. $labels_count]['fields'][0]['type']  = 'input:radio:'. ( $c['cipher'] === $index_language ? 'checked' : 'unchecked' ); 
			$form_parameters['tabs']['tab_4']['fieldsets']['fieldset_4']['labels']['label_'. $labels_count]['fields'][0]['name']  = 'revolver_country_code';
			$form_parameters['tabs']['tab_4']['fieldsets']['fieldset_4']['labels']['label_'. $labels_count]['fields'][0]['value'] = $c['cipher'];

		}

		$contents .= $form::build( $form_parameters, true );

		$node_data[] = [

			'title'		=> $title,
			'id'		=> 'create',
			'route'		=> '/node/create/',
			'contents'	=> $contents,
			'teaser'	=> null,
			'footer'	=> null,
			'published' => 1

		];

	}

}

?>
