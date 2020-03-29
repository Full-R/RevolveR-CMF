<?php

 /* 
  * 
  * RevolveR Route Comment Dispatch
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

if( in_array(ROLE, ['Admin', 'Writer', 'User'], true) || (int)USER['id'] === BigNumericX64 ) {

	if( !empty(SV['p']) ) {

		$advanced_action = 'update';

		$form_pass = null;

		$subscribe = null;

		$action = null;

		$published = 0;

		$contents = '';

		if( in_array(ROLE, ['Admin', 'Writer', 'User'], true) ) {

			if( isset(SV['p']['revolver_comment_id']) ) {

				if( (bool)SV['p']['revolver_comment_id']['valid'] ) {

					$comment_id = SV['p']['revolver_comment_id']['value'];

				}

			}

			if( isset(SV['p']['revolver_comments_action_edit']) ) {

				if( (bool)SV['p']['revolver_comments_action_edit']['valid'] ) {

					$action = 'edit';

				}

			}

			if( isset(SV['p']['revolver_comments_action_delete']) ) {

				if( (bool)SV['p']['revolver_comments_action_delete']['valid'] ) {

					$advanced_action = 'delete';

				}

			}

			if( isset(SV['p']['revolver_comments_published']) ) {

				if( (bool)SV['p']['revolver_comments_published']['valid'] ) {

					$published = 1;

				}

			}

		}

		if( isset(SV['p']['revolver_comment_node_author']) ) {

			if( (bool)SV['p']['revolver_comment_node_author']['valid'] ) {

				$mailTo = SV['p']['revolver_comment_node_author']['value'];

			}

		}

		if( isset(SV['p']['revolver_comment_node_route']) ) {

			if( (bool)SV['p']['revolver_comment_node_route']['valid'] ) {

				$node_route = SV['p']['revolver_comment_node_route']['value'];

			}

		}

		if( isset(SV['p']['revolver_comment_content']) ) {

			if( (bool)SV['p']['revolver_comment_content']['valid'] ) {

				$contents = $markup::Markup(

					SV['p']['revolver_comment_content']['value'], [ 'xhash' => 0 ]

				);

			}

		}

		if( isset(SV['p']['revolver_comment_time']) ) {

			if( (bool)SV['p']['revolver_comment_time']['valid'] ) {

				$time = SV['p']['revolver_comment_time']['value'];

			}

		}

		if( isset(SV['p']['revolver_comment_user_id']) ) {

			if( (bool)SV['p']['revolver_comment_user_id']['valid'] ) {

				$user_id = SV['p']['revolver_comment_user_id']['value'];

			}

		}

		if( isset(SV['p']['revolver_node_id']) ) {

			if( (bool)SV['p']['revolver_node_id']['valid'] ) {

				$node_id = SV['p']['revolver_node_id']['value'];

			}

		}

		if( isset(SV['p']['revolver_comment_user_name']) ) {

			if( (bool)SV['p']['revolver_comment_user_name']['valid'] ) {

				$user_name = SV['p']['revolver_comment_user_name']['value'];

				$auth::setCookie([

					[ 'user_name', $user_name, time() + 86400, '/' ]

				]);

			}

		}

		if( isset(SV['p']['revolver_comment_node_country']) ) {

			if( (bool)SV['p']['revolver_comment_node_country']['valid'] ) {

				$country = SV['p']['revolver_comment_node_country']['value'];

			}

		}

		// Subscriptions
		if( isset(SV['p']['revolver_comment_user_email']) ) {

			if( (bool)SV['p']['revolver_comment_user_email']['valid'] ) {

				$user_email = SV['p']['revolver_comment_user_email']['value'];        

				$auth::setCookie([

					[ 'user_email', SV['p']['revolver_comment_user_email']['value'], time() + 86400, '/' ]

				]);

			}

		}

		if( isset(SV['p']['revolver_comment_subscription']) ) {

			if( (bool)SV['p']['revolver_comment_subscription']['valid'] ) {

				$subscribe = true;

			}

		} 

		if( isset(SV['p']['revolver_captcha']) ) {

			if( (bool)SV['p']['revolver_captcha']['valid'] ) {

				if( Auth ) {

					if( $captcha::verify( SV['p']['revolver_captcha']['value'] ) ) {

						$form_pass = true;

					}

				}
				else {

					$users = iterator_to_array(

						$model::get( 'users', [

							'criterion' => 'email::'. $user_email,

							'bound'		=> [

								1

							],

							'course'	=> 'backward',
							'sort'		=> 'id'

						])

					)['model::users'];

					if( !$users ) {

						if( $captcha::verify( SV['p']['revolver_captcha']['value'] ) ) {

							$form_pass = true;

						}

					}

				}

			}

		}

	}

	if( $action === 'edit' ) {

		if( $advanced_action === 'delete' && $form_pass ) {

			// Delete comment
			$model::erase('comments', [

				'criterion' => 'id::'. $comment_id

			]);

			header('Location: '. site_host . $node_route .'?notification=comment-erased^'. $comment_id);

		}
		else {

			if( $form_pass && (bool)strlen( $contents ) ) {

				$model::set('comments', [

					'id'		=> $comment_id,
					'user_id'	=> $user_id,
					'node_id'	=> $node_id,
					'content'	=> $contents,
					'time'		=> date('d.m.Y h:m'),
					'published'	=> $published,

					'criterion'	=> 'id'

				]);

				header('Location: '. site_host . $node_route .'?notification=comment-updated^'. $comment_id .'#comment-'. $comment_id );

			}
			else {

				header('Location: '. site_host . '/comment/'. $comment_id .'/edit/?notification=no-changes' );

			}

		}

	}
	else {

		if( $form_pass ) {

			$model::set('comments', [

				'user_id'	=> $user_id,
				'node_id'	=> $node_id,
				'country'	=> $country,
				'user_name'	=> $user_name,
				'content'	=> $contents,
				'time'		=> date('d.m.Y h:m'),

				'published'	=> (int)USER['id'] === BigNumericX64 ? 0 : 1

			]);

			$email_users = iterator_to_array(

				$model::get( 'users', [

					'criterion' => 'nickname::'. $mailTo,

					'bound'		=> [

						1

					],

					'course'	=> 'forward',
					'sort'		=> 'id'

				])

			)['model::users'];

			if( $email_users ) {

				$user_id_to = $email_users[0]['id'];

				$email  = '<p>'. TRANSLATIONS[ $ipl ]['Posted'];

				$email .= ' <a title="'. TRANSLATIONS[ $ipl ]['new comment'] .'" href="'. site_host . $node_route .'">';

				$email .= TRANSLATIONS[ $ipl ]['new comment'];

				$email .= '</a>!</p>';

				$mail::send( 

					$email_users[0]['email'], TRANSLATIONS[ $ipl ]['New comment for you contents'], $email

				);

			}

			$notify = iterator_to_array(

				$model::get( 'subscriptions', [

					'criterion'	=> 'node_id::'. $node_id,
					'course'	=> 'backward',
					'sort'		=> 'id'

				])

			)['model::subscriptions'];

			$subscribed = null;

			if( $notify ) {

				foreach( $notify as $n ) {

					if( $n['user_email'] === $user_email ) {

						if( $subscribe ) {

							if( !$subscribed ) {

								// Update if exist
								$model::set('subscriptions', [

									'id'			=> $n['id'],
									'user_id'		=> $user_id,
									'node_id'		=> $node_id,

									'user_name'		=> $user_name,
									'user_email'	=> $user_email,

									'criterion'		=> 'id'

								]);

								$subscribed = true;

							}

						}
						else {

							// Delete if exist
							$model::erase('subscriptions', [

								'criterion' => 'id::'. $n['id']

							]);

							$subscribed = null;

						}

					}

				}

			}

			if( $subscribe ) {

				if( !$subscribed ) {

					// Add if not exist
					$model::set('subscriptions', [

						'user_id'		=> $user_id,
						'node_id'		=> $node_id,

						'user_name'		=> $user_name,
						'user_email'	=> $user_email

					]);

				}

			}

			$model::set('messages', [

				'user_id'	=> $user_id_to,
				'to'		=> $mailTo,
				'from'		=> $user_name,

				'message'	=> $markup::Markup(

				'<p>'. TRANSLATIONS[ $ipl ]['Hello'] .', '. $mailTo .'! '. TRANSLATIONS[ $ipl ]['Posted'] .' <a title="'. TRANSLATIONS[ $ipl ]['new comment'] .'" href="'. $node_route .'">'. TRANSLATIONS[ $ipl ]['new comment'] .'</a>!</p>', [ 'xhash' => 0 ]

				),

				'time' => date('d.m.Y h:m')

			]);

			// Send subscription notification
			if( $notify ) {

				foreach( $notify as $n ) {

					$notification  = '<p>'. TRANSLATIONS[ $ipl ]['Posted'] .' <a title="'. TRANSLATIONS[ $ipl ]['new comment'] .'" href="'. site_host . $node_route .'">';
					$notification .= TRANSLATIONS[ $ipl ]['new comment'];
					$notification .= '</a>!</p>';

					$mail::send( 

						$n['user_email'],

						$n['user_name'] .'! '. TRANSLATIONS[ $ipl ]['New comment for you subscription'], 

						$notification

					);

				}

			}

			header( 'Location: '. site_host . $node_route .'?notification=comment-added^'. ($subscribed ? 'subscribed' : 'not-subscribed'));

		} 
		else {

			header( 'Location: '. site_host . $node_route .'?notification=no-changes' );

		}

	}

}

print '<!-- comments dispatch -->';

define('serviceOutput', [

	'ctype'	=> 'text/html',
	'route'	=> '/comments-d/'

]);

?>
