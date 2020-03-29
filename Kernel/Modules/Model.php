<?php

 /* 
  * 
  * RevolveR Kernel Models
  *
  * v.1.8.0
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

final class Model {

  protected static $dbx;
  protected static $schema;
  protected static $tprefix;
  protected static $fprefix;

  function __construct( DBX $dbx, iterable $schema, ?string $tprefix = null, ?string $fprefix = null ) {

    // Data Base X Instance
    self::$dbx = $dbx;

    // Data Base X Schema
    self::$schema = $schema;

    // Data Base tables prefix
    self::$tprefix = $tprefix ? $tprefix .'__' : 'revolver__';

    // Data Base X fields prefix 
    self::$fprefix = $fprefix ? $fprefix .'_' : 'field_';

  }

  // Model set
  public static function set( ?string $m = null, ?iterable $p = null ): void {

    // Read Data Base table fields from structure
    $fields = self::fields( $m );

    $passed = true;

    $struct = [];

    // Fields integrity protection
    foreach( $p as $f => $v ) {

      if( !in_array( self::$fprefix . $f, $fields ) ) {

        // criterion parameter field is used only when 
        // strict SQL mode activated 
        // otherwise it's not needed
        if( $f === 'criterion' ) {

          $struct[ $f ]['value'] =  self::$fprefix . $v;                   

        }
        else {

          $passed = null;

        }

      }
      else {

        $struct[ self::$fprefix . $f ]['value'] = $v;

      }

    }

    // If id not present make add.
    // Otherwise make update existed.
    if( !isset( $struct['field_id']['value'] ) ) {

      $struct['field_id']['value'] = 0;    

    }

    // Execute intelligent query to set or update values
    if( $passed ) {

      self::$dbx::query( 'in', self::$tprefix . $m, $struct );

    }

  }

  // Model get
  public static function get( ?string $m = null, ?iterable $p = null ): iterable {

    $istack = null; 

    switch( $m ) {

      # Related tables models
      # :: expert join queries 

      // Node related to category with user
      case 'category->node->user':

        $istack = self::nodeCategoryUser($p);

        break;

      case 'category->node':

        $istack = self::nodeCategory($p);

        break;

      // Comments related to node with user
      case 'node->comment->user':

        $istack = self::nodeCommentUser($p);

        break;

      // Comments related to node
      case 'node->comment':

        $istack = self::nodeComment($p);

        break;

      // Subsription related to User
      case 'user->subscription':

        $istack = self::userSubscription($p);

        break;

      // Role and permissions related to User
      case 'user->role':

        $istack = self::userRole($p);

        break;

      # Mutual models
      # :: merge different models when expert join queries can't return
      # result becuase there no data for joint in one of the related tables 

      // Joint models
      case 'joint':

        $istack = self::complexModel($p);

        break;

      default:

        $istack = self::singleModel($m, $p);

        break;

    }

    // Format choose
    switch( $m ) {

      case 'category->node->user':
      case 'category->node':
      
      case 'node->comment->user':
      case 'node->comment':

      case 'user->subscription':
      case 'user->role':
      

        $istack = self::simplify( $istack );

        break;

      default:

        $istack = $istack;

        break;

    }

    // Store iterable array collection
    yield  'model::'. $m => $istack;


  }

  // Model erase
  public static function erase( string $m = null, iterable $p = null ): void {

    // Read Data Base table fields from structure
    $fields = self::fields( $m );

    $passed = true;

    $struct = [];

    // Fields integrity protection
    if( isset( $p['criterion'] ) ) {

      $args = explode('::', $p['criterion']);

      if( isset($args[0]) ) {

        if( (bool)strlen($args[0]) ) {

          if( !in_array( self::$fprefix . $args[0], $fields ) ) {

            $passed = null;

          }
          else {

            $struct['criterion_field'] = self::$fprefix . $args[0];

          }

          if( isset( $args[1] ) ) {

            if( (bool)strlen($args[1]) ) {

              $struct['criterion_value'] = $args[1];

            }
            else {

              $passed = null;

            }

          }
          else {

            $passed = null;

          }

        }
        else {

          $passed = null;

        }

      }
      else {

        $passed = null;

      }

    }
    else {

      $passed = null;

    }

    // Execute delete query
    if( $passed ) {

      self::$dbx::query( 'r', self::$tprefix . $m, $struct );

    }

  }

  // Prepeare fields
  private static function fields( string $struct ): iterable {

    $fields = [];

    foreach( self::$schema[ $struct ] as $k => $v ) {

      $fields[] = $k;

    }

    return $fields;

  }

  // Make possible to use simplified access
  private static function simplify( iterable $struct ): iterable {

    $astack = [];

    $i = 0;

    foreach( $struct as $item ) {

      foreach( $item as $ifield => $ivalue ) {

        foreach( $ivalue as $ikey => $icontents ) {

          $astack[ $i ][ str_replace( self::$tprefix, '', $ifield ) ][ str_replace( self::$fprefix, '', $ikey ) ] = $icontents;

        }

      }

      $i++;

    }

    return $astack;

  }

  // Compare indexes
  private static function indexes( string $tname ): ?string {

    $indexs = [];

    foreach( self::$schema[ $tname ] as $fname => $fvalue ) {

      if( isset(self::$schema[ $tname ][ $fname ]['index']) ) {

        $iname = self::$tprefix . $tname .'_INDEX_'. self::$schema[ $tname ][ $fname ]['index']['type'];

        $indexs[ $iname ] = $iname;

      }

    }

    return (bool)count( $indexs ) ? implode(', ', $indexs) : null;    

  }

  protected static function nodeCategoryUser( ?iterable $params ): ?iterable {

    // Clean stored result
    unset( self::$dbx::$result['result'] );

    // Get fields
    $catgories = self::fields('categories');
    $nodes = self::fields('nodes');
    $users = self::fields('users');

    // Link tables
    $nodes['linked_table'] = self::$tprefix .'nodes';
    $nodes['linked_field'] = self::$fprefix .'category';

    // Link fields
    $nodes['criterion_table'] = self::$tprefix .'categories';
    $nodes['criterion_field'] = self::$fprefix .'id';

    // Link tables
    $users['criterion_table'] = self::$tprefix .'users';
    $users['criterion_field'] = self::$fprefix .'nickname';

    // Link fields
    $users['linked_table'] = self::$tprefix .'nodes';
    $users['linked_field'] = self::$fprefix .'user';

    if( $params ) {

      // Additional criterion
      if( isset( $params['criterion'] ) ) {

        $args = explode('::', $params['criterion']);

        $users['where_field'] = self::$tprefix . $args[0] .'::'. self::$fprefix . $args[1] .'::'. $args[2];

      }

      if( isset( $params['sort'] ) ) {

        $users['group_field'] = self::$fprefix . $params['sort'];

      }

      if( (bool)self::indexes( 'categories' ) ) {

        $users['index'] = self::indexes( 'categories' );

      }

    }

    // Expert JOIN query configure
    $EJModel = [ $catgories, $nodes, $users ];

    // Affected tables
    $mTables = [ '', 'categories', 'nodes', 'users' ];

    self::$dbx::query( 'xj', ltrim( implode('|'. self::$tprefix, $mTables), '|' ), $EJModel );

    return isset( self::$dbx::$result['result'] ) ? self::$dbx::$result['result'] : null;

  }

  protected static function nodeCategory( ?iterable $params ): ?iterable {

    // Clean stored result
    unset( self::$dbx::$result['result'] );

    // Get fields
    $catgories = self::fields('categories');
    $nodes = self::fields('nodes');

    // Link tables
    $nodes['linked_table'] = self::$tprefix .'nodes';
    $nodes['linked_field'] = self::$fprefix .'category';

    // Link fields
    $nodes['criterion_table'] = self::$tprefix .'categories';
    $nodes['criterion_field'] = self::$fprefix .'id';

    if( $params ) {

      // Additional criterion
      if( isset( $params['criterion'] ) ) {

        $args = explode('::', $params['criterion']);

        $nodes['where_field'] = self::$tprefix . $args[0] .'::'. self::$fprefix . $args[1] .'::'. $args[2];

      }

      if( isset( $params['sort'] ) ) {

        $nodes['group_field'] = self::$fprefix . $params['sort'];

      }

      if( (bool)self::indexes( 'categories' ) ) {

        $nodes['index'] = self::indexes( 'categories' );

      }

    }

    // Expert JOIN query configure
    $EJModel = [ $catgories, $nodes ];

    // Affected tables
    $mTables = [ '', 'categories', 'nodes' ];

    self::$dbx::query( 'xj', ltrim( implode('|'. self::$tprefix, $mTables), '|' ), $EJModel );

    return isset( self::$dbx::$result['result'] ) ? self::$dbx::$result['result'] : null;

  }

  protected static function nodeCommentUser( ?iterable $params ): ?iterable {

    // Clean stored result
    unset( self::$dbx::$result['result'] );

    // Make fields collection
    $nodes = self::fields('nodes');
    $comments = self::fields('comments');
    $users = self::fields('users');

    // Link tables
    $comments['criterion_table'] = self::$tprefix .'nodes';
    $comments['criterion_field'] = self::$fprefix .'id';

    // Link fields
    $comments['linked_table'] = self::$tprefix .'comments';
    $comments['linked_field'] = self::$fprefix .'node_id';

    // Link tables
    $users['criterion_table'] = self::$tprefix .'users';
    $users['criterion_field'] = self::$fprefix .'nickname';

    // Link fields
    $users['linked_table'] = self::$tprefix .'comments';
    $users['linked_field'] = self::$fprefix .'user_name';

    if( $params ) {

      // Additional criterion
      if( isset( $params['criterion'] ) ) {

        $args = explode('::', $params['criterion']);

        $nodes['where_field'] = self::$tprefix . $args[0] .'::'. self::$fprefix . $args[1] .'::'. $args[2];

      }

      if( isset( $params['sort'] ) ) {

        $nodes['group_field'] = self::$fprefix . $params['sort'];

      }

      if( (bool)self::indexes( 'nodes' ) ) {

        $users['index'] = self::indexes( 'nodes' );

      }

    }

    // Expert JOIN query configure
    $EJModel = [ 

      $nodes, $comments, $users

    ];

    // Affected tables
    $mTables = [

      '', 'nodes', 'comments', 'users'

    ];

    self::$dbx::query( 'xj', ltrim( implode('|'. self::$tprefix, $mTables), '|' ), $EJModel );

    return isset( self::$dbx::$result['result'] ) ? self::$dbx::$result['result'] : null;

  }

  protected static function nodeComment( ?iterable $params ): ?iterable {

    // Clean stored result
    unset( self::$dbx::$result['result'] );

    // Make fields collection
    $nodes = self::fields('nodes');
    $comments = self::fields('comments');

    // Link tables
    $comments['criterion_table'] = self::$tprefix .'nodes';
    $comments['criterion_field'] = self::$fprefix .'id';

    // Link fields
    $comments['linked_table'] = self::$tprefix .'comments';
    $comments['linked_field'] = self::$fprefix .'node_id';

    if( $params ) {

      // Additional criterion
      if( isset( $params['criterion'] ) ) {

        $args = explode('::', $params['criterion']);

        $comments['where_field'] = self::$tprefix . $args[0] .'::'. self::$fprefix . $args[1] .'::'. $args[2];

      }

      if( isset( $params['sort'] ) ) {

        $comments['group_field'] = self::$fprefix . $params['sort'];

      }

      if( (bool)self::indexes( 'nodes' ) ) {

        $comments['index'] = self::indexes( 'nodes' );

      }

    }

    // Expert JOIN query configure
    $EJModel = [ 

      $nodes, $comments

    ];

    // Affected tables
    $mTables = [

      '', 'nodes', 'comments'

    ];

    self::$dbx::query( 'xj', ltrim( implode('|'. self::$tprefix, $mTables), '|' ), $EJModel );

    return isset( self::$dbx::$result['result'] ) ? self::$dbx::$result['result'] : null;

  }

  protected static function userSubscription( ?iterable $params ): ?iterable {

    // Clean stored result
    unset( self::$dbx::$result['result'] );

    // Make fields collection
    $users = self::fields('users');
    $subscriptions = self::fields('subscriptions'); 

    // Link tables
    $subscriptions['criterion_table'] = self::$tprefix .'users';
    $subscriptions['criterion_field'] = self::$fprefix .'nickname';

    // Link fields
    $subscriptions['linked_table'] = self::$tprefix .'subscriptions';
    $subscriptions['linked_field'] = self::$fprefix .'user_name';

    if( $params ) {

      // Additional criterion
      if( isset( $params['criterion'] ) ) {

        $args = explode('::', $params['criterion']);

        $subscriptions['where_field'] = self::$tprefix . $args[0] .'::'. self::$fprefix . $args[1] .'::'. $args[2];

      }

      if( isset( $params['sort'] ) ) {

        $subscriptions['group_field'] = self::$fprefix . $params['sort'];

      }

      if( (bool)self::indexes( 'users' ) ) {

        $subscriptions['index'] = self::indexes( 'users' );

      }

    }

    // Expert JOIN query configure
    $EJModel = [ 

      $users, $subscriptions

    ];

    // Affected tables
    $mTables = [

      '', 'users', 'subscriptions'

    ];

    self::$dbx::query( 'xj', ltrim( implode( '|'. self::$tprefix, $mTables), '|' ), $EJModel );

    return isset( self::$dbx::$result['result'] ) ? self::$dbx::$result['result'] : null;

  }

  protected static function userRole( ?iterable $params ): ?iterable {

    // Clean stored result
    unset( self::$dbx::$result['result'] );

    // Make fields collection
    $users = self::fields('users');
    $roles = self::fields('roles'); 

    // Link tables
    $roles['criterion_table'] = self::$tprefix .'users';
    $roles['criterion_field'] = self::$fprefix .'permissions';

    // Link fields
    $roles['linked_table'] = self::$tprefix .'roles';
    $roles['linked_field'] = self::$fprefix .'name';

    if( $params ) {

      // Additional criterion
      if( isset( $params['criterion'] ) ) {

        $args = explode('::', $params['criterion']);

        $roles['where_field'] =  self::$tprefix . $args[0] .'::'. self::$fprefix . $args[1] .'::'. $args[2];

      }

      if( isset( $params['sort'] ) ) {

        $roles['group_field'] = self::$fprefix . $params['sort'];

      }

      if( (bool)self::indexes( 'users' ) ) {

        $roles['index'] = self::indexes( 'users' );

      }

    }

    // Expert JOIN query configure
    $EJModel = [ 

      $users, $roles

    ];

    // Affected tables
    $mTables = [

      '', 'users', 'roles'

    ];

    self::$dbx::query( 'xj', ltrim( implode( '|'. self::$tprefix, $mTables), '|' ), $EJModel );

    return isset( self::$dbx::$result['result'] ) ? self::$dbx::$result['result'] : null;

  }

  protected static function singleModel( string $model, ?iterable $params ): ?iterable {

    // Clean stored result
    unset( self::$dbx::$result['result'] );

    if( isset( self::$schema[ $model ] ) ) {

      $tname = self::$tprefix . $model;
      $model = self::$schema[ $model ];

    }
    else {

      return null;

    }

    // Default query parameters
    $asort = self::$fprefix .'id';
    $order = 'asc';
    
    $shift = '';

    if( $params ) {

      $pmkey = 'criterion_value';

      if( isset( $params['expert'] ) ) {

        if( (bool)$params['expert'] ) {

          $pmkey = 'criterion_regexp';

        }

      }

      // Additional criterion
      if( isset( $params['criterion'] ) ) {

        $args = explode('::', $params['criterion']);

        $model['criterion_field'] = self::$fprefix . $args[0];

        $model[ $pmkey ] = $args[1];

      }

      if( isset( $params['sort'] ) ) {

        $asort = self::$fprefix . $params['sort'];

      }

      if( isset( $params['course'] ) ) {

        if( $params['course'] === 'forward' ) {

          $order = 'asc';

        }

        if( $params['course'] === 'backward' ) {

          $order = 'desc';
          
        }        

      }

      if( isset( $params['bound'] ) ) {

        if( (bool)$params['bound'][0] ) {

          $shift = '|'. $params['bound'][0];

          if( isset($params['bound'][1]) ) {

            if( (bool)$params['bound'][1] ) {

              $shift .= '|'. $params['bound'][1];

            }

          }

        }

      }

      // Expert Select query configure
      self::$dbx::query( 'xs|'. $asort .'|'. $order . $shift, $tname, $model );

    } 
    else {

      // Simple Select query configure
      self::$dbx::query( 's|'. $asort .'|'. $order . $shift, $tname, $model );

    }

    if( isset( self::$dbx::$result['result'] ) ) {

      foreach( self::$dbx::$result['result'] as $r ) {

        $output[] = array_combine(

          array_map(

            function( $k ) {

              return str_replace( self::$fprefix, '', $k );

            }, array_keys( $r )

          ), $r

        );

      }

    }
    else {

      $output = null;

    }

    return $output;

  }

}

?>
