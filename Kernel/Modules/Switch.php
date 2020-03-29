<?php

 /* 
  * RevolveR primary routing switch
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

if( defined('ROUTE') ) {

	switch( ROUTE['node'] ) {
		
		/* 404, any ... */
		default:

		/* Main Nodes */
		case '#homepage':

			ob_start();

			if( !INSTALLED ) {

				$node_data[] = [

					'title'     => 'RevolveR CMF &#8226; Welcome',
					'contents'  => '<p>Revolver CMF not installed!</p><p>Follow <a title="Install RevolveR CMS Now!" href="/setup/">setup</a> process now!</p>',
					'id'	    => 'welcome-message',
					'route'     => '/setup/',
					'teaser'    => true,
					'footer'    => true,
					'published' => 1

				];

			}
			else {

				require_once('./Kernel/Nodes/Node.php');

			}

			break;

		case '#create':

			ob_start();

			if( !Auth ) {

				header('Location: '. site_host .'/user/auth/');

			}

			require_once('./Kernel/Nodes/NodeCreate.php');

			break;

		case '#setup':

			ob_start();

			require_once('./Kernel/Nodes/NodeSetup.php');

			break;

		case '#dashboard':

			ob_start();

			if( ROLE !== 'Admin' ) {

				header('Location: '. site_host .'/');

			}

			require_once('./Kernel/Nodes/NodeDashboard.php');

			break;

		case '#terminal':

			ob_start();

			if( ROLE !== 'Admin' ) {

				header('Location: '. site_host .'/');

			}

			require_once('./Kernel/Nodes/NodeTerminal.php');

			break;

		case '#attendance':

			ob_start();

			if( ROLE !== 'Admin' ) {

				header('Location: '. site_host .'/');

			}

			require_once('./Kernel/Nodes/NodeAttendance.php');

			break;


		case '#moderation':

			ob_start();

			if( Auth ) {

				if( in_array(ROLE, ['Admin', 'Writer'], true) ) {

					$title = TRANSLATIONS[ $ipl ]['Moderation'];

					require_once('./Kernel/Nodes/NodeModeration.php');

				}

			} 
			else {

				header( 'Location: '. site_host .'/' );

			}

			break;

		case '#categories':

			ob_start();

			require_once('./Kernel/Nodes/NodeCategories.php');

			break;

		case '#privacy':

			ob_start();

			require_once('./Kernel/Nodes/NodePrivacy.php');

			break;

		case '#user':

			ob_start();


			if( in_array(ROUTE['route'], ['/user/messages/', '/user/'], true) && !Auth ) {

				header('Location: '. site_host .'/user/auth/');

			}

			if( ROUTE['route'] === '/user/auth/' && !Auth ) {

				require_once('./Kernel/Nodes/NodeUserLogin.php');

			}
			else {

				if( Auth ) {

					if( in_array(ROUTE['route'], ['/user/auth/', '/user/register/'], true) ) {

						header('Location: '. site_host .'/user/');

					}

				}

			}

			if( ROUTE['route'] === '/user/' && Auth ) {

				require_once('./Kernel/Nodes/NodeUser.php');

			}

			if( ROUTE['route'] === '/user/messages/' && Auth ) {

				require_once('./Kernel/Nodes/NodeMessages.php');

			}

			if( ROUTE['route'] === '/logout/' ) {

				if( !Auth ) {

					$auth::logout();

					header('Location: '. site_host .'/');

				}

				$node_data[0] = [

					'title'     => TRANSLATIONS[ $ipl ]['See you soon'] .'!',
					'contents'  => '<p>'. TRANSLATIONS[ $ipl ]['You are signed out of the system. Goodbye'] .'</p>',
					'id'	    => 'user-logout',
					'route'     => '/logout/',
					'teaser'    => false,
					'footer'    => false,
					'time'		=> false,
					'published' => 1

				];

				$auth::logout();

			}

			if( ROUTE['route'] === '/user/recovery/' && !Auth ) {

				require_once('./Kernel/Nodes/NodeUserRecovery.php');

			}

			if( ROUTE['route'] === '/user/register/' && !Auth ) {

				require_once('./Kernel/Nodes/NodeUserRegister.php');

			}

			break;

		/* Dispatch routes */
		case '#comments-d':

			ob_start('ob_gzhandler');

			// Comments dispatch
			require_once('./Kernel/Routes/RouteComments.php');

			break;

		case '#contents-d':

			ob_start('ob_gzhandler');

			if( Auth ) {

				// Contents dispatch
				require_once('./Kernel/Routes/RouteContents.php');

			}

			break;

		case '#category-d':

			ob_start('ob_gzhandler');

			if( Auth ) {

				// Category dispatch
				require_once('./Kernel/Routes/RouteCategoriesEdit.php');

			}

			break;

		case '#user-d':

			ob_start('ob_gzhandler');

			if( Auth ) {

				// Profile dispatch
				require_once('./Kernel/Routes/RouteUserEdit.php');

			}

			break;

		/* Service Routes output without template renderer with another MIME */
		case '#secure':

			ob_start();

			// Protection CSRF / XSRF
			require_once('./Kernel/Routes/RouteSecure.php');

			break;

		case '#search':

			ob_start('ob_gzhandler');

			// Search
			require_once('./Kernel/Routes/RouteSearch.php');

			break;

		case '#preview':

				ob_start('ob_gzhandler');

				// Preview
				require_once('./Kernel/Routes/RoutePreview.php');	

			break;

		case '#terminal-s':

			ob_start();

			require_once('./Kernel/Routes/RouteTerminal.php');

			break;

		case '#sitemap':

			ob_start('ob_gzhandler');

			// Sitemap XML
			require_once('./Kernel/Routes/RouteSitemap.php');

			break;

		case '#aggregator':

			ob_start();

			// Feed
			require_once('./Kernel/Routes/RouteAggregator.php');

			break;

	}

}
else {

	ob_start();

	// Nodes, categories, comments, profile edit
	require_once('./Kernel/Nodes/Node.php');

}

?>
