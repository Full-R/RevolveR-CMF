<?php

 /* 
  * 
  * RevolveR Node User
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

$flag_pass = null;

foreach( iterator_to_array(

			$model::get( 'users', [

				'criterion' => 'id::*',
				'course'	=> 'forward',
				'sort'		=> 'id'

			])

		)['model::users'] as $user ) {

	$token_explode = explode('|', $cipher::crypt('decrypt', SV['c']['usertoken']));

	if( $user['email'] === $token_explode[0] ) {

		if( $user['password'] === $token_explode[1] ) {

			$user_name = $user['nickname'];
			$user_email = $user['email'];
			$user_id = $user['id'];
			$user_permissions = $user['permissions'];
			$user_password = $user['password'];
			$avatar = $user['avatar'];
			$telephone = $user['telephone'];
			$session_id = $user['session_id'];

		}

	}

}

/* Edit profile */
if( !empty( SV['p'] ) && ROLE !== 'none') {

	$user_telephone = '';

	if( isset(SV['p']['revolver_user_name']) ) {

		if( (bool)SV['p']['revolver_user_name']['valid'] ) {

			$name = SV['p']['revolver_user_name']['value'];

			$flag_pass = true;

		}

		else {

			$flag_pass = null;

		}

	}
	else {

		$flag_pass = null;

	}

	if( isset(SV['p']['revolver_user_email']) ) {

		if( (bool)SV['p']['revolver_user_email']['valid'] ) {

			$user_email = SV['p']['revolver_user_email']['value'];

			$flag_pass = true;

		}
		else {

			$flag_pass = null;

		}

	}
	else {

		$flag_pass = null;

	}

	if( isset(SV['p']['revolver_user_telephone']) ) {

		if( (bool)SV['p']['revolver_user_telephone']['valid'] ) {

			$user_telephone = SV['p']['revolver_user_telephone']['value'];

		}

	}

	if( isset(SV['p']['revolver_user_old_password']) ) {

		if( (bool)SV['p']['revolver_user_old_password']['valid'] ) {

			$user_old_password = SV['p']['revolver_user_old_password']['value'];

			$flag_pass = true;

		}
		else {

			$flag_pass = null;

		}

	}
	else {

		$flag_pass = null;

	}

	if( isset(SV['p']['revolver_user_new_password']) ) {

		if( (bool)SV['p']['revolver_user_new_password']['valid'] ) {

			$revolver_user_new_password = SV['p']['revolver_user_new_password']['value'];

			$flag_pass = true;

		}
		else {

			$flag_pass = null;

		}

	}
	else {

		$flag_pass = null;

	}

	if( isset(SV['p']['revolver_user_confirm_password']) ) {

		if( (bool)SV['p']['revolver_user_confirm_password']['valid'] ) {

			$revolver_user_confirm_password = SV['p']['revolver_user_confirm_password']['value'];

			$flag_pass = true;

		}
		else {

			$flag_pass = null;

		}

	}
	else {

		$flag_pass = null;

	}

	if( isset(SV['p']['revolver_interface_code']) ) {

		if( (bool)SV['p']['revolver_interface_code']['valid'] ) {

			$interface_language = SV['p']['revolver_interface_code']['value'];

			$flag_pass = true;

		} 
		else {

			$flag_pass = null;

		}

	}
	else {

		$flag_pass = null;

	}

}

if( $flag_pass ) {

	$upload = 'default';

	if( (bool)count(SV['f']) ) {

		if( isset(SV['f']['revolver_user_avatar-0'][0]) ) {

			if( (bool)SV['f']['revolver_user_avatar-0'][0]['valid'] ) {

				if( in_array(SV['f']['revolver_user_avatar-0'][0]['type'][1], ['jpg', 'jpeg', 'png', 'webp']) ) {

					$upload = 'public/avatars/'. SV['f']['revolver_user_avatar-0'][0]['name'];

					move_uploaded_file( SV['f']['revolver_user_avatar-0'][0]['temp'], $upload );

					$upload = '/'. $upload;

				}

			}

		}

	}

	if( $user_old_password === $cipher::crypt('decrypt', $user_password) ) {

		if( $revolver_user_new_password === $revolver_user_confirm_password ) { 
				
			$user_password = $cipher::crypt('encrypt', $revolver_user_confirm_password);

			//$notify::set('status', TRANSLATIONS[$ipl]['Accaunt password changed'] .' '. $revolver_user_confirm_password, null);

		} 
		else {

			$notify::set('inactive', 'Passwords not mutch. Stored old password');

		}

	} 
	else {

		$notify::set('inactive', 'Passwords not mutch. Stored old password');

	}
	
	// Reset user token
	$auth::setCookie([

		[ 'usertoken', $cipher::crypt('encrypt', $user_email .'|'. $user_password .'|'. $name ), time() + 10600, '/' ]

	]);

	$model::set('users', [

		'id'				 => $user_id,
		'nickname'			 => $name,
		'email' 			 => $user_email,
		'password' 			 => $user_password,
		'telephone'			 => $user_telephone,
		'interface_language' => $interface_language,
		'permissions' 		 => $user_permissions,
		'avatar' 			 => $upload,
		'session_id'		 => $session_id,
		'criterion'			 => 'id'

	]);

	$avatar = $upload;

	$notify::set('status', 'Profile updated');

}

$title = TRANSLATIONS[ $ipl ]['User profile'] .' • '. $user_name;

// TAB-1 PLAIN HTML
$form_parameters_tab_1_html .= '<figure class="revolver__user-profile-avatar">';


if( $avatar === 'default') {

	$form_parameters_tab_1_html .= '<img src="/public/avatars/default.png" alt="'. $user_name .'" />';

} 
else {

	$form_parameters_tab_1_html .= '<img src="'. $avatar .'" alt="'. $user_name .'" />';

}

$form_parameters_tab_1_html .= '<figcaption>';

$form_parameters_tab_1_html .= '<p>'. TRANSLATIONS[ $ipl ]['User Name'] .': <i>'. $user_name .'</i></p>';
$form_parameters_tab_1_html .= '<p>'. TRANSLATIONS[ $ipl ]['User Email'] .': <i>'. $user_email .'</i></p>';
$form_parameters_tab_1_html .= '<p>'. TRANSLATIONS[ $ipl ]['Telephone'] .': <i>'. $telephone .'</i></p>';
$form_parameters_tab_1_html .= '<p>'. TRANSLATIONS[ $ipl ]['Permissions'] .': <i>'. $user_permissions .'</i></p>';

$form_parameters_tab_1_html .= '</figcaption>';

$form_parameters_tab_1_html .= '</figure><br style="clear:both" />';

// User Profile Form Structure
$form_parameters = [

	// main parameters
	'id' 	  => 'profile-form',
	'class'	  => 'revolver__profile-form revolver__new-fetch',
	'action'  => '/user/',
	'method'  => 'post',
	'encrypt' => true,
	'submit'  => 'Set',

	// tabs
	'tabs' => [

		'tab_1' => [

			// tab title
			'title'  => 'Account info',
			'active' => true,

			// included fieldsets
			'html' => $form_parameters_tab_1_html,

		], // #tab 1

		'tab_2' => [

			// tab title
			'title' => 'Profile settings',

			// included fieldsets
			'fieldsets' => [

				// fieldset contents parameters
				'fieldset_2' => [

					'title' => 'Edit account details',

					// wrap fields into label
					'labels' => [

						'label_1' => [

							'title'  =>  'Account nickname',
							'access' => 'profile',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type'			=> 'input:text',
									'name'			=> 'revolver_user_name',
									'placeholder'	=> 'Type your nickname here',
									'required'		=> true,
									'value'			=> $user_name,
									'readonly'		=> true

								],

							],

						],

						'label_2' => [

							'title'  =>  'Account avatar',
							'access' => 'profile',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type'			=> 'input:file',
									'name'			=> 'revolver_user_avatar',
									'multiple'		=> true

								],

							],

						],

						'label_3' => [

							'title'  =>  'Email',
							'access' => 'profile',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type'			=> 'input:email',
									'name'			=> 'revolver_user_email',
									'placeholder'	=> 'Type your email here',
									'required'		=> true,
									'value'			=> $user_email

								],

							],

						],

						'label_4' => [

							'title'  =>  'Telephone',
							'access' => 'profile',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type'			=> 'input:tel',
									'name'			=> 'revolver_user_telephone',
									'placeholder'	=> 'Type your telephone number here',
									'value'			=> $telephone

								],

							],

						],

					],

				],

				// fieldset password parameters
				'fieldset_3' => [

					'title' => 'Change account password',
					'collapse' => true,

					// wrap fields into label
					'labels' => [

						'label_5' => [

							'title'  =>  'Old user password',
							'access' => 'profile',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type'			=> 'input:password',
									'name'			=> 'revolver_user_old_password',
									'placeholder'	=> 'Old user password',
									'required'		=> true,
									'value'			=> $cipher::crypt( 'decrypt', $user_password )

								],

							],

						],

						'label_6' => [

							'title'  =>  'New user password',
							'access' => 'profile',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type'			=> 'input:password',
									'name'			=> 'revolver_user_new_password',
									'placeholder'	=> 'New user password',
									'required'		=> true,
									'value'			=> $cipher::crypt( 'decrypt', $user_password )

								],

							],

						],

						'label_7' => [

							'title'  =>  'Confirm password',
							'access' => 'profile',
							'auth'   => 1,

							'fields' => [

								0 => [

									'type'			=> 'input:password',
									'name'			=> 'revolver_user_confirm_password',
									'placeholder'	=> 'Confirm password',
									'required'		=> true,
									'value'			=> $cipher::crypt( 'decrypt', $user_password )

								],

							],

						],

					],

				],

			],

		], // #tab 1

		'tab_3' => [

			// tab title
			'title' => 'Interface language',

			// included fieldsets
			'fieldsets' => [

				// fieldset contents parameters
				'fieldset_4' => [

					'title' => 'Website interface language'

				],

			],

		], // #tab 3

	]

];

// TAB-3 Interface language
$c = 0;

foreach( TRANSLATIONS as $i => $l ) {

	foreach( $lang::getLanguageData('*') as $cn ) {

		if( $cn['code_length_2'] === $i || $cn['code_length_2'] === 'US' ) {

			$form_parameters['tabs']['tab_3']['fieldsets']['fieldset_4']['labels'][ 'label_lng_'. $c ]['title:html'] = TRANSLATIONS[ $i ]['Language'] .' <span class="revolver__stats-system">[ '. $cn['code_length_3'] .' :: '. $cn['code_length_2'] .' :: '. $cn['hreflang'] .' ]</span> <i class="state-attribution laguage-list-item revolver__sa-iso-'. strtolower( $cn['code_length_2'] ) .'"></i>'. TRANSLATIONS[ $i ]['country'] .' <span class="revolver__stats-country">['. $cn['name'] .']</span>';

		}

	}

	$form_parameters['tabs']['tab_3']['fieldsets']['fieldset_4']['labels'][ 'label_lng_'. $c ]['access'] = 'profile';
	$form_parameters['tabs']['tab_3']['fieldsets']['fieldset_4']['labels'][ 'label_lng_'. $c ]['auth'] = 1;

	$form_parameters['tabs']['tab_3']['fieldsets']['fieldset_4']['labels'][ 'label_lng_'. $c ]['fields'][0] = [

		'type'  => 'input:radio:'. ( $i === USER['language'] ? 'checked' : 'unchecked' ),
		'name'  => 'revolver_interface_code',
		'value' =>  $i

	];

	$c++;

}

$contents = $form::build( $form_parameters, true );

$node_data[0] = [

	'title'		=> $title,
	'id'		=> 'user-profile',
	'route'		=> '/user/',
	'contents'	=> $contents,
	'teaser'	=> false,
	'footer'	=> false,
	'time'		=> false,
	'published' => 1

];

?>
