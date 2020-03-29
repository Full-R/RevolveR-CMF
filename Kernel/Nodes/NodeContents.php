<?php

 /* 
  * 
  * RevolveR Contents Moderate
  *
  * v.1.7.0
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
  * License: Apache 2.0
  *
  */

//var_dump( $lang::getLanguageData( $node['country'] ) );

/*
$dbx::query('index', 'revolver__settings', $STRUCT_SITE);
$dbx::query('index', 'revolver__files', $STRUCT_FILES);
$dbx::query('index', 'revolver__messages_files', $STRUCT_MESSAGES_FILES);
$dbx::query('index', 'revolver__categories', $STRUCT_CATEGORIES);
$dbx::query('index', 'revolver__users', $STRUCT_USER);
$dbx::query('index', 'revolver__roles', $STRUCT_ROLES);

$dbx::query('index', 'revolver__comments', $STRUCT_COMMENTS);
$dbx::query('index', 'revolver__subscriptions', $STRUCT_SUBSCRIPTIONS);

$dbx::query('index', 'revolver__messages',  $STRUCT_MESSAGES);
$dbx::query('index', 'revolver__nodes', $STRUCT_NODES); 
$dbx::query('index', 'revolver__statistics', $STRUCT_STATISTICS); 
*/

//$dbx::query('c', 'revolver__test', $STRUCT_STATISTICS );
//$dbx::query('xc', 'revolver__test', $STRUCT_STATISTICS );

//$dbx::query('d', 'revolver__test', $STRUCT_STATISTICS );
//$dbx::query('d', 'revolver__noindexes', $STRUCT_STATISTICS );

//$dbx::query('index', 'revolver__nodes', $STRUCT_NODES);

//$model = new Model( $dbx, $DBX_KERNEL_SCHEMA );

//print '<h1>Model set</h1>';

/*

$model::set('test', [

	'id'			=> 3,
	'date'  		=> 'Updated string',
	'time'  		=> 'test time',
	'track' 		=> 'test track',
	'route' 		=> '/test/',
	'user_agent'	=> 'User Agent ____ 123',
	'ip'			=> '127.0.0.1',
	'referer'		=> 'test referer'

]);

*/

/*
print '<h1>Model erase</h1>';

$model::erase('test', [

	'criterion' => 'id::6'

]);

*/

print '<h1>Node category</h1>';

print '<pre>'. print_r(

	iterator_to_array(

		$model::get('category->node', [

			'criterion' => 'categories::id::*'

		], null ), // true || null for output format of assemble || stack

	), true

) .'</pre>';

print '<h1>Node comment without user</h1>';

print '<pre>'. print_r(

	iterator_to_array(

		$model::get( 'node->comment', [

			'criterion' => 'comments::id::1'

		], null ), // true || null for output format of assemble || stack

	), true

) .'</pre>';

print '<h1>Node comment</h1>';

print '<pre>'. print_r(

	iterator_to_array(

		$model::get( 'node->comment->user', [

			'criterion' => 'comments::id::1'

		], null ), // true || null for output format of assemble || stack

	), true

) .'</pre>';

print '<h1>Single Model</h1>';

print '<pre>'. print_r(

	iterator_to_array(

		$model::get( 'nodes', [

			'criterion' => 'user::CyberX',

			'bound'		=> [

				10,   // limit

			],

			'course'	=> 'backward', // backward
			'sort' 		=> 'time',

		], true ), // true || null for output format of assemble || stack

	), true

) .'</pre>';

print '<h1>Node category</h1>';

print '<pre>'. print_r(

	iterator_to_array(

		$model::get( 'category->node->user', [

			'criterion' => 'categories::id::3'

		], true )

	), true

) .'</pre>';


print '<h1>User Role</h1>';

print '<pre>'. print_r(

	iterator_to_array(

		$model::get( 'user->role', [

			'criterion' => 'users::id::1'

		], true ), // true || null for output format of assemble || stack

	), true

) .'</pre>';


print '<h1>User subscription</h1>';

print '<pre>'. print_r(

	iterator_to_array(

		$model::get( 'user->subscription', [

			'criterion' => 'subscriptions::node_id::3'

		], true ), // true || null for output format of assemble || stack

	), true

) .'</pre>';


/*
$message = '<p src="data:text/html,%3C%73%63%72%69%70%74%3E%61%6C%65%72%74%2F%73%63%72%69%70%74%3E" style="&#X03C;&#X0003C;&#X03C;j&#X41;vascript: ################### background: url(\'\{006a}\0020\u0061\u0076\u0061\u0073\u0063\u0072\u0069\u0070\u0074\u003aProBE\')">見実生 ヒエログリフ ヒエラグリフ 表意文字 象形文字</p><style>head#lohi {contents: \'\';}</style><p>Проверка письма</p><strong><a href="data:text/css;base64,Ym9keSB7DQogIGNvbG9yOnJlZA0KfQ==">probe</a><i>ثيكفاييصشىظكتففلةةةةةةةلاضشن٣٣وحة ا</i></strong><strong>1<i>123<b>21</b><meta charset="utf-8" /><b><a>123S</a></b></i></strong><svg><xml></xml></svg><meta charset="utf-8" />
<p><span>test <b>some bold</b> <i>test</i> <meta /></span></p><p>proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui <b style="color:#006000">officia</b> deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt <span style="color:#006400; text-decoration: underline">mollit anim id est</span> laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum proident, sunt in culpa qui officia deserunt mollit anim id est laborum .</p><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod</p>
<p>tempor incididunt ut labore et dolore magna <span style="color:#b00000">aliqua</span>. Ut enim ad minim veniam,</p>
<p class="test parapraph" style="backgroud-image: url(/asdasd/asdsad.png)">quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo</p>
<p>consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse</p>
<p>cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non</p>
<h1 style="background-image: url(\'data:image/gif;base64,amEgVmEgU0NSIGkgICAgcCAgICAgVDphbGVydCgiWFNTIik=\');">TEST</h1><IMG SRC="&#x6A;&#x61;&#x76&#x61&#x73&#x63&#x72&#x69&#x70&#x74&#x3A&#x61&#x6C&#x65&#x72&#x74&#x28&#x27&#x58&#x53&#x53&#x27&#x29" /> <IMG SRC="&#0000106&#0000097&#0000118&#0000097&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116&#0000058&#0000097&#0000108&#0000101&#0000114&#0000116&#0000040&#0000039&#0000088&#0000083&#0000083&#0000039&#0000041" /> <IMG SRC="&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;&#58;&#97;&#108;&#101;&#114;&#116;&#40;&#39;&#88;&#83;&#83;&#39;&#41;" />';


$mail::send('unwired.mind.project@gmail.com', 'Test Probe End', $message, ['https://revolver.team/public/uploads/Databe-types-SQL-No-SQL.png', 'https://revolver.team/public/uploads/Xiaomi-Smart-Band-4.jpg']);

if( $mail::$status ) {

	print '<h1>unwired.mind.project@gmail.com</h1>';

}


$mail::send('xcyberx@protonmail.com', 'Test Probe XSS END ', $message, ['https://revolver.team/public/uploads/Databe-types-SQL-No-SQL.png', 'https://revolver.team/public/uploads/Xiaomi-Smart-Band-4.jpg']);

if( $mail::$status ) {
	
	print '<h1>xcyberx@protonmail.com</h1>';

}


$mail::send(default_email, 'Test Probe End', $message, ['https://revolver.team/public/uploads/Databe-types-SQL-No-SQL.png', 'https://revolver.team/public/uploads/Xiaomi-Smart-Band-4.jpg']);

if( $mail::$status ) {

	print '<h1>'. default_email .'</h1>';

}

*/


/*
$form_parameters = [

	// main parameters
	'id' 	 => 'comment-moderation-form',
	'class'	 => 'revolver__comment-moderation-form',
	'action' => '/node/contents/',
	'captcha' => null,	
	'submit' => 'Change',

	// included fieldsets
	'fieldsets' => [
		
		// fieldset contents parameters
		'fieldset_1' => [

			'title' => 'Account login',
			
			// wrap fields into label
			'labels' => [
				'label_1' => [
					'title'  =>  'User Email',
					'access' => 'comment',
					'auth'   => 1,

					'fields' => [
						0 => [
							'type' 			=> 'input:email',
							'name' 			=> 'revolver_login_user_email',
							'placeholder'	=>  'User Email',
							'required'		=> true,
							'value'			=> '',
						],
					],
				],

				'label_2' => [

					'title'  => 'User password',
					'access' => 'comment',
					'auth'   => 1,
					
					'fields' => [
						0 => [
							'type' 			=> 'input:password',
							'name' 			=> 'revolver_login_user_password',
							'placeholder'	=> 'User password',
							'required'		=> true,
							'value'			=> '',
						],
					],
				],
			],
		],
	],
];

//$contents = $form::build($form_parameters);

$contents = '';
$contents .= '<aside id="tabs" class="revolver__tabs tabs">';
$contents .= '<ul>';
$contents .= '<li class="revolver__tabs-tab-tab_1 revolver__tabs-tab activetab" data-link="tab-0">'. $lang::T( '[Unpublished comments]', $interface_primary_language )[0] .'</li>';
$contents .= '<li class="revolver__tabs-tab-tab_2 revolver__tabs-tab" data-link="tab-1">'. $lang::T( '[Unpublished nodes]', $interface_primary_language )[0] .'</li>';
$contents .= '<li class="revolver__tabs-tab-tab_3 revolver__tabs-tab" data-link="tab-2">'. $lang::T( '[Published comments]', $interface_primary_language )[0] .'</li>';
$contents .= '<li class="revolver__tabs-tab-tab_4 revolver__tabs-tab" data-link="tab-3">'. $lang::T( '[Published nodes]', $interface_primary_language )[0] .'</li>';
$contents .= '</ul>';

$contents .= '<div data-content="tab-0">';
$contents .= '';
$contents .= '</div>';

$contents .= '<div data-content="tab-1">';
$contents .= '';
$contents .= '</div>';

$contents .= '<div data-content="tab-2">';


$nodesFieldsCriterion     = ['field_id', 'field_title', 'field_content', 'field_description', 'field_user', 'field_time', 'field_route', 'field_category', 'field_published', 'field_mainpage', 'field_country'];
$catgoriesFieldsCriterion = ['field_id', 'field_title', 'field_description'];
$commentsFieldsCriterion  = ['field_id', 'field_node_id', 'field_user_id', 'field_user_name', 'field_content', 'field_time', 'field_published', 'field_country'];

$catgoriesFieldsCriterion['criterion_table'] = 'revolver__nodes';
$catgoriesFieldsCriterion['criterion_field'] = 'field_category';

$catgoriesFieldsCriterion['linked_table'] = 'revolver__categories';
$catgoriesFieldsCriterion['linked_field'] = 'field_id';

$commentsFieldsCriterion['criterion_table'] = 'revolver__nodes';
$commentsFieldsCriterion['criterion_field'] = 'field_id';

$commentsFieldsCriterion['linked_table'] = 'revolver__comments';
$commentsFieldsCriterion['linked_field'] = 'field_node_id';

$commentsFieldsCriterion['group_field'] = 'field_id';

//$userFieldCriterion['criterion_table'] = 'revolver__nodes';
//$userFieldCriterion['criterion_field'] = 'field_user';

//$userFieldCriterion['linked_table'] = 'revolver__users';
//$userFieldCriterion['linked_field'] = 'field_nickname';

//$subscriptionsFieldsCriterion['criterion_table'] = 'revolver__comments';
//$subscriptionsFieldsCriterion['criterion_field'] = 'field_nickname';

//$subscriptionsFieldsCriterion['linked_table'] = 'revolver__subscriptions';
//$subscriptionsFieldsCriterion['linked_field'] = 'field_user_name';

$criterionFields = [
	$nodesFieldsCriterion,
	$catgoriesFieldsCriterion,
	//$commentsFieldsCriterion,
	//$userFieldCriterion,
	//$userCommentFieldCriterion,
	//$subscriptionsFieldsCriterion,
];


//$userFieldCriterion 		  = ['field_id', 'field_nickname', 'field_avatar'];

//$subscriptionsFieldsCriterion = ['field_id', 'field_node_id', 'field_user_id', 'field_user_name', 'field_user_email', 'field_subscription_enabled'];

// unset( $dbx::$result['result'] );

$dbx::query('xj', 'revolver__nodes|revolver__categories', |revolver__comments', 'revolver__subscriptions' $criterionFields);

print '<pre>';
print_r( $dbx::$result );
print '</pre>';

//print '<pre>';
//print_r( $dbx::$result['status'] );
//print '</pre>';

foreach ($dbx::$result['result'] as $cv) {

	//print '<pre>';
	//print_r( $cv );
	//print '</pre>';

	if( (int)$cv['revolver__comments']['field_published'] > 0 ) {


		$contents .= '<article itemprop="comment" itemscope itemtype="http://schema.org/UserComments" id="'. $cv['revolver__comments']['field_id'] .'" class="revolver__comments moderation-section">';
		$contents .= '<header class="revolver__comments-header">'; 
		$contents .= '<h2 itemprop="creator" itemscope itemtype="http://schema.org/Person"><a href="'. $cv['revolver__nodes']['field_route'] .'#'. $cv['revolver__comments']['field_id'] .'">#'. $cv['revolver__comments']['field_id'] .'</a> '. $lang::T( '[by]', $interface_primary_language )[0] .' <span itemprop="name">'. $cv['revolver__comments']['field_user_name'] .'</span></h2>';
		$contents .= '<time datetime="'. $cv['revolver__comments']['field_time'] .'">'. $cv['revolver__comments']['field_time'] .'</time>';
		$contents .= '</header>';
		
		$contents .= '<div class="revolver__comments-moderation-row">';

		$contents .= '<figure class="revolver__comments-avatar">';

		if( $cv['revolver__users']['field_avatar'] === 'default') {

			$src = '/public/avatars/default.png';

		} 
		else {

			$src = $cv['revolver__users']['field_avatar'];
			
		}
		
		$contents .= '<img src="'. $src .'" alt="'. $cv['field_user_name'] .'" />';
		$contents .= '</figure>';

		$contents .= '<div itemprop="commentText" class="revolver__comments-contents">'. $safe::safe(html_entity_decode(htmlspecialchars_decode($cv['revolver__comments']['field_content']))) .'</div>';

		$contents .= '</div><br />';

		$contents .= '<footer class="revolver__comments-footer"><nav><ul>';
		$contents .= '<li><a href="/comment/'. $cv['revolver__comments']['field_id'] .'/edit/">'. $lang::T( '[Edit]', $interface_primary_language )[0] .'</a></li>';
		$contents .= '</ul></nav></footer>';
		
		$contents .= '</article>';

	}
}


$contents .= '</div>';

$contents .= '<div data-content="tab-3">';

foreach ($base::getData('nodes', ['order' => 'desc', 'limit' => 0]) as $nodes) {

	foreach ($nodes as $n => $nv) {

		//$contents .= '<pre>'. $n .'</pre>';
	
	}

}

*/

// $contents .= '</div>';
// $contents .= '</aside>';

$node_data[0] = [

	'title'		=> TRANSLATIONS[ $ipl ]['Contents moderation'],
	'id'		=> 'contents-moderation',
	'route'		=> '/node/contents/',
	'contents'	=> $contents,
	'teaser'	=> false,
	'footer'	=> false,
	'time'		=> false,
	'published' => 1

];

?>