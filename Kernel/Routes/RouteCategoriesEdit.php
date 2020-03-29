<?php

 /*
  * 
  * RevolveR Route Categories Edit
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

if( in_array(ROLE, ['Admin', 'Writer'], true) ) {

	if( !empty(SV['p']) ) {

		if( isset(SV['p']['revolver_category_title']) ) {

			if( (bool)SV['p']['revolver_category_title']['valid'] ) {

				$title = strip_tags( SV['p']['revolver_category_title']['value'] );

			}

		}

		if( isset(SV['p']['revolver_category_description']) ) {

			if( (bool)SV['p']['revolver_category_description']['valid'] ) {

				$description = strip_tags( SV['p']['revolver_category_description']['value'] );

			}

		}


		if( isset(SV['p']['revolver_category_edit']) ) {

			if( (bool)SV['p']['revolver_category_edit']['valid'] ) {

				$id = SV['p']['revolver_category_edit']['value'];

			}

		}

		if( isset(SV['p']['revolver_category_action_delete']) ) {

			$action = 'delete';

		}
		else {

			$action = 'update';

		}

		if( isset(SV['p']['revolver_captcha']) ) {

			if( (bool)SV['p']['revolver_captcha']['valid'] ) {

				if( $captcha::verify(SV['p']['revolver_captcha']['value']) ) {

					define('form_pass', 'pass');

				}

			}

		}

	}

	if( defined('form_pass') ) {

		if( form_pass === 'pass' ) {

			if( $action === 'update' ) {

				$model::set('categories', [

					'id'			=> $id,
					'title'			=> $title,
					'description'	=> $description,
					'criterion'		=> 'id'

				]);

				header('Location: '. site_host .'/categories/?notification=category-updated^'. $id);

			}
			else {

				$model::erase('categories', [

					'criterion' => 'id::'. $id

				]);

				header('Location: '. site_host .'/categories/?notification=category-erased^'. $id);

			}

		}
		else {

			header('Location: '. site_host .'/categories/'. $id .'/edit/?notification=no-changes');

		}

	} 
	else {

		header('Location: '. site_host .'/categories/'. $id .'/edit/?notification=no-changes');

	}

} 
else {

	header('Location: '. site_host .'/categories/');

}

print '<!--Category dispatcher -->';

define('serviceOutput', [

  'ctype'     => 'text/html', 
  'route'     => '/category-d/'

]);

?>
