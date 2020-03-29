<?php

 /* 
  * 
  * RevolveR Contents Moderate
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

if( in_array( ROLE, ['Admin', 'Writer'] ) ) {

  if( !empty(SV['p']) ) {

    $session_control = null;

    // Moderation users
    if( isset(SV['p']['revolver_session_id']) ) {

      if( (bool)SV['p']['revolver_session_id']['valid'] ) {

        $session_id = SV['p']['revolver_session_id']['value'];

      }

    }

    if( isset(SV['p']['revolver_user_id']) ) {

      if( (bool)SV['p']['revolver_user_id']['valid'] ) {

        $user_id = SV['p']['revolver_user_id']['value'];

        $session_control = true;

      }

    }

    $comments_moderate = [];

    for( $i = 0; $i < 100; $i++ ) {

      if( !isset(SV['p'][ 'revolver_publish_comment_'. $i ]) ) {

        $comments_moderate[ $i ][0] = $i .'::0';

      }
      else {

        if( (bool)SV['p'][ 'revolver_publish_comment_'. $i ]['valid'] ) {

          $comments_moderate[ $i ][0] = $i .'::1';

        }
        else {

          $comments_moderate[ $i ][0] = $i .'::0';

        }

      }

      if( isset( SV['p'][ 'revolver_delete_comment_'. $i ] ) ) {

        if( (bool)SV['p'][ 'revolver_delete_comment_'. $i ]['valid'] ) {

          $comments_moderate[ $i ][1] = SV['p'][ 'revolver_delete_comment_'. $i ]['value'];

        }

      }

    }

    if( isset(SV['p']['revolver_user_id']) ) {

      if( (bool)SV['p']['revolver_user_id']['valid'] ) {

        $user_id = SV['p']['revolver_user_id']['value'];

      }

    }

    if( (bool)count( $comments_moderate ) && !$session_control ) {

      $comments_all = iterator_to_array(

        $model::get('comments', [

          'criterion' => 'id::*',
          'course'    => 'backward',
          'sort'      => 'time'

        ])

      )['model::comments'];

      foreach( $comments_moderate as $n => $m ) {

        $erased = null;

        if( isset($m[1]) ) {

          $model::erase('comments', [

            'criterion' => 'id::'. $m[1]

          ]);

          $erased = true;

          $notify::set('notice', 'Comment '. $m[1] .' erased.', null);

        }

        if( isset( $m[0] ) && !$erased ) {

          if( $comments_all ) {

            foreach( $comments_all as $comment ) {

              $cmd = explode('::', $m[0]);

              if( (int)$comment['id'] === (int)$cmd[0] ) {  

                if( $comment['published'] !== $cmd[1] ) {

                  $update = $comment;

                  $update['content'] = $markup::markup( $comment['content'], [ 'xhash'=> 0 ] );
                  $update['published'] = $cmd[1];
                  $update['criterion'] = 'id';

                  $model::set('comments', $update);

                  if( (bool)$cmd[1] ) {

                    $notify::set('active', 'Comment '. $comment['id'] .' published.', null);

                  }
                  else {

                    $notify::set('inactive', 'Comment '. $comment['id'] .' unpublished.', null);

                  }

                }

              }

            }

          }

        }

      }

    }

    $usl = iterator_to_array(

        $model::get('users', [

        'criterion' => 'id::'. $user_id

      ])

    )['model::users'];

    if( $usl ) {

      $user_to_drop = $usl[0];

      $user_to_drop['criterion'] = 'id';

      $user_to_drop['session_id'] = $session_id;

      $model::set( 'users', $user_to_drop );

      $notify::set('inactive', 'Authorization canceled for user '. $user_id .'.', null);

    }

  }

  $contents .= '<aside id="tabs" class="revolver__tabs tabs">';
  $contents .= '<ul>';
  $contents .= '<li class="revolver__tabs-tab-tab_1 revolver__tabs-tab" data-link="tab-0">'. TRANSLATIONS[ $ipl ]['Users'] .'</li>';
  $contents .= '<li class="revolver__tabs-tab-tab_2 revolver__tabs-tab activetab" data-link="tab-1">'. TRANSLATIONS[ $ipl ]['Comments moderation'] .'</li>';
  $contents .= '</ul>';

  $contents .= '<div data-content="tab-0">';

  $contents .= '<dl class="revolver__users-list">';

  $users = iterator_to_array(

          $model::get('users', [

          'criterion' => 'id::*'

        ])

      )['model::users'];

  if( $users ) {

    foreach( $users as $user ) {

      $contents .= '<dd>';

      $contents .= '<h2>• '. TRANSLATIONS[ $ipl ]['User'] .' :: '. $user['nickname'] .'<span style="float:right;">[ <a title="'. TRANSLATIONS[ $ipl ]['Manage account'] .'" href="/user/'. $user['id'] .'/edit/">'. TRANSLATIONS[ $ipl ]['manage'] .'</a> ]</span></h2>';

      $contents .= '<img class="revolver__user-info-avatar" src="'. ( $user['avatar'] === 'default' ? '/public/avatars/default.png' : $user['avatar'] ) .'" alt="'. $user['nickname'] .' '. TRANSLATIONS[ $ipl ]['User profile'] .'" />';

      $contents .= '<ul class="revolver__user-info">';

      $c = 0;

      foreach( $user as $u => $n ) {

        if( !in_array($u, ['id', 'password', 'interface_language', 'session_id', 'avatar'] ) ) {

          $contents .= '<li><span class="marker">'. ucfirst( $u ) .'</span> <strong>'. $n .'</strong></li>';

        }

      }

      $contents .= '</ul>';

      $session_form_params = [

        'id'      => 'auth-form-'. $c++,
        'class'   => 'revolver__session_form revolver__new-fetch',
        'action'  => '/moderation/',
        'method'  => 'POST',
        'captcha' => null,
        'encrypt' => true,
        'submit'  => 'Tear off session',

        'fieldsets' => [

          'fieldset_1' => [

            'title' => 'Tear off session',

            'labels' => [

              'label_1' => [

                'title'  => 'Tear off session',

                'access' => 'profile',

                'auth'   => 1,

                'fields' => [

                  0 => [

                    'type'        => 'input:hidden',
                    'name'        => 'revolver_session_id',

                    'required'    => true,

                    'value'       => 'no-session-id'

                  ],

                  1 => [

                    'type'        => 'input:hidden',
                    'name'        => 'revolver_user_id',

                    'required'    => true,

                    'value'       => $user['id']

                  ]

                ],

              ],

            ],

          ],

        ]

      ];

      $contents .= $form::build( $session_form_params );

      $contents .= '</dd>';

    }

  }

  $contents .= '</dl>';
  $contents .= '</div>';

  $contents .= '<div data-content="tab-1" class="tabactive">';

  $comments = iterator_to_array(

    $model::get('node->comment', [

      'criterion' => 'comments::id::*',
      'course'    => 'backward',
      'sort'      => 'id'

    ])

  )['model::node->comment'];

  if( $comments ) {

    // Moderation form configure
    $moderation_form_params = [

      'id'      => 'moderation-form',
      'class'   => 'revolver__moderation_form revolver__new-fetch',
      'action'  => '/moderation/',
      'method'  => 'POST',
      'captcha' => null,
      'encrypt' => true,
      'submit'  => 'Moderate'

    ];

    $n = 0;

    $moderation_form_params['fieldsets']['fieldset_0']['labels']['label_0']['title:html'];

    foreach( array_reverse($comments) as $c ) {

      $published = (bool)$c['comments']['published'] ? 'published' : 'unpublished';

      $avatar = '/public/avatars/';

      if( (int)$c['comments']['user_id'] === BigNumericX64 ) {

        $avatar .= 'default.png';

      }
      else {

        $user_avatar = iterator_to_array(

          $model::get('users', [

            'criterion' => 'id::'. $c['comments']['user_id']

          ])

        )['model::users'][0]['avatar'];

        $avatar .= $user_avatar === 'default' ? $user_avatar .'.png' : $user_avatar; 

      }

      $contents_comment  = '<article style="display:inline-block" id="comment-'. $c['comments']['id'] .'" class="revolver__comments comments-'. $c['comments']['id'] .' '. $published .'">';
      $contents_comment .= '<header class="revolver__comments-header">';

      $contents_comment .= '<h2>• '. TRANSLATIONS[ $ipl ]['Comment'] .' '. $c['comments']['id'] .' '. TRANSLATIONS[ $ipl ]['by'] .' <span>'. $c['comments']['user_name'] .'</span></h2>';
      $contents_comment .= '<time>'. $c['comments']['time'] .'</time>';

      $contents_comment .= '</header>';

      $contents_comment .= '<figure class="revolver__comments-avatar">';
      $contents_comment .= '<img src="'. $avatar .'" alt="'. TRANSLATIONS[ $ipl ]['Account avatar'] .' '. $c['comments']['user_name'] .'" />';
      $contents_comment .= '</figure>';

      $contents_comment .= '<div class="revolver__comments-contents">';

      $contents_comment .= $markup::markup(

        $c['comments']['content'], [ 'xhash' => 1 ]

      );

      $contents_comment .= '</div>';

      $contents_comment .= '<div class="revolver__moderation-flags">';
      $contents_comment .= '<label id="label_publish_'. $n .'">'. TRANSLATIONS[ $ipl ]['Published'] .':';

      $contents_comment .= '<input type="hidden" name="revolver_publish_comment_0" value="0::1" />';

      $contents_comment .= '<input type="checkbox" name="revolver_publish_comment_'. $c['comments']['id'] .'" value="'. $c['comments']['id'] .'::'. ( (bool)$c['comments']['published'] ? 1 : 0 ) .'" '. ( (bool)$c['comments']['published'] ? 'checked="checked"' : '' ) .' />';

      $contents_comment .= '</label>';

      $contents_comment .= '<label id="label_delete_'. $n .'">'. TRANSLATIONS[ $ipl ]['Delete comment'] .':';
      $contents_comment .= '<input type="checkbox" name="revolver_delete_comment_'. $c['comments']['id'] .'" value="'. $c['comments']['id'] .'" />';
      $contents_comment .= '</label>';

      $contents_comment .= '<a title="Сomment '. $c['comments']['id'] .' Edit" href="/comment/'. $c['comments']['id'] .'/edit/">[ Edit ]</a>';

      $contents_comment .= '</div>';
      $contents_comment .= '</article><br /><br />';

      $moderation_form_params['fieldsets']['fieldset_0']['title'] = 'Latest comments';
      $moderation_form_params['fieldsets']['fieldset_0']['labels']['label_0']['title'] = 'Moderation';
      $moderation_form_params['fieldsets']['fieldset_0']['labels']['label_0']['access'] = 'node';
      $moderation_form_params['fieldsets']['fieldset_0']['labels']['label_0']['auth'] = 1;

      $moderation_form_params['fieldsets']['fieldset_0']['labels']['label_0']['fields'][0]['html:contents'] .= $contents_comment;

      $n++;

    }

    $contents .= $form::build( $moderation_form_params );

  }
  else {

    $contents .= '<p>No any comments to moderate now.</p>';

  }

  $contents .= '</div>';
  $contents .= '</aside>';

}

$node_data[0] = [

	'title'      => TRANSLATIONS[ $ipl ]['Moderation'],
	'id'         => 'moderation',
	'route'      => '/moderation/',
	'contents'   => $contents,
	'teaser'     => false,
	'footer'     => false,
	'time'       => false,
	'published'  => 1

];

?>
