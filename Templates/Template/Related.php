<?php 

if( defined('ROUTE') ):

	if( ROUTE['node'] !== '#user' && ROUTE['node'] !== '#categories' ):

?>

<section class="revolver__related">

<?php 

if( INSTALLED ) {

	$related = $related_comments = '';

	$nodesByCategories = iterator_to_array(

		$model::get('category->node', [

			'criterion' => 'categories::id::*'

		]),

	)['model::category->node'];

	if( $nodesByCategories ) {

		// Sort
		$nbc = [];

		foreach( $nodesByCategories as $r ) {

			$nbc[ $r['categories']['id'] ]['id'] = $r['categories']['id']; 
			$nbc[ $r['categories']['id'] ]['title'] = $r['categories']['title'];
			$nbc[ $r['categories']['id'] ]['items'][] = $r['nodes'];

		}

		// Make templated
		foreach( $nbc as $c ) {

			$related .= '<div class="revolver__related-group-category-'. $c['id'] .'">';
			$related .= '<h4>'. $c['title'] .'</h4>';

			$related .= '<ul>';		

			foreach( $c['items'] as $n ) {

				if( $n['country'] === LANGUAGE ) {

					if( (bool)$n['published'] ) {

						$related .= '<li><a hreflang="'. $lang::getLanguageData(LANGUAGE)['hreflang'] .'" title="'. $n['description'] .'" href="'. $n['route'] .'">'. $n['title'] .'</a></li>';

					}
					else {

						$related .= '<li>'. $n['title'] .'</li>';

					}

				}
			
			}

			$related .= '</ul>';
			$related .= '</div>';

		}

		print $related;

	}

	$comments = iterator_to_array(

		$model::get( 'node->comment', [

			'criterion' => 'comments::id::*'

		]),

	)['model::node->comment'];

	if( $comments ) {

		$related_comments .= '<div class="revolver__related-group-comments">';
		$related_comments .= '<h4>'. TRANSLATIONS[ $ipl ]['Latest comments'] .'</h4>';
		$related_comments .= '<ul>';

		$show_comments = null;

		foreach( $comments as $c => $v ) {

			$comment = substr(

							strip_tags(

								html_entity_decode(

									htmlspecialchars_decode( 

										$v['comments']['content']

									)

								)

							), 0, 50

						);

			$comment = rtrim($comment, '!,.-');

			if( (bool)$v['comments']['published'] ) {

				$show_comments = true;

				$class = 'published';

			}
			else {

				$class = 'unpublished';

				if( isset( ACCESS['role'] ) ) {

					if( in_array( ACCESS['role'], ['none', 'User'], true ) ) {

						continue;

					}

				}

				if( ACCESS === 'none' ) {

					continue;

				}

			}

			$date = explode(' ',  $v['comments']['time']);

			$datetime = explode('.', $date[0]);

			$datetime = $datetime[2] .'-'. $datetime[1] .'-'. $datetime[0] .'T'. ( (bool)strlen( $date[ 1 ] ) ? $date[ 1 ] : '12:00' );

			$related_comments .= '<li class="'. $class .'">#'. $v['comments']['id'] .' :: ';
			$related_comments .= '<a hreflang="'. $lang::getLanguageData( $v['nodes']['country'] )['hreflang'] .'" title="'. $v['comments']['time'] .'" href="'. $v['nodes']['route'] .'#comment-'. $v['comments']['id'] .'">'. $comment .'</a>';
			$related_comments .= '<time datetime="'. $datetime .'">'. $v['comments']['time'] .'</time>';
			$related_comments .= '<span>'. TRANSLATIONS[ $ipl ]['by'] .' '. $v['comments']['user_name'] .'</span>';
			$related_comments .= '</li>';

		}

		$related_comments .= '</ul></div>';

		if( !Auth && $show_comments || Auth ) {

			print $related_comments;

		}

	}

}

?>

</section>

<?php

	endif;

endif;

?>
