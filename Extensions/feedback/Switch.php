<?php

/** 
  * 
  * RevolveR Feedback Extension
  *
  * v.1.9.0
  *
  */

if( defined('ROUTE') ) {

	switch( ROUTE['node'] ) {

		case '#preview':

			if( !empty(SV['p']) ) {

				if( isset(SV['p']['revolver_preview_mode']) ) {

					if( (bool)SV['p']['revolver_preview_mode']['valid'] ) {

						switch( SV['p']['revolver_preview_mode']['value'] ) {

							case 'feedback':

								ob_start();

								$render = '<!-- Service Preview :: extension Feedback -->';

								$title = $message = $sender_name = $sender_phone = $sender_email = null;

								if( isset(SV['p']['revolver_feedback_message_title']) ) {

									if( (bool)SV['p']['revolver_feedback_message_title']['valid'] ) {

										$title = strip_tags( SV['p']['revolver_feedback_message_title']['value'] );

									}

								}

								if( isset(SV['p']['revolver_feedback_message_message']) ) {

									if( (bool)SV['p']['revolver_feedback_message_message']['valid'] ) {

										$message = $markup::Markup( SV['p']['revolver_feedback_message_message']['value'], ['xhash' => 1] );

									}

								}

								if( isset(SV['p']['revolver_feedback_message_sender_name']) ) {

									if( (bool)SV['p']['revolver_feedback_message_sender_name']['valid'] ) {

										 $sender_name = strip_tags( SV['p']['revolver_feedback_message_sender_name']['value'] );

									}

								}

								if( isset(SV['p']['revolver_feedback_message_sender_phone_number']) ) {

									if( (bool)SV['p']['revolver_feedback_message_sender_phone_number']['valid'] ) {

										$sender_email = strip_tags( SV['p']['revolver_feedback_message_sender_phone_number']['value'] );

									}

								}

								if( isset(SV['p']['revolver_feedback_message_sender_email']) ) {

									if( (bool)SV['p']['revolver_feedback_message_sender_email']['valid'] ) {

										 $sender_phone = strip_tags( SV['p']['revolver_feedback_message_sender_email']['value'] );

									}

								}

								if( $title && $message && $sender_name && $sender_email ) {

									$notify::set('active', '<div>'. etranslations[ $ipl ]['Sense avalibale for feedback'] .'.</div>', null);

									$render .= '<article class="revolver__article revolver__article-preview">';

									$render .= '<header class="revolver__article-header">'; 

									$render .= '<h2>'. $title .'</h2>';

									$render .= '<time>'. date('Y.m.d h:i') .'</time>';

									$render .= '</header>';

									$render .= '<div class="revolver__article-contents">';

									$render .= '<h2 class="revolver_feedaback_heading">'. etranslations[ $ipl ]['Sender message'] .'</h2>';

									$render .= $message;

									$render .= '<h2 class="revolver_feedaback_heading">'. etranslations[ $ipl ]['Sender contact info'] .'</h2>';
									$render .= '<ul>';
									$render .= '<li>Sender: '. $sender_name .';</li>';

									if( $sender_phone ) {

										$render .= '<li>Telephone: '. $sender_phone .';</li>';

									}

									$render .= '<li>Email: '. $sender_email .'.</li>';
									$render .= '</ul>';

									$render .= '</div>';

									$render .= '</article>';

								}
								else {

									$notify::set('inactive', '<div>'. etranslations[ $ipl ]['Preview not available'] .'.</div>', null);

								}

								print $notify::Conclude() . $render;

							break;

						}

					}

				}

			}

			break;

		case '#feedback':

			ob_start();

			$contents  = '';

			if( !empty(SV['p']) ) {

				$message_title = $message_text = $sender_name = $sender_email = $sender_phone = $action_process = null;

				if( isset(SV['p']['revolver_process_action_process']) ) {

					if( (bool)SV['p']['revolver_process_action_process']['valid'] ) {

						$action_process = true;

					}

				}

				if( isset(SV['p']['revolver_feedback_message_title']) ) {

					if( (bool)SV['p']['revolver_feedback_message_title']['valid'] ) {

						$message_title = strip_tags( SV['p']['revolver_feedback_message_title']['value'] );

					}

				}

				if( isset(SV['p']['revolver_feedback_message_message']) ) {

					if( (bool)SV['p']['revolver_feedback_message_message']['valid'] ) {

						$message_text = SV['p']['revolver_feedback_message_message']['value'];

					}

				}

				if( isset(SV['p']['revolver_feedback_message_sender_name']) ) {

					if( (bool)SV['p']['revolver_feedback_message_sender_name']['valid'] ) {

						$sender_name = strip_tags( SV['p']['revolver_feedback_message_sender_name']['value'] );

					}

				}

				if( isset(SV['p']['revolver_feedback_message_sender_phone_number']) ) {

					if( (bool)SV['p']['revolver_feedback_message_sender_phone_number']['valid'] ) {

						$sender_phone = strip_tags( SV['p']['revolver_feedback_message_sender_phone_number']['value'] );

					}

				}

				if( isset(SV['p']['revolver_feedback_message_sender_email']) ) {

					if( (bool)SV['p']['revolver_feedback_message_sender_email']['valid'] ) {

						$sender_email = strip_tags( SV['p']['revolver_feedback_message_sender_email']['value'] );

					}

				}

				if( isset(SV['p']['revolver_captcha']) ) {

					if( (bool)SV['p']['revolver_captcha']['valid'] ) {

						if( $captcha::verify(SV['p']['revolver_captcha']['value']) ) {

							define('form_pass', 'pass');

						}

					}

				}

				$feedback_moderate = [];

				for( $i = 0; $i < 100; $i++ ) {

					if( !isset(SV['p'][ 'revolver_process_feedback_'. $i ]) ) {

						$feedback_moderate[ $i ][0] = $i .'::0';

					}
					else {

						if( (bool)SV['p'][ 'revolver_process_feedback_'. $i ]['valid'] ) {

							$feedback_moderate[ $i ][0] = $i .'::1';

						}
						else {

							$feedback_moderate[ $i ][0] = $i .'::0';

						}

					}

					if( isset( SV['p'][ 'revolver_delete_feedback_'. $i ] ) ) {

						if( (bool)SV['p'][ 'revolver_delete_feedback_'. $i ]['valid'] ) {

							$feedback_moderate[ $i ][1] = SV['p'][ 'revolver_delete_feedback_'. $i ]['value'];

						}

					}

				}

			}

			if( INSTALLED ) {

				$extension_id = $extension_enabled_set = $extension_cache_enabled_set = $extension_install_set = $extension_uninstall_set = 0;

				$enabled_lock = $cache_lock = $installed = $enabled = $post_status = null;

				if( !empty(SV['p']) ) {

					$post_status = true;

					if( isset( ACCESS['role'] ) ) {

						if( ACCESS['role'] === 'Admin' ) {

							if( isset(SV['p']['revolver_feedback_enabled']) ) {

								if( (bool)SV['p']['revolver_feedback_enabled']['valid'] ) {

									if( SV['p']['revolver_feedback_enabled']['value'] === 'on' ) {

										$extension_enabled_set = 1;

									}

								}

							}

							if( isset(SV['p']['revolver_feedback_cache_enabled']) ) {

								if( (bool)SV['p']['revolver_feedback_cache_enabled']['valid'] ) {

									if( SV['p']['revolver_feedback_cache_enabled']['value'] === 'on' ) {

										$extension_cache_enabled_set = 1;

									}

								}

							}

							if( isset(SV['p']['revolver_feedback_install']) ) {

								if( (bool)SV['p']['revolver_feedback_install']['valid'] ) {

									if( SV['p']['revolver_feedback_install']['value'] === 'on' ) {

										$extension_install_set = 1;

										$installed = true;

									}

								}

							}

							if( isset(SV['p']['revolver_feedback_uninstall']) ) {

								if( (bool)SV['p']['revolver_feedback_uninstall']['valid'] ) {

									if( SV['p']['revolver_feedback_uninstall']['value'] === 'on' ) {

										$extension_uninstall_set = 1;

									}

								}

							}

							if( isset(SV['p']['revolver_captcha']) ) {

								if( (bool)SV['p']['revolver_captcha']['valid'] ) {

									if( $captcha::verify(SV['p']['revolver_captcha']['value']) ) {

										define('form_pass', 'pass');

									}

								}

							}

						}

					}

				}

				foreach( EXTENSIONS_SETTINGS as $e ) {

					if( $e['name'] === ltrim(ROUTE['node'], '#') ) {

						$extension_id = $e['id'];

						$extension_enabled = $e['enabled'];

						$extension_cache_enabled = $e['cache'];

						// Set lock 
						$enabled_lock = (bool)$e['enabled'] ? true : null;

						// Enabled 
						$enabled = (bool)$e['enabled'];

						// Installed
						$installed = true;

						break;

					}

				}

				$stored = null;

				if( ROLE === 'Admin' ) {

					if( $installed && $extension_enabled ) {

						if( $action_process ) {

							$feedbacks = iterator_to_array(

								$model::get('feedback', [

									'criterion' => 'id::*'

								])

							)['model::feedback'];

							foreach( $feedback_moderate as $n => $m ) {

								$erased = null;

								if( isset($m[1]) ) {

									$model::erase('feedback', [

										'criterion' => 'id::'. $m[1]

									]);

									if( $feedbacks ) {

										$files_deleted = null;

										foreach( $feedbacks as $f ) {

											if( (int)$m[1] === (int)$f['id'] ) {

												$files = iterator_to_array(

														$model::get('feedback_files', [

															'criterion' => 'message_hash::'. $f['message_hash']

														])

													)['model::feedback_files'];


												if( $files ) {

													foreach( $files as $xf ) {

														unlink( $_SERVER['DOCUMENT_ROOT'] .'/public/feedback/'. $xf['file'] );

													}

												}

												$model::erase('feedback_files', [

													'criterion' => 'message_hash::'. $f['message_hash']

												]);

												$files_deleted = true;

											}

										}

									}

									$erased = true;

									if( $files_deleted ) {

										$notify::set('notice', 'Feedback files erased');

									}

									$notify::set('notice', 'Feedback '. $m[1] .' erased.', null);

								}

								if( isset($m[0]) && !$erased ) {

									if( $feedbacks ) {

										foreach( $feedbacks as $f ) {

											$cmd = explode('::', $m[0]);

											if( (int)$f['id'] === (int)$cmd[0] ) {  

												if( $f['message_processed'] !== $cmd[1] ) {

													$update = $f;

													$update['message_processed'] = $cmd[1];

													$update['message_text'] = html_entity_decode( $update['message_text'] );

													$update['criterion'] = 'id';

													$model::set('feedback', $update);

													if( (bool)$cmd[1] ) {

														$notify::set('active', 'Feedback '. $comment['id'] .' processed.', null);

													}
													else {

														$notify::set('inactive', 'Feedback '. $comment['id'] .' unprocessed.', null);

													}

												}

											}

										}

									}

								}

							}

						}

					}

				}

				// Send and store feedback message request
				if( defined('form_pass') && ROLE !== 'Admin' ) {

					if( form_pass === 'pass' ) {

						if( $installed && $extension_enabled ) {

							if( $message_title && $message_text && $sender_name && $sender_email && !$action_process ) {

								$notify::set('status', 'Feedback request stored and send');

								$message_hash = md5( 

									base64_encode( date('Y.m.d h:i') . $message_title ) 

								);

								$model::set('feedback', [

									'id'					=> 0,
									'sender_name'			=> $sender_name,
									'sender_email'			=> $sender_email,
									'sender_phone'			=> $sender_phone ? $sender_phone : '',
									'message_hash'			=> $message_hash,
									'message_title'			=> $message_title,
									'message_time'			=> date('Y.m.d h:i'),
									'message_text'			=> $markup::Markup( $message_text, ['xhash' => 0] ),
									'message_processed'		=> 0,
									'criterion'				=> 'id'

								]);

								if( (bool)count(SV['f']) ) {

									foreach( SV['f'] as $file ) {

										foreach( $file as $f ) {

											$upload_allow = null;

											if( !is_readable($_SERVER['DOCUMENT_ROOT'] .'/public/feedback/'. $f['name']) ) {

												if( (bool)$f['valid'] ) {

													$upload_allow = true;

												}

											}

											if( $upload_allow ) {

												$model::set('feedback_files', [

													'id'			=> 0,
													'message_hash'	=> $message_hash,
													'file'			=> $f['name'],
													'criterion'		=> 'id'

												]);

												move_uploaded_file( $f['temp'], $_SERVER['DOCUMENT_ROOT'] .'/public/feedback/'. $f['name'] );

											}

										}

									}

								}

								$stored = true;

							}

						}

					}
					else {

						$notify::set('notice', 'Security check not pass');

					}

				}

				$pass = true;

				// Settings manage
				if( defined('form_pass') ) {

					if( form_pass === 'pass' ) {

						if( (bool)$extension_uninstall_set ) {

							if( ROLE === 'Admin' ) {

								$model::erase('extensions', [

									'criterion' => 'id::'. $extension_id

								]);

								$dbx::query('d', 'revolver__feedback', $DBX_KERNEL_SCHEMA['feedback']);

								$dbx::query('d', 'revolver__feedback_files', $DBX_KERNEL_SCHEMA['feedback_files']);

								$notify::set('notice', 'Extension feedback now uninstalled');

								$installed = null;

							}

						}
						else {

							if( ROLE === 'Admin' ) {

								$model::set('extensions', [

									'id'		=> $extension_id,
									'name'		=> 'feedback',
									'enabled'	=> $extension_enabled_set,
									'cache'		=> $extension_cache_enabled_set,
									'criterion' => 'name'

								]);

								// Installed status
								if( (bool)$extension_install_set ) {

									$notify::set('status', 'Extension feedback now installed');

									$dbx::query('c', 'revolver__feedback', $DBX_KERNEL_SCHEMA['feedback']);

									$dbx::query('c', 'revolver__feedback_files', $DBX_KERNEL_SCHEMA['feedback_files']);

								}

								// Enabled status
								if( (bool)$extension_enabled_set && !(bool)$extension_enabled ) {


									$notify::set('status', 'Extension feedback now enabled');

									// Enabled cache status
									if( ROLE === 'Admin' ) {

										if( (bool)$extension_cache_enabled ) {

											if( (bool)$extension_cache_enabled_set ) {

												$notify::set('status', 'Extension feedback cache enabled');

												$notify::set('active', 'Extension feedback cache now active');

											}

										}

										// Disabled cache status
										if( !(bool)$extension_cache_enabled && !(bool)$extension_cache_enabled_set ) {

											$notify::set('status', 'Extension feedback now enabled');

											$notify::set('notice', 'Extension feedback cache not enabled');

											$notify::set('inactive', 'Extension feedback cache now inactive');

										}

									}

									$enabled = true;

								}

								// Disabled status
								if( !(bool)$extension_enabled_set && (bool)$extension_enabled ) {

									$notify::set('notice', 'Extension feedback now disabled');

									$notify::set('notice', 'Extension feedback cache not enabled');

									$notify::set('inactive', 'Extension feedback cache now inactive');

								}

								// Enabled cache status
								if( (bool)$extension_cache_enabled_set && !(bool)$extension_cache_enabled ) {

									if( $enabled  ) {	

										$notify::set('status', 'Extension feedback now enabled');

										$notify::set('status', 'Extension feedback cache enabled');

										$notify::set('active', 'Extension feedback cache now active');

									}

								}

								// Disabled cache status
								if( !(bool)$extension_cache_enabled_set && (bool)$extension_cache_enabled ) {

									$notify::set('status', 'Extension feedback now enabled');

									$notify::set('notice', 'Extension feedback cache not enabled');

									$notify::set('inactive', 'Extension feedback cache now inactive');

								}

							}

							$extension_enabled = $extension_enabled_set;

							$extension_cache_enabled = $extension_cache_enabled_set;

							$enabled_lock = (bool)$extension_enabled ? true : null;

						}

					}
					else {

						$notify::set('notice', 'Security check not pass');

					}

				}

				// Not installed status
				if( !$installed ) {

					$notify::set('warning', 'Extension feedback not intsalled');

					if( ROLE !== 'Admin' ) {

						$notify::set('status', 'Use Admin privileges to manage');

					}

				}

				// Not enabled status
				if( $installed && !$enabled ) {

					$notify::set('notice', 'Extension feedback not enabled');

					if( ROLE === 'Admin' ) {

						$notify::set('notice', 'Extension feedback cache not enabled');

						$notify::set('inactive', 'Extension feedback cache now inactive');

					}
					else {

						$notify::set('inactive', 'Use Admin privileges to manage');

					}

				}

				if( isset(ACCESS['role']) ) {

					if( ACCESS['role'] === 'Admin' )  {

						if( !$installed ) {

							$form_parameters = [

								'id'		=> 'feedback-settings',
								'class'		=> 'revolver__feedback-settings-form revolver__new-fetch',
								'action'	=> '/feedback/',
								'method'	=> 'POST',
								'captcha'	=> true,
								'encrypt'	=> true,
								'submit'	=> 'Install',

								'fieldsets' => [

									'fieldset_1' => [

										'title' => 'Feedback extension install',

										'labels' => [

											'label_1' => [

												'title'		=> 'Install',
												'access'	=> 'preferences',
												'auth'		=> 1,

												'fields' => [

													0 => [

														'type'		=> 'input:hidden',
														'name'		=> 'revolver_feedback_enabled',
														'value'		=> 1

													],

													1 => [

														'type'		=> 'input:hidden',
														'name'		=> 'revolver_feedback_cache_enabled',
														'value'		=> 1

													],

													2 => [

														'type'		=> 'input:checkbox:unchecked',
														'name'		=> 'revolver_feedback_install'

													]

												],

											],

										],

									],

								]

							];

						} 
						else {

							$form_parameters = [

								'id'		=> 'feedback-settings',
								'class'		=> 'revolver__feedback-settings-form revolver__new-fetch',
								'action'	=> '/feedback/',
								'method'	=> 'POST',
								'captcha'	=> true,
								'encrypt'	=> true,
								'submit'	=> 'Set',

								'fieldsets' => [

									'fieldset_1' => [

										'title' => 'Feedback settings',

										'labels' => [

											'label_1' => [

												'title'  => (bool)$extension_enabled ? 'Enabled' : 'Disabled',
												'access' => 'preferences',
												'auth'   => 1,

												'fields' => [

													0 => [

														'type'	=> 'input:checkbox:'. ( (bool)$extension_enabled ? 'checked' : 'unchecked' ),
														'name'	=> 'revolver_feedback_enabled'

													]

												],

											],

											'label_2' => [

												'title'  => (bool)$extension_cache_enabled ? 'Cache enabled' : 'Cache disabled',
												'access' => 'preferences',
												'auth'   => 1,

												'fields' => [

													0 => [

														'type'		=> 'input:checkbox:'. ( (bool)$extension_cache_enabled ? 'checked' : 'unchecked' ),
														'name'		=> 'revolver_feedback_cache_enabled',
														'disabled'	=> $cache_lock
													]

												],

											],

										],

									],

								]

							];

						}

						if( !$enabled_lock && $installed ) {

							$form_parameters['fieldsets']['fieldset_1']['labels']['label_3'] = [

								'title'		=> 'Uninstall',
								'access'	=> 'preferences',
								'auth'		=> 1,

								'fields' => [

									0 => [

										'type'	=> 'input:checkbox:unchecked',
										'name'	=> 'revolver_feedback_uninstall'

									]

								]

							];

						}

						$contents .= '<h2 class="revolver__collapse-form-legend revolver__collapse-form-legend-form-free">'. etranslations[ $ipl ]['Feedback settings'] .'</h2>';

						$contents .= '<output class="revolver__collapse-form-contents" style="overflow: hidden; width: 0px; height: 0px; line-height: 0px; display: inline-block; min-height: 0px; opacity: 0; transform: scaleX(1) scaleY(1) scaleZ(1);">';
						$contents .= $form::build( $form_parameters, null, etranslations );
						$contents .= '</output>';

					}

				}

				// Fedback request form
				if( $installed && $enabled && !Auth && !$stored ) {

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

					$form_parameters_request_form = [

						'id'		=> 'request-feedback',
						'class'		=> 'revolver__feedback-request-form revolver__new-fetch',
						'action'	=> '/feedback/',
						'method'	=> 'POST',
						'captcha'	=> true,
						'encrypt'	=> true,
						'submit'	=> 'Send',

						'fieldsets' => [

							'fieldset_1' => [

								'title'	=> 'Introduce yourself',

								'labels' => [

									'label_1' => [

										'title'		=> 'Sender name',
										'access'	=> 'comment',
										'auth'		=> 'all',

										'fields' => [

											0 => [

												'type'			=> 'input:text',
												'name'			=> 'revolver_feedback_message_sender_name',
												'placeholder'	=> 'Leave your name',
												'required'		=> true,
												'value'			=> (bool)$sender_name ? $sender_name : ''

											]

										],

									],

									'label_2' => [

										'title'		=> 'Sender email',
										'access'	=> 'comment',
										'auth'		=> 'all',

										'fields' => [

											0 => [

												'type'			=> 'input:text',
												'name'			=> 'revolver_feedback_message_sender_email',
												'placeholder'	=> 'Leave your email',
												'required'		=> true,
												'value'			=> (bool)$sender_email ? $sender_email : ''

											]

										],

									],

									'label_3' => [

										'title'		=> 'Sender phone number',
										'access'	=> 'comment',
										'auth'		=> 'all',

										'fields' => [

											0 => [

												'type'			=> 'input:tel',
												'name'			=> 'revolver_feedback_message_sender_phone_number',
												'placeholder'	=> 'Leave your phone number',
												'value'			=> (bool)$sender_phone ? $sender_phone : ''

											]

										],

									],

								],

							],

							'fieldset_2' => [

								'title' => 'Feedback request details',

								'labels' => [

									'label_4' => [

										'title'		=> 'Message title',
										'access'	=> 'comment',
										'auth'		=> 'all',

										'fields' => [

											0 => [

												'type'			=> 'input:text',
												'name'			=> 'revolver_feedback_message_title',
												'placeholder'	=> 'Define message title',
												'required'		=> true,
												'value'			=> (bool)$message_title ? $message_title : '' 

											]

										],

									],

									'label_5' => [

										'title'		=> 'Message text',
										'access'	=> 'comment',
										'auth'		=> 'all',

										'fields' => [

											0 => [

												'type'			=> 'textarea:text',
												'name'			=> 'revolver_feedback_message_message',
												'placeholder'	=> 'Define message text',
												'rows'			=> 7,
												'required'		=> true,
												'value:html'	=> (bool)$message_text ? $message_text : ''

											]

										],

									],

									'label_6' => [

										'title'		=> 'Allowed files',
										'access'	=> 'comment',
										'auth'		=> 'all',
										'collapse'	=> true,

										'fields' => [

											0 => [

												'html:contents' => $form_parameters_html_help

											]

										],

									],

									'label_7' => [

										'title'		=> 'Choose files to upload',
										'access'	=> 'comment',
										'auth'		=> 'all',
										'fields'	=> [

											0 => [

												'type'		=> 'input:file',
												'name'		=> 'revolver_feedback_message_files',
												'multiple'	=> true

											]

										],

									],

								],

							],

						]

					];

					$contents .= $form::build( $form_parameters_request_form, null, etranslations );

				} 
				else if( $stored ) {

					$contents .= '<p>'. etranslations[ $ipl ]['Feedback request sent successfully'] .'.</p>';

				}

				if( in_array(ROLE, ['Admin', 'Writer']) ) {

					$feedback = iterator_to_array(

						$model::get('feedback', [

							'criterion' => 'id::*',
							'course'    => 'backward',
							'sort'      => 'id'

						])

					)['model::feedback'];

					$rfiles = iterator_to_array(

							$model::get('feedback_files', [

								'criterion' => 'id::*',
								'course'	=> 'forward',
								'sort'		=> 'id'

							])

						)['model::feedback_files'];

					if( $feedback ) {

						$processing_feedback_form = [

							'id'		=> 'feedback-processing',
							'class'		=> 'revolver__feedback-feedback-processing revolver__new-fetch',
							'action'	=> '/feedback/',
							'method'	=> 'POST',
							'captcha'	=> null,
							'encrypt'	=> true,
							'submit'	=> 'Manage'

						];

						$processing_feedback_form['fieldsets']['fieldset_0']['title'] = 'Manage feedbacks';

						$n = 0;

						foreach( $feedback as $f ) {

							$files_flag = null;

							$contents_feedback_files = ''; 

							$processed = (bool)$f['message_processed'] ? 'proccessed' : 'unprocessed';

							$contents_feedback  = '<article id="feedback-'. $f['id'] .'" class="revolver__feedback feedback-'. $f['id'] .' '. $processed .'">';
							$contents_feedback .= '<header class="revolver__feedback-header">';

							$contents_feedback .= '<h2>• '. etranslations[ $ipl ]['Feedback request'] .' '. $f['id'] .' '. etranslations[ $ipl ]['by'] .' <span>'. $f['sender_name'] .'</span></h2>';
							$contents_feedback .= '<time>'. $f['message_time'] .'</time>';

							$contents_feedback .= '</header>';

							$contents_feedback .= '<div class="revolver__article-contents">';

							$contents_feedback .= $markup::markup(

								$f['message_text'], [ 'xhash' => 1 ]

							);

							$contents_feedback .= '</div>';

							if( $rfiles ) {

								foreach( $rfiles as $file ) {

									if( $file['message_hash'] === $f['message_hash'] ) {

										$attached_file = '/public/feedback/'. $file['file'];

										$contents_feedback_files .= '<li><a href="'. $attached_file .'" target="_blank" class="external">'. $file['file'] .'</a>';

										foreach( $D::$file_descriptors as $attachement ) {

											if( pathinfo( basename($attached_file), PATHINFO_EXTENSION ) === $attachement['extension'] ) {

												$contents_feedback_files .= '<span>'. $attachement['description'] .' .'. $attachement['extension'] .' '. round(

													((int)filesize(ltrim( $attached_file, '/')) / 1024), 3, PHP_ROUND_HALF_ODD

												) .' Kb</span>';

											}

										}

										$contents_feedback_files .= '</li>';

										$files_flag = true;

									}

								}

								if( $files_flag ) {

									$contents_feedback .= '<h2>'. etranslations[ $ipl ]['Attached files'] .'</h2>';

								}

								$contents_feedback .= '<ul class="revolver__files_list">';
								$contents_feedback .= $contents_feedback_files;
								$contents_feedback .= '</ul>';

							}

							$contents_feedback .= '<div class="revolver__moderation-flags">';
							$contents_feedback .= '<label id="label_processed_'. $n .'">'.  etranslations[ $ipl ]['Processed'] .':';
							$contents_feedback .= '<input type="hidden" name="revolver_process_feedback_0" value="0::1" />';
							$contents_feedback .= '<input type="checkbox" name="revolver_process_feedback_'. $f['id'] .'" value="'. $f['id'] .'::'. ( (bool)$f['message_processed'] ? 1 : 0 ) .'" '. ( (bool)$f['message_processed'] ? 'checked="checked"' : '' ) .' />';
							$contents_feedback .= '</label>';

							$contents_feedback .= '<label id="label_delete_'. $n .'">'.  etranslations[ $ipl ]['Delete feedback'] .':';
							$contents_feedback .= '<input type="checkbox" name="revolver_delete_feedback_'. $f['id'] .'" value="'. $f['id'] .'" />';
							$contents_feedback .= '</label>';
							$contents_feedback .= '</div>';

							$contents_feedback .= '</article>';

							$processing_feedback_form['fieldsets']['fieldset_0']['title'] = 'Feedback requests';
							$processing_feedback_form['fieldsets']['fieldset_0']['labels']['label_0']['title'] = 'Manage';
							$processing_feedback_form['fieldsets']['fieldset_0']['labels']['label_0']['access'] = 'preferences';
							$processing_feedback_form['fieldsets']['fieldset_0']['labels']['label_0']['auth'] = 1;

							$processing_feedback_form['fieldsets']['fieldset_0']['labels']['label_0']['fields'][0]['html:contents'] .= $contents_feedback;

							$n++;

						}

						$processing_feedback_form['fieldsets']['fieldset_0']['labels']['label_0']['fields'][0]['html:contents'] .= '<input type="hidden" name="revolver_process_action_process" value="1" />';

						$contents .= $form::build( $processing_feedback_form, null, etranslations );

					} 
					else {

						$contents .= '<p>'. etranslations[ $ipl ]['No feedbacks to manage found'] .'.</p>';

					}

				}

				if( $installed && !$enabled && !Auth ) { 

					$contents .= '<p>'. etranslations[ $ipl ]['Feedback not enabled for now'] .'.</p>';

				}

				$node_data[] = [

					'title'		=> 'Feedback',
					'id'		=> 'feedback',
					'route'		=> '/feedback/',
					'contents'	=> $contents,
					'teaser'	=> false,
					'footer'	=> false,
					'time'		=> false,
					'published'	=> 1

				];

			}
			else {

				header('Location: '. site_host .'/');

			}

		break;

	}

}

?>
