
<!-- RevolveR :: main -->
<section class="revolver__main-contents <?php print $main_class; ?> <?php print $auth_class; ?>">

<!-- RevolveR :: site description -->
<h2 class="revolver__site-description"><?php print DESCRIPTION; ?></h2> 

<?php

$mainWrap = true;

/* Edit Templates */
if( defined('ROUTE') ) {

	if( isset( ROUTE['edit'] ) ) {

		// Categories edit
		if( ROUTE['node'] === '#categories' && ROUTE['edit'] ) {

			require_once('./Templates/'. TEMPLATE .'/Forms/categories-edit.php');

			$mainWrap = null;

		}

		// Profile edit
		if( ROUTE['node'] === '#user' && ROUTE['edit'] ) {

			require_once('./Templates/'. TEMPLATE .'/Forms/profile-edit.php');

			$mainWrap = null;

		}

	}

}

if( !defined('ROUTE') ) {

	if( isset( PASS[ 3 ] ) ) {

		if( PASS[ 1 ] === 'comment' && PASS[ 3 ] === 'edit' ) {

			// Comments edit
			require_once('./Templates/'. TEMPLATE .'/Forms/comments-edit.php');

			$mainWrap = null;

		}

	}

}

?>

<?php if( $mainWrap ): 

	$render_node = '';

	$counter = 0;

	$shift = 0;

	if( is_array( $node_data ) ) {

		foreach( $node_data as $n ) {

			if( !isset($n['editor_mode']) ) {

				$n['editor_mode'] = null;

			}

			/* Pagination :: makes a render nodes only in pagination range */
			$pages_count = ceil( count($node_data) / $nodes_per_page );

			if( (bool)pagination['allow'] ) {

				if( (bool)pagination['offset'] ) {

					if( (bool)( pagination['offset'] - $nodes_per_page ) ) {

						if( $counter + $nodes_per_page >= pagination['offset'] ) {

							break;

						}

						if( $shift++ < ( pagination['offset'] - $nodes_per_page ) || $shift > ( pagination['offset'] ) ) {

							continue;

						}

					}
					else {

						if( $counter >= pagination['offset'] ) {

							break;

						}

					}

				}
				else {

					if( $counter >= $nodes_per_page ) {

						break;

					}

				}

			}

			// Node presets
			if( (bool)$n['published'] ) {

				$class = 'published';

			}
			else {

				$class = 'unpublished';

				if( in_array( ACCESS['role'], ['none', 'User'], true ) || ACCESS === 'none' ) {

					continue;

				}

			}

			if( !$n['editor_mode'] ) {

				if( RQST === $n['route'] ) {

					$allowView = true;

					if( defined('ROUTE') ) {

						if( !$resolve::isAllowed( ROUTE['route'] ) ) {

							$allowView = null;

						}

					}

					if( $allowView ) {

						/* Node views */
						empty($n['route']) ?: include('./Templates/'. TEMPLATE .'/Views/node-view.php');

						if( !$resolve::isAllowed( RQST ) && !(bool)pagination['offset'] ) {

							/* Comments views */
							include('./Templates/'. TEMPLATE .'/Views/comments-view.php');

							/* Comments add */
							require_once('./Templates/'. TEMPLATE .'/Forms/comments-add.php');

						}

					}

				}
				else if( RQST === '/' ) {

					include('./Templates/'. TEMPLATE .'/Views/node-view.php');

				}

			}
			else {

				/* Node edit */
				RQST !== $n['route'] . 'edit/' ?: require_once('./Templates/'. TEMPLATE .'/Forms/node-edit.php');

			}

			$counter++;

		} /* end foreach */ 

	}
	//else {

		//header('Location: '. site_host .'/?notification=nothing-to-show');

	//}

endif;

?>

<?php if( $pages_count > 1 && pagination['allow'] ) {

	require_once('./Templates/'. TEMPLATE .'/Pagination.php');

} ?>

<?php 

	// Preload contents
	define('PreloadList', $markup::$lazyList);

?>

<?php print $notify::Conclude(); ?>

<?php print $render_node; ?>

<!-- related -->
<?php require_once('Related.php'); ?>

</section>
