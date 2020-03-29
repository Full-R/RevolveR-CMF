<?php

if( ACCESS['role'] === 'Admin' ) {

	foreach( iterator_to_array(

		$model::get( 'users', [

			'criterion' => 'id::*',
			'course'	=> 'forward',
			'sort' 		=> 'id'

		])

	)['model::users'] as $k => $v ) {

		if( PASS[ 2 ] == $v['id'] ) {

			$render_node .= '<article class="revolver__article">';
			$render_node .= '<header class="revolver__article-header">'; 
			$render_node .= '<h1>'. TRANSLATIONS[ $ipl ]['Account manage'] .' :: '. $v['nickname'] .'</h1>';
			$render_node .= '</header>';
			$render_node .= '<div class="revolver__article-contents">';
			$render_node .= '<figure class="revolver__user-profile-avatar">';

			if( $v['avatar'] === 'default') {

				$render_node .= '<img src="/public/avatars/default.png" alt="'. $v['nickname'] .'" />';

			}
			else {

				$render_node .= '<img src="'. $v['avatar'] .'" alt="'. $v['nickname'] .'" />';

			}

			$render_node .= '<figcaption>';

			$render_node .= '<p>'. TRANSLATIONS[ $ipl ]['User Name'] .': <i>'. $v['nickname'] .'</i></p>';
			$render_node .= '<p>'. TRANSLATIONS[ $ipl ]['User Email'] .': <i>'. $v['email']  .'</i></p>';
			$render_node .= '<p>'. TRANSLATIONS[ $ipl ]['Telephone'] .': <i>'. $v['telephone']  .'</i></p>';
			$render_node .= '<p>'. TRANSLATIONS[ $ipl ]['Permissions'] .': <i>'. $v['permissions'] .'</i></p>';

			$render_node .= '</figcaption>';
			$render_node .= '</figure>';

			$roles_allowed = iterator_to_array(

				$model::get( 'roles', [

					'criterion' => 'id::*',
					'course'	=> 'forward',
					'sort' 		=> 'id'

				])

			)['model::roles'];

			$roles = ['User', 'Admin', 'Writer', 'Banned'];

			if( $v['permissions'] === 'Admin' ) {

				$roles = ['User', 'Admin', 'Writer'];

			}

			$render_node_options = '';

			foreach( $roles as $r ) {

				$render_node_options .= ( $v['permissions'] === $r ) ? '<option value="'. $r .'" selected="selected">'. $r .'</option>' : '<option value="'. $r .'">'. $r .'</option>';

			}

			$form_parameters = [

				// Main parameters
				'id' 	  => 'profile-edit-form',
				'class'	  => 'revolver__profile-edit-form revolver__new-fetch',
				'action'  => '/user-d/',
				'method'  => 'post',
				'encrypt' => true,
				'captcha' => true,
				'submit'  => 'Set',

				// Include fieldsets
				'fieldsets' => [

					// Fieldset contents parameters
					'fieldset_1' => [

						'title' => 'Edit permissions',

						// Wrap fields into label
						'labels' => [

							'label_1' => [

								'title'  => 'Role',
								'access' => 'profile',
								'auth'	 => 1,

								'fields' => [

									0 => [

										'type' 			=> 'select',
										'name' 			=> 'revolver_user_edit_role',
										'required'		=> true,
										'value:html' 	=> $render_node_options

									],

									1 => [

										'type' 			=> 'input:hidden',
										'name' 			=> 'revolver_user_edit_id',
										'required'		=> true,
										'value' 		=> $v['id']

									],

								],

							],

						],

					],

				]

			];

			$render_node .= $form::build( $form_parameters );

			$render_node .= '</div>';
			$render_node .= '</article>';

		}

	}

}

?>
