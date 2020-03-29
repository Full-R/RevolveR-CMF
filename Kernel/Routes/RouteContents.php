<?php

 /* 
  * 
  * RevolveR Route Contents Dispatch
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

if( in_array( ROLE, ['Admin', 'Writer'] ) ) {

  $node = null;

  if(!empty(SV['p'])) {

    $token_explode = explode('|', $cipher::crypt('decrypt', SV['c']['usertoken']));

    $files_to_delete = [];

    $published = 0;
    $mainpage  = 0;

    $action = null;

    // Files
    $f_limit = 0;

    while( $f_limit <= 100 ) {

      if( isset(SV['p'][ 'revolver_delete_attached_file_'. $f_limit ]) ) {

        $files_to_delete[ $f_limit ] = SV['p'][ 'revolver_delete_attached_file_'. $f_limit ]['value'];

      }

      $f_limit++;

    }

    if( isset(SV['p']['revolver_node_edit_id']) ) {

      if( (bool)SV['p']['revolver_node_edit_id']['valid'] ) {

        $node_id = SV['p']['revolver_node_edit_id']['value'];

      }

    }

    if( isset(SV['p']['revolver_node_edit_title']) ) {

      if( (bool)SV['p']['revolver_node_edit_title']['valid'] ) {

        $node_title = strip_tags( SV['p']['revolver_node_edit_title']['value'] );

      }

    }

    if( isset(SV['p']['revolver_node_edit_description']) ) {

      if( (bool)SV['p']['revolver_node_edit_description']['valid'] ) {

        $node_description = strip_tags( SV['p']['revolver_node_edit_description']['value'] );

      }

    }

    if( isset(SV['p']['revolver_node_edit_content']) ) {

      if( (bool)SV['p']['revolver_node_edit_content']['valid'] ) {

        $node_content = $markup::Markup(

          SV['p']['revolver_node_edit_content']['value'], [ 'xhash' => 0 ]

        );

      }

    }

    if( isset(SV['p']['revolver_node_edit_route']) ) {

      if( (bool)SV['p']['revolver_node_edit_route']['valid'] ) {

        $node_route = strip_tags(preg_replace("/\/+/", '/', preg_replace("/ +/", '-', trim( SV['p']['revolver_node_edit_route']['value'] )))); 

      }

    }

    if( isset(SV['p']['revolver_node_edit_category']) ) {

      if( (bool)SV['p']['revolver_node_edit_category']['valid'] ) {

        $node_category = SV['p']['revolver_node_edit_category']['value'][0];

      }

    }

    if( isset(SV['p']['revolver_node_country']) ) {

      if( (bool)SV['p']['revolver_node_country']['valid'] ) {

        $country = SV['p']['revolver_node_country']['value'];

      }

    }

    if( isset(SV['p']['revolver_node_published']) ) {

      if( (bool)SV['p']['revolver_node_published']['valid'] ) {

        $published = 1;

      }

    }

    if( isset(SV['p']['revolver_node_mainpage']) ) {

      if( (bool)SV['p']['revolver_node_mainpage']['valid'] ) {

        $mainpage = 1;

      }

    }

    if( isset(SV['p']['revolver_node_edit_delete']) ) {

      if( (bool)SV['p']['revolver_node_edit_delete']['valid'] ) {

        $action = 'delete';

      }

    }

    if( isset(SV['p']['revolver_captcha']) ) {

      if( (bool)SV['p']['revolver_captcha']['valid'] ) {

        if( $captcha::verify(SV['p']['revolver_captcha']['value']) ) {

          define('form_pass', 'pass');

        }

      }

    }

    $node = iterator_to_array(

          $model::get('nodes', [

            'criterion' => 'id::'. $node_id,
            'course'  => 'backward',
            'sort'    => 'id'

          ])

        )['model::nodes'];

  }

} 
else {

  header('Location: '. site_host .'/user/auth/?notification=authorization-reazon');

}

if( $node ) {

  $node = $node[0];

  if( defined('form_pass') ) {

    if( $token_explode[2] === $node['user'] || ( in_array(ROLE, ['Admin', 'Writer'], true) ) ) {

      if( $action !== 'delete' ) {

        // Is route avalilable
        $passed = true;

        foreach( main_nodes as $k => $v ) {

          if( trim($v['route']) === trim($node_route) ) {

            $passed = null;

            break;

          }

        }

        if( strlen( $node_route ) !== strlen( utf8_decode( $node_route ) ) ) {

          $passed = null;

        }


        // Is route correct
        $route_fix = ltrim(

          rtrim(

            $node_route, '/'

          ), '/'

        );

        $node_route = '/'. $route_fix .'/';

        if( $passed && form_pass === 'pass' ) {

          $model::set('nodes', [

            'id'          => $node_id,
            'title'       => $node_title,
            'content'     => $node_content,
            'description' => $node_description,
            'route'       => $node_route,
            'category'    => $node_category,
            'user'        => $node['user'],

            'time'        => date('d.m.Y h:i'),

            'country'     => $country,

            'published'   => $published,
            'mainpage'    => $mainpage,

            'criterion'   => 'id'

          ]);

          // Files
          if( (bool)count($files_to_delete) ) {

            foreach( $files_to_delete as $file_to_delete ) {

              // Delete from database
              $model::erase('files', [

                'criterion' => 'id::'. explode(':', $file_to_delete)[0]

              ]);

              // Delete file from filesystem
              unlink( $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/' . explode(':', $file_to_delete)[1] );

            }

          }

          if( (bool)count(SV['f']) ) {

            foreach( SV['f'] as $file ) {

              foreach( $file as $f ) {

                $upload_allow = null;

                if( !is_readable($_SERVER['DOCUMENT_ROOT'] .'/public/uploads/'. $f['name']) ) {

                  if( (bool)$f['valid'] ) {

                    $upload_allow = true;

                  }

                }

                if( $upload_allow ) {

                  $model::set('files', [

                    'node'      => $node_route,
                    'name'      => $f['name'],
                    'criterion' => 'node'

                  ]);

                  move_uploaded_file( $f['temp'], $_SERVER['DOCUMENT_ROOT'] .'/public/uploads/'. $f['name'] );

                }

              }

            }

          }

          header( 'Location: '. site_host . $node_route .'?notification=node-updated^'. $node_id .'-'. $published .'-'. $mainpage );

        }
        else {

          header('Location: '. site_host . $node_route .'edit/?notification=conflict-reason');

        }

      }
      else if( $action === 'delete' && form_pass === 'pass' ) {

        $files = iterator_to_array(

            $model::get('files', [

              'criterion' => 'node::'. $node_route,
              'course'  => 'forward',
              'sort'    => 'id'

            ])

          )['model::files'];

        if( $files ) {

          foreach( $files as $f ) {   

            unlink( $_SERVER['DOCUMENT_ROOT'] .'/public/uploads/'. $f['name'] );

          }

          // Delete from database
          $model::erase('files', [

            'criterion' => 'node::'. $node_route

          ]);

        }

        // Delete from database
        $model::erase('nodes', [

          'criterion' => 'id::'. $node_id

        ]);

        header( 'Location: '. site_host .'?notification=node-erased^'. $node_id );

      }

    }

  }
  else {

    header('Location: '. site_host . $node_route .'edit/?notification=no-changes');

  }

} 
else {

  header('Location: '. site_host .'?notification=no-changes');

}

print '<!-- contents dispatch -->';

define('serviceOutput', [

  'ctype'     => 'text/html',
  'route'     => '/contents-d/'

]);

?>
