<?php

	if( !isset($n['editor']) ) {

		$n['editor'] = null;

	}

	if( !isset($n['language']['hreflang']) ) {

		$n['language']['hreflang'] = $primary_language; 

	}

	if( !isset( $n['time'] ) ) {

		$n['time'] = null;

	}

	$render_node .= '<article lang="'. $n['language']['hreflang'] .'" class="revolver__article article-id-'. $n['id'] .' '. $class .'">';

	$render_node .= '<header class="revolver__article-header">'; 

	if( $n['teaser'] ) {

		$render_node .= '<h2><a hreflang="'. $n['language']['hreflang'] .'" href="'. $n['route'] .'" rel="bookmark">'. $n['title'] .'</a></h2>';

	}
	else {

		$render_node .= '<h2>'. $n['title'] .'</h2>';

	}

	if( $n['time'] ) {

		$render_node .= '<time datetime="'. $calendar::formatTime( $n['time'] ) .'">'. $n['time'] .'</time>';

	}

	$render_node .= '</header>';

	if( RQST === '/' ) {

		$render_node .= '<div class="revolver__article-contents">'. $markup::Markup( $n['contents'], [ 'length' => 2000, 'xhash' => 0, 'lazy' => 1 ] ) .'</div>';

	}
	else {

		if( $flag_main_node ) {

			$render_node .= '<div class="revolver__article-contents">'. $n['contents'] .'</div>';	

		}
		else {

			$render_node .= '<div class="revolver__article-contents">'. $markup::Markup( $n['contents'], ['lazy' => 1] ) .'</div>';

		}

	}

	if( $n['footer'] ) {

		$render_node .= '<footer class="revolver__article-footer"><nav>';

		$render_node .= '<ul>';

		if( $n['editor'] ) {

			$render_node .= '<li><a title="'. $n['title'] .' '. TRANSLATIONS[ $ipl ]['edit'] .'" href="'. $n['route'] .'edit/' .'">'. TRANSLATIONS[ $ipl ]['Edit'] .'</a></li>';

		}
		else {

			$render_node .= '<li><a hreflang="'. $n['language']['hreflang'] .'" title="'. $n['title'] .'" href="'. $n['route'] .'">'. TRANSLATIONS[ $ipl ]['Read More'] .' &rArr;</a></li>';

		}

		$render_node .= '</ul></nav></footer>';

	}

	$render_node .= '</article>';

	// Similar contents links next \ prev 
	if( isset($n['similar']) && RQST !== '/' && !(bool)pagination['offset'] ) {

		$render_node .= '<nav class="revolver__similar-links">';

		$render_node .= '<ul>';

		if( !empty( $n['similar']['prev']['route'] ) ) {

			$render_node .= '<li class="revolver__similar-links--previous">';

			$render_node .= '<a hreflang="'. $n['language']['hreflang'] .'" rel="prev" href="'. $n['similar']['prev']['route'] .'" title="'. TRANSLATIONS[ $ipl ]['previous'] .' :: '. $n['similar']['prev']['title'].'">';

			$render_node .= '<span>'. $n['similar']['prev']['title'] .'</span>';

			$render_node .= '</a>';

			$render_node .= '</li>';

		}

		if( !empty( $n['similar']['next']['route'] ) ) { 

			$render_node .= '<li class="revolver__similar-links--next">';

			$render_node .= '<a hreflang="'. $n['language']['hreflang'] .'" rel="next" href="'. $n['similar']['next']['route'] .'" title="'. TRANSLATIONS[ $ipl ]['next'] .' :: '. $n['similar']['next']['title'] .'">';

			$render_node .= '<span>'. $n['similar']['next']['title'] .'</span>';

			$render_node .= '</a>';

			$render_node .= '</li>';

		}

		$render_node .= '</ul>';

		$render_node .= '</nav>';

	}

?>
