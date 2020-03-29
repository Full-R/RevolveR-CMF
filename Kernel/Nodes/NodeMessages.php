<?php

 /* 
  * 
  * RevolveR Node Messages
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

if( !empty(SV['p']) && ROLE !== 'none' ) {

	if( isset( SV['p']['revolver_user_action'] ) ) {

		if( (bool)SV['p']['revolver_user_action']['valid'] ) { 

			$action = SV['p']['revolver_user_action']['value'];

		}

	}

	if( isset( SV['p']['revolver_mailto_nickname'] ) ) {

		if( (bool)SV['p']['revolver_mailto_nickname']['valid'] ) { 

			$mailTo = SV['p']['revolver_mailto_nickname']['value'];

		}

	}

	if( isset( SV['p']['revolver_mailto_message'] ) ) {

		if( (bool)SV['p']['revolver_mailto_message']['valid'] ) { 

			$mailToMessage = $markup::Markup( 

					SV['p']['revolver_mailto_message']['value'], [ 'xhash' => 0 ]

				);

		}

	}


	if( isset( SV['p']['revolver_user_name'] ) ) {

		if( (bool)SV['p']['revolver_user_name']['valid'] ) { 

			$name = SV['p']['revolver_user_name']['value'];

		}

	}

	if( isset(SV['p']['revolver_captcha']) ) {

		if( (bool)SV['p']['revolver_captcha']['valid'] ) {

			if( $captcha::verify( SV['p']['revolver_captcha']['value'] ) ) {

				define('form_pass', 'pass');

			}

		}

	}

	$messages_to_delete = [];

	$m_count = 0;

	while( $m_count <= 100 ) {

		if( isset( SV['p'][ 'revolver_message_'. $m_count ] ) ) {

			if( (bool)SV['p'][ 'revolver_message_'. $m_count ]['valid'] ) {

				$messages_to_delete[ $m_count ] = SV['p'][ 'revolver_message_'. $m_count ]['value'];

			}

		}

		$m_count++;

	}

}

if( $action === 'message' ) {

	$allow_send = null;

	$user = iterator_to_array(

		$model::get( 'users', [

			'criterion' => 'nickname::'. $mailTo,

			'bound'		=> [

				1,   // limit

			],

			'course'	=> 'forward',
			'sort' 		=> 'id'

		])

	)['model::users'];

	if( $user ) {
		
		$allow_send = true;

		$user_m = $user[0];

		$user_id_to = $user_m['id'];

	}

	$mail_message = '';

	if( defined('form_pass') ) {

		if( $allow_send && form_pass === 'pass' && (bool)SV['p']['identity']['validity'] ) {

			$model::set('messages', [

				'user_id'	=> $user_id_to,
				'to' 		=> $mailTo,
				'from' 		=> $name,

				'message'	=> $mailToMessage,

				'time' => date('d.m.Y H:i')

			]);

			if( (bool)count(SV['f']) ) {

				foreach( SV['f'] as $file ) {

					foreach( $file as $f ) {

						$upload_allow = null;

						if( !is_readable( $_SERVER['DOCUMENT_ROOT'] .'/public/files/'. $f['name']) ) {

							if( (bool)$f['valid'] ) {

								$upload_allow = true;

							}

						}

						if( $upload_allow ) {

							$id = iterator_to_array(

									$model::get( 'messages', [

										'criterion' => 'to::'. $mailTo,

										'bound'		=> [

											1,   // limit

										],

										'course'	=> 'backward',
										'sort' 		=> 'id'

									])

								)['model::messages'];

							$model::set('messages_files', [

								'message_id'	=> $id[0]['id'],
								'file'			=> $f['name']

							]);

							move_uploaded_file( $f['temp'], $_SERVER['DOCUMENT_ROOT'] .'/public/files/'. $f['name'] );

						}

					}

				}

			}

			// Send notification to email
			$notification = '<p>'. TRANSLATIONS[ $ipl ]['Hello'] .' '. $mailTo .'! '. TRANSLATIONS[ $ipl ]['You have unreaded private message here'] .' <a href="'. site_host .'/user/messages/">here</a>.</p>';

			$mail::send(

				$user_m['email'], TRANSLATIONS[ $ipl ]['New private message'], $notification

			);

			$notify::set('status', 'Message sent');

		}
		else {

			$notify::set('notice', 'Message not sent. User not found');

		}

	}

}

if( $action === 'cleanup' && (bool)SV['p']['identity']['validity'] ) {

	$deleted = null;

	foreach( $messages_to_delete as $message ) {

		$mfiles = iterator_to_array(

					$model::get( 'messages_files', [

						'criterion' => 'message_id::'. $message,
						'course'	=> 'backward',
						'sort' 		=> 'id'

					])

				)['model::messages_files'];

		if( (bool)count($mfiles) ) {

			foreach( $mfiles as $f ) {		

				unlink( $_SERVER['DOCUMENT_ROOT'] .'/public/files/'. $f['file']);

			}

			$model::erase('messages_files', [

				'criterion' => 'message_id::'. $message

			]);

		}

		$model::erase('messages', [

			'criterion' => 'id::'. $message

		]);

		$deleted = true;

	}

	$notify::set('notice', 'Messages deleted');

}

// Messages
$contents .= $mail_message;

$messages_list = iterator_to_array(

					$model::get( 'messages', [

						'criterion' => 'user_id::'. USER['id'],
						'course'	=> 'forward',
						'sort' 		=> 'id'

					])

				)['model::messages'];


$contents_messages .= '<dl class="revolver__messages">';

$messages_counter = 0;

if( $messages_list ) {

	foreach( $messages_list as $key => $v ) {

		$message_files  = '<label>'. TRANSLATIONS[ $ipl ]['Attached Files'];
		$message_files .= '<ul class="revolver__files-list">';

		$message_files_count = 0;

		$mfiles = iterator_to_array(

					$model::get( 'messages_files', [

						'criterion' => 'message_id::'. $v['id'],
						'course'	=> 'forward',
						'sort' 		=> 'id'

					])

				)['model::messages_files'];

		if( $mfiles ) {

			foreach( $mfiles as $f ) {

				$attached_file = '/public/files/'. $f['file'];

				$message_files .= '<li>';
				$message_files .= '#'. ( $message_files_count + 1 ) .' '. TRANSLATIONS[ $ipl ]['file path'] .': <a href="'. $attached_file .'" target="_blank" class="external">'. $f['file'] .'</a>';

				foreach( $D::$file_descriptors as $attachement ) {

					if( pathinfo( basename($attached_file), PATHINFO_EXTENSION ) === $attachement['extension'] ) {

						$message_files .= ' : ['. $attachement['description'] .' .'. $attachement['extension'] .' '. round(

							((int)filesize(ltrim( $attached_file, '/')) / 1024), 3, PHP_ROUND_HALF_ODD

						) .' Kb]';

					}

				}

				$message_files .= '</li>';

				$message_files_count++;

			}

		}

		$checkBox  = '<label>'. TRANSLATIONS[ $ipl ]['Delete message'] . ( (bool)$message_files_count ? ' '. TRANSLATIONS[ $ipl ]['and'] .' '. TRANSLATIONS[ $ipl ]['Attached Files'] : '') .': <input type="checkbox" name="revolver_message_'. $messages_counter .'" value="'. $v['id'] .'" /></label>'; 

		$contents_messages .= '<dd class="revolver__messages-message">';

		$user = iterator_to_array(

					$model::get( 'users', [

						'criterion' => 'nickname::'. $v['from'],
						'course'	=> 'forward',

						'bound'		=> [

							1

						],

						'sort' 		=> 'id'

					])

				)['model::users'];

		if( $user ) {

			if( $user[0]['avatar'] === 'default' ) {

				$avatar = '<img src="/public/avatars/default.png" alt="'. $v['from'] .'" />';

			}
			else {

				$avatar = '<img src="'. $user[0]['avatar'] .'" alt="'. $v['from'] .'" />';

			}

		}

		$contents_messages .= '<figure class="revolver__messages-avatar">';
		$contents_messages .= $avatar;
		$contents_messages .= '</figure>';

		$contents_messages .= '<div class="revolver__messages-body">';
		$contents_messages .= '<header><b>'. TRANSLATIONS[ $ipl ]['Message from'] .' '. $v['from'] .'</b> <time>'. $v['time'] .'</time></header>';

		$contents_messages .= '<div class="revolver__messages-text">'. $markup::Markup( $v['message'], [ 'xhash' => 1 ] ) .'</div>';

		if( (bool)$message_files_count ) {

			$contents_messages .= $message_files . '</ul></label>';

		}

		$contents_messages .= $checkBox;

		$contents_messages .= '</div>';
		$contents_messages .= '</dd>';

		$messages_counter++;

	}

}


$contents_messages .= '<input type="hidden" name="revolver_user_action" value="cleanup" />';
$contents_messages .= '</dl>';

// message form parameters
$form_parameters_messages = [

	// main parameters
	'id' 	 => 'messages-form-control',
	'class'	 => 'revolver__messages-form-control revolver__new-fetch',
	'action' => '/user/messages/',
	'method' => 'post',
	'encrypt' => true,
	'submit' => 'Delete messages',

	// included fieldsets
	'fieldsets' => [

		// fieldset contents parameters
		'fieldset_1' => [

			'title' => 'Private Messages',

			'html:contents' => $contents_messages

		],

	]

];

if( $messages_list ) {

	$contents .= $form::build($form_parameters_messages);

}
else {

	$notify::set('notice', 'No messages found for you');

}

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

	$form_parameters_html_help .= '<span class="revolver__allowed-files-size">'. round(

		(int)$allowed_files['max-size'] / 1024, 1, PHP_ROUND_HALF_ODD

	) .' Kb</span>';

	$form_parameters_html_help .= '</li>';

}

$form_parameters_html_help .= '</ul>';

$form_parameters = [

	// main parameters
	'id' 	 => 'message-form',
	'class'	 => 'revolver__message-form revolver__new-fetch',
	'action' => '/user/messages/',
	'method' => 'post',
	'encrypt' => true,
	'captcha' => true,
	'submit' => 'Send',

	// included fieldsets
	'fieldsets' => [

		// fieldset contents parameters
		'fieldset_1' => [

			'title' => 'Write message',
			'collapse' => true,

			// wrap fields into label
			'labels' => [

				'label_1' => [

					'title'  => 'Mail to(destination nickname)',
					'access' => 'messages',
					'auth'	 => 1,

					'fields' => [

						0 => [

							'type' 			=> 'input:text',
							'name' 			=> 'revolver_mailto_nickname',
							'placeholder'	=> 'Type account nickname you want to send mail',
							'required'		=> true,
							'value'			=> ''

						],

					],

				],

				'label_2' => [

					'title'  => 'Message',
					'access' => 'messages',
					'auth'	 => 1,
					'fields' => [

						0 => [

							'type' 			=> 'textarea:text',
							'name' 			=> 'revolver_mailto_message',
							'placeholder'	=> 'Type your message here',
							'required'		=> true,
							'value'			=> '',
							'rows'			=> 20

						],

						1 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_user_name',
							'required'		=> true,
							'value'			=> USER['name']

						],

						2 => [

							'type' 			=> 'input:hidden',
							'name' 			=> 'revolver_user_action',
							'value'			=> 'message'

						],

					],

				],

				'label_3' => [

					'title'  => 'Choose files to upload',
					'access' => 'messages',
					'auth'	 => 1,

					'fields' => [

						0 => [

							'type' 			=> 'input:file',
							'name' 			=> 'revolver_messages_files',
							'multiple'		=> true

						],

					],

				],

				'label_4' => [

					'title'  => 'Allowed files',
					'access' => 'messages',
					'collapse' => true,
					'auth'	 => 1,

					'fields' => [

						0 => [

							'html:contents' => $form_parameters_html_help

						],

					],

				],

			],

		],

	]

];

$contents .= $form::build( $form_parameters );

$node_data[0] = [

	'title'		=> TRANSLATIONS[ $ipl ]['Private messages'],
	'id'		=> 'user-messages',
	'route'		=> '/user/messages/',
	'contents'	=> $contents,
	'teaser'	=> false,
	'footer'	=> false,
	'time'		=> false,
	'published' => 1

];

?>
