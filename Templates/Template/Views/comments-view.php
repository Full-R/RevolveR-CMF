<?php

$render_node .= '<section class="revolver__advanced-contents">';

$render_node .= '<h2>'. TRANSLATIONS[ $ipl ]['Comments'] .' &hellip;</h2>';

if( ACCESS === 'none' ) {

	$render_node .= '<div class="revolver__status-notifications revolver__inactive">';

	$render_node .= '<div class="revolver__statuses-heading">... Please register<i>+</i></div>';

	$render_node .= TRANSLATIONS[ $ipl ]['You can write here as guest with moderation'] .' '. TRANSLATIONS[ $ipl ]['Please'];

	$render_node .= ' <a href="/user/auth/">'. TRANSLATIONS[ $ipl ]['confirm your person'] .'</a> ';

	$render_node .= TRANSLATIONS[ $ipl ]['if you have an account or'];

	$render_node .= ' <a href="/user/register/">'. TRANSLATIONS[ $ipl ]['register'] .'</a>';

	$render_node .= '</div>';

	$render_node .= '</p>';

}

// Render comments
if( is_array( $node_comments ) ) {

	foreach( $node_comments as $c ) {

		if( (bool)$c['comment_published'] ) {

			$class = 'published';

		}
		else {

			$class = 'unpublished';

			if( isset( ACCESS['role'] ) ) {

				if( in_array( ACCESS['role'], ['none', 'User'], true) ) {

					continue;

				}

			}

			if( ACCESS === 'none' ) {

				continue;

			}

		}

		$dateData_0 = explode(' ',  $c['comment_time']);

		$dateData_1 = explode('.', $dateData_0[0]); ///YYYY-MM-DDThh:mmTZD

		$datetime = $dateData_1[2] .'-'. $dateData_1[1] .'-'. $dateData_1[0];

		$render_node .= '<article id="comment-'. $c['comment_id'] .'" class="revolver__comments comments-'. $c['comment_id'] .' '. $class .'">';

		$render_node .= '<header class="revolver__comments-header">'; 

		$render_node .= '<h2><a href="'. $n['route'] .'#comment-'. $c['comment_id'] .'">&#8226;'. $c['comment_id'] .'</a> '. TRANSLATIONS[ $ipl ]['by'] .' <span>'. $c['comment_user_name'] .'</span></h2>';

		$render_node .= '<time datetime="'. $datetime .'">'. $c['comment_time'] .'</time>';

		$render_node .= '</header>';

		$render_node .= '<figure class="revolver__comments-avatar">';

		if( $c['comment_user_avatar'] === 'default') {

			$src = '/public/avatars/default.png';

		}
		else {

			$src = $c['comment_user_avatar'];

		}

		$render_node .= '<img src="'. $src .'" alt="'. $c['comment_user_name'] .'" />';

		$render_node .= '</figure>';

		$render_node .= '<div class="revolver__comments-contents">'. $markup::Markup( $c['comment_contents'], [ 'xhash' => 1,  'lazy' => 1 ] ) .'</div>';


		if( $n['editor'] ) {

			$render_node .= '<footer class="revolver__comments-footer"><nav><ul>';

			$render_node .= '<li><a title="'. $c['comment_id'] .' '. TRANSLATIONS[ $ipl ]['edit'] .'" href="/comment/'. $c['comment_id'] .'/edit/">'. TRANSLATIONS[ $ipl ]['Edit'] .'</a></li>';

			$render_node .= '</ul></nav></footer>';

		}

		$render_node .= '</article>';

	}

}

$render_node .= '</section>';

?>
