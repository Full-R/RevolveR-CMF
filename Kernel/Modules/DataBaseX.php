<?php
 
 /* 
  * 
  * MySQLi Data Base X Class
  * 
  * v.2.9.0
  *
  * `Strict` mode supported
  *
  *
  *
  *                   ^
  *                  | |
  *                @#####@
  *              (###   ###)-.
  *            .(###     ###) \
  *           /  (###   ###)   )
  *          (=-  .@#####@|_--"
  *          /\    \_|l|_/ (\
  *         (=-\     |l|    /
  *          \  \.___|l|___/
  *          /\      |_|   /
  *         (=-\._________/\
  *          \             /
  *            \._________/
  *              #  ----  #
  *              #   __   #
  *              \########/
  *
  *
  *
  * Developer: Dmitry Maltsev
  *
  * License: Apache 2.0
  *
  *
  */

final class DBX {

	// Define connection parameters
	protected static $host = 'localhost';
	protected static $user = 'root';
	protected static $pass = 'root';
	protected static $name = 'root';
	protected static $port = 3306;

	// Secured connection
	protected static $useSSL = null;
	protected static $path_to_cert = null;

	// Strict SQL Mode
	protected static $strict = null;

	// SQL execution parameters
	protected static $sql_action;
	protected static $sql_query;

	// SQL execution variables
	protected static $sql_hash_table;
	protected static $sql_actual_hash;

	// Number of partitions( cache chunks ); 
	// 0 - one static file for every query
	protected static $sql_cache_segments = dbx_cache_chunks_size;

	// Cache directory path
	protected static $cache_directory_path;

	protected static $cipher;
	protected static $encrypted_cache = null;

	// Public data
	public static $result = [];
	public static $struct;

	// Connection
	public static $dbx_lnk = [];

	// Performance 
	public static $queries_counter = 0;
	public static $connect_counter = 0;

	public static $queries_cache_counter = 0;
	public static $queries_hash_counter = 0;

	// Prepared hash
	public static $pq_hash = [];

	function __construct( iterable $db ) {

		// Set strict
		self::$strict = null;

		// Set connection config
		self::$host = (string)$db[ 0 ] ?? self::$host;
		self::$user = (string)$db[ 1 ] ?? self::$user;
		self::$pass = (string)$db[ 2 ] ?? self::$pass;
		self::$name = (string)$db[ 4 ] ?? self::$name;

		// SSL
		self::$useSSL = (bool)$db[ 5 ] ?? self::$useSSL;

		if( isset( $db[ 6 ] ) && self::$useSSL ) {

			if( (bool)$db[ 6 ] ) {

				self::$path_to_cert = $_SERVER['DOCUMENT_ROOT'] .'/private/'. $db[ 6 ];

			}

		} 

		self::$port = (int)$db[3] ?? (int)self::$port;

		// Set cache directory path
		self::$cache_directory_path = $_SERVER['DOCUMENT_ROOT'] .'/cache/dbcache/';

		// Set cache encription to On
		self::$encrypted_cache = true;

		// Get internal instance of cryptography
		self::$cipher = new Cipher();

	}

	protected function __clone() {

	}

	// Compare SQL
	public static function query( string $dbx_q, string $dbx_t, iterable $dbx_f ): void {

		// If query related to multiple tables make iterable
		$tJoin = explode('|', $dbx_t);

		$dbx_t = isset( $tJoin[1] ) ? $tJoin : self::innerEscape( $dbx_t );

		// Explode query
		$dbx_q = explode('|', self::innerEscape($dbx_q));

		$SQL = '';

		self::$sql_hash_table = $dbx_t;

		self::$struct = $dbx_f;

		// Switch query variant 
		switch( $dbx_q[ 0 ] ) {

			// Create query
			case 'c':

				self::$sql_action = 'create';

				$SQL = 'CREATE TABLE IF NOT EXISTS '. $dbx_t .'(';
				
				$SQL .= self::compileListValues( $dbx_f, $dbx_q[0] );

				$SQL .= self::compliteIndexes( $dbx_t, $dbx_f );

				$SQL .= ');';

				break;

			// Expert create supports of compressed table query
			case 'xc':

				self::$sql_action = 'create';

				$SQL = 'CREATE TABLE IF NOT EXISTS '. $dbx_t .'(';

				$SQL .= self::compileListValues( $dbx_f, $dbx_q[0] );

				$SQL .= self::compliteIndexes( $dbx_t, $dbx_f );

				$SQL .= ') ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8;';

				break;

			// Truncate query
			case 't':

				self::$sql_action = 'rehash';

				$SQL = 'TRUNCATE TABLE '. $dbx_t .';';

				break;

			// Drop query
			case 'd':

				self::$sql_action = 'drop';

				$SQL = 'DROP TABLE '. $dbx_t .';';

				break;

			// Info query
			case 'info':

				self::$sql_action = 'describe';

				$SQL = 'DESCRIBE '. $dbx_t .';';

				break;

			// Alter query
			case 'alter':

				self::$sql_action = 'alter';

				$SQL = '';

				break;

			// Refresh indexes query
			case 'index':

				self::$sql_action = 'index';

				$SQL = 'ALTER TABLE `'. $dbx_t .'` ';

				// Alter stack
				$SQL_I = [];

				// Collect indexing fields
				foreach( $dbx_f as $k => $v ) {

					if( isset( $v['index']['type'] ) ) {

						$SQL_I[ $v['index']['type'] ][] = '`'. $k .'`';

					}

				}

				// Make SQL string
				foreach( $SQL_I as $k => $v ) {

					$SQL .= 'ADD';

					switch ( $k ) {

						case 'simple':

							$SQL .= ' INDEX `'. $dbx_t .'_INDEX_'. $k .'`';

							break;

						case 'unique':

							$SQL .= ' UNIQUE INDEX `'. $dbx_t .'_INDEX_'. $k .'`';

							break;

						case 'spatial':

							$SQL .= ' SPATIAL INDEX `'. $dbx_t .'_INDEX_'. $k .'`';

							break;

						case 'full':

							$SQL .= ' FULLTEXT INDEX `'. $dbx_t .'_INDEX_'. $k .'`';

							break;

					}

					$SQL .= ' (';

					if( in_array( $k, [ 'simple', 'unique' ] ) ) {

						$SQL .=	'`field_id`, ';

					}

					foreach( $v as $f ) {

						$SQL .= $f .', ';

					}

					$SQL = rtrim($SQL, ', ') . ')'. ( !in_array( $k, [ 'spatial', 'full' ] ) ? ' USING BTREE, ' : ', ' ); // HASH

				}

				$SQL = rtrim( $SQL, ', ') .';';

				break;

			// Select query
			case 's':

				self::$sql_action = 'hash';

				$SQL = 'SELECT ';

				$SQL_0 = '';

				$c = 0;

				foreach( $dbx_f as $k => $v ) {

					if( (bool)$c ) {

						$SQL .= ', ';

					}

					$SQL .= '`'. self::innerEscape($k) .'`';

					$c++;

				}

				if( isset( $dbx_q[1] ) ) {

					$SQL_0 .= ' ORDER BY `'. $dbx_q[1] .'`';

					if( isset($dbx_q[2]) ) {

						switch( $dbx_q[2] ) {

							case 'asc':

								$SQL_0 .= ' ASC';

								break;

							case 'desc':

								$SQL_0 .= ' DESC';

								break;

						}

					}

					if( isset($dbx_q[3]) ) {

						$SQL_0 .= ' LIMIT ' . (int)$dbx_q[3];

					}

					if( isset($dbx_q[4]) ) {

						$SQL_0 .= ' OFFSET ' . (int)$dbx_q[4];

					}

				}

				$SQL .= ' FROM '. $dbx_t . ' USE INDEX(PRIMARY) '. $SQL_0 .';';

				break;

			// Expert select query
			case 'xs':

				self::$sql_action = 'parametrized_select';

				$SQL = 'SELECT ';

				// WHERE
				$SQL_1 = '';
				$SQL_0 = '';

				// INDEX
				$SQL_I = [];

				// REGEXP
				$SQL_3 = ' REGEXP';

				// This flag make query with RegExp
				$rgx = null;

				$c = 0;

				foreach( $dbx_f as $k => $v ) {

					if( isset( $v['index']['type'] ) ) {

						$SQL_I[ $v['index']['type'] ] = $dbx_t .'_INDEX_'. $v['index']['type'];

					}

					switch( $k ) {

						case 'criterion_field':
						case 'criterion_value':

							if( $k === 'criterion_field' ) {

								$SQL_1 .= '`'. self::innerEscape($v) .'`';

							}

							if( $k === 'criterion_value' ) {

								$SQL_1 .= self::innerEscape($v) === '*' ? "<>''" : '=\''. self::innerEscape($v) .'\'';

							}

						break;

						case 'criterion_regexp':

							$rgx = true;

							if( $k === 'criterion_regexp' ) {

								$SQL_3 .= '(\''. $v .'\')';

							}

							break;

						default:

							if( (bool)$c ) {

								$SQL .= ', ';

							}

							$SQL .= '`'. self::innerEscape($k) .'`'; 

							$c++;

							break;

					}

				}

				if( isset($dbx_q[1]) ) {

					$SQL_0 .= ' ORDER BY `'. $dbx_q[1] .'`';

					if( isset($dbx_q[2]) ) {

						switch( $dbx_q[2] ) {

							case 'asc':

								$SQL_0 .= ' ASC';

							break;

							case 'desc':

								$SQL_0 .= ' DESC';

							break;

						}

					}

					if( isset($dbx_q[3]) ) {

						$SQL_0 .= ' LIMIT ' . (int)$dbx_q[3];

					}

					if( isset($dbx_q[4]) ) {

						$SQL_0 .= ' OFFSET ' . (int)$dbx_q[4];

					}

				}

				if( !(bool)count($SQL_I) ) {

					$SQL_I[] = 'PRIMARY';					

				}


				$SQL .= ' FROM `'. $dbx_t .'` USE INDEX('. ltrim(

					implode(', ', $SQL_I), ', '

				) .') WHERE '. $SQL_1 . ( $rgx ? $SQL_3 : '' ) . $SQL_0 .';';

				break;

			// Insert query
			case 'i':

				self::$sql_action = 'rehash';

				$SQL = 'INSERT INTO '. $dbx_t .'(';

				$SQL .= self::compileListValues( $dbx_f, $dbx_q[0] );

				$SQL .= ') VALUES (';

				$c = 0;

				foreach( $dbx_f as $f => $v ) {

					if( isset($v['value']) ) {

						if( (bool)$c ) {

							$SQL .= ', ';

						}

						$SQL .= '\''. self::innerEscape( $v['value'] ) .'\''; $c++;

					}

				}

				$SQL .= ');';

				break;

			// Intelligent insert with update query
			case 'in':

				self::$sql_action = 'rehash';

				$SQL_1 = '';
				$SQL_2 = '';
				$SQL_3 = '';

				// Strict mode update query exception
				$SQL_4 = '';

				if( self::$strict ) {

					$insert = isset( $dbx_f['criterion'] ) ? null : true;

					foreach( $dbx_f as $k => $v ) {

						$field = $k;

						foreach( $v as $l ) {

							if( $field === 'criterion' ) {

								$SQL_4 .= '`'. $l .'`=\''. $dbx_f[ $l ]['value'] .'\'';		

							}

							$value = $l;

						}

						if( $field !== 'criterion' ) {

							if( $field === 'field_id' && !(bool)$value && $insert ) {

								continue; 								

							}

							$SQL_1 .= '`'. self::innerEscape($field) .'`, ';

							$SQL_2 .= '\''. self::innerEscape($value) .'\', ';

							$SQL_3 .= '`'. self::innerEscape($field) .'`=\''. self::innerEscape($value) .'\', ';

						}

					}

					if( $insert ) {

						$SQL = 'INSERT INTO `'. self::innerEscape( self::$sql_hash_table ) .'` ('. rtrim($SQL_1, ', ') .') VALUES ('. rtrim( $SQL_2, ', ' ) .');';

					}
					else {

						$SQL = 'UPDATE `'. self::innerEscape( self::$sql_hash_table ) .'` SET '. rtrim( $SQL_3, ', ') .' WHERE '. $SQL_4 .';';

					}

				}
				else {

					if( isset($dbx_f['criterion']) ) {

						unset( $dbx_f['criterion'] );

					}

					foreach( $dbx_f as $k => $v ) {

						$field = $k;

						foreach( $v as $l ) {

							$value = $l;

						}

						$SQL_1 .= '`'. self::innerEscape($field) .'`, ';

						$SQL_2 .= '\''. self::innerEscape($value) .'\', ';

						$SQL_3 .= '`'. self::innerEscape($field) .'`=\''. self::innerEscape($value) .'\', ';

					}

					$SQL_1 = '('. rtrim($SQL_1, ', ') .')'; 

					$SQL_2 = '('. rtrim($SQL_2, ', ') .')';

					$SQL_3 = rtrim( $SQL_3, ', ');

					$SQL = 'INSERT INTO `'. self::innerEscape( self::$sql_hash_table ) .'` '. $SQL_1 . ' VALUES '. $SQL_2 .' ON DUPLICATE KEY UPDATE '. $SQL_3 . ';';

				}

				break;

			// Join query
			case 'j':

				self::$sql_action = 'join';

				$SQL = 'SELECT ';

				foreach( $dbx_f as $table => $field ) {

					$SQL .= '`'. self::innerEscape($dbx_t[$table]) .'`.*';

					$SQL .= $table < count($dbx_f) - 1 ? ', ' : ' FROM `' . self::innerEscape($dbx_t[0]) .'` USE INDEX(PRIMARY) ';

				}

				foreach( $dbx_f as $table => $field ) {

				if( (bool)$table ) { 

					$SQL .= 'INNER JOIN `' . self::innerEscape($dbx_t[$table]) .'` ON(';

						if( isset($field['criterion_field']) && isset($field['linked_field']) && isset($field['linked_table']) && isset($field['criterion_table']) ) {

							$SQL .= '`'. self::innerEscape($field['criterion_table']) .'`.'. $field['criterion_field'] .'='. '`'. self::innerEscape($field['linked_table']) .'`.'. $field['linked_field'] .' ';

							$SQL .= 'AND ';

						}

						$SQL = trim($SQL, 'AND ') .') ';

					}

				}

				$SQL = ''. trim($SQL) .';';

				break;

			// Expert join query
			case 'xj':

				self::$sql_action = 'xjoin';

				$SQL = 'SELECT DISTINCT ';

				$SQL_J = [ 

					0 => 'PRIMARY'

				];

				$SQL_I = '';

				$SQL_T = [];

				$c = 0;

				$dbx_g = $dbx_f;

				$group = '';

				foreach( $dbx_t as $table ) {

					unset( 

						$dbx_g[ $c ]['index'], 
						$dbx_g[ $c ]['criterion_table'], 
						$dbx_g[ $c ]['criterion_field'], 
						$dbx_g[ $c ]['linked_table'], 
						$dbx_g[ $c ]['linked_field'], 
						$dbx_g[ $c ]['group_field'],
						$dbx_g[ $c ]['where_field'] 

					);

					foreach( $dbx_g[ $c ] as $f => $fv ) {

						$SQL_T[ self::innerEscape( $table ) ] = '`'. self::innerEscape( $table ) .'`';

						$SQL_I .= '`'. self::innerEscape( $table ) .'`.'. $fv .', ';

					}

					$c++;

				}

				$SQL .= rtrim($SQL_I, ', ');

				foreach( $dbx_f as $table => $index ) {

					if( (bool)$table ) { 

						if( isset($index['index']) ) {

							$SQL_J[ $index['index'] ] = $index['index']; 

						}

					}

				}

				$SQL .= ' FROM `'. self::innerEscape( $dbx_t[0] ) .'` USE INDEX('. ltrim(implode(', ', $SQL_J), ', ') .') ';

				foreach( $dbx_f as $table => $field ) {

					if( (bool)$table ) { 

						$SQL .= 'INNER JOIN `'. self::innerEscape( $dbx_t[ $table ] ) .'` ON(';

						unset( $field['index'] );

						if( isset($field['criterion_field']) && isset($field['linked_field']) && isset($field['linked_table']) && isset($field['criterion_table']) ) {

							$SQL .= '`'. self::innerEscape($field['criterion_table']) .'`.'. $field['criterion_field'] .'='. '`'. self::innerEscape($field['linked_table']) .'`.'. $field['linked_field'] .' ';

							$SQL .= 'AND ';

						}

						if( isset($field['where_field']) ) {

							$where_sql_clause = explode('::', $field['where_field']);

							$SQL_WHERE = ' WHERE `'. self::innerEscape( $where_sql_clause[0] ) . '`.'. self::innerEscape( $where_sql_clause[1] );

							$where = $where_sql_clause[2] === '*' ? "<>''" : '=\''. self::innerEscape( $where_sql_clause[2] ) .'\'';

							$SQL_WHERE .= $where;

						}

						if( isset($field['group_field']) ) {

							$group = ' GROUP BY '. '`'. self::innerEscape( $dbx_t[ $table ] ) .'`.'. $field['group_field'] .' ';

						}

						$SQL = trim($SQL, 'AND ') .') ';

					}

				}

				$SQL = trim($SQL) . $SQL_WHERE . $group .';';

				break;

			// Delete query
			case 'x':

				self::$sql_action = 'rehash';

				$SQL = 'DELETE FROM '. $dbx_t .' WHERE ';

				foreach( $dbx_f as $k => $v ) {

					foreach( $v as $c => $x) {

						if( $c === 'criterion_field' ) {

							$SQL_0 = '`'. self::innerEscape($x) .'`';

						}

						if( $c === 'criterion_value' ) {

							$SQL_1 .= '=\''. self::innerEscape($x) . '\'';

						}

					}

				}

			$SQL .= $SQL_0 . $SQL_1 . ';';

			break;

			// Remove query
			case 'r':

				self::$sql_action = 'rehash';

				$SQL = 'DELETE FROM '. $dbx_t .' WHERE ';

				foreach( $dbx_f as $k => $v ) {

					switch( $k ) {

						case 'criterion_field':
						case 'criterion_value':

							if( $k === 'criterion_field' ) {

								$SQL_0 .= '`'. self::innerEscape($v) .'`=';

							}

							if( $k === 'criterion_value' ) {

								$SQL_1 .= '\''. self::innerEscape($v) .'\'';

							}

							break;
					}

				}

				$SQL .= $SQL_0 . $SQL_1 . ';';

				break;

			// Expert query for user defined SQL is not presented in API for now
			case 'p':

				self::$sql_action = 'parametrized_select';

				$SQL = '';

				foreach( $dbx_f as $k => $v ) {

					if ( $k === 'extra_select_sql' ) {

						$SQL = $v; // be careful because unescaped query may inject something

					}

				}

				break;

		}

		self::$sql_query = $SQL;

		self::executionSQL($SQL);

	}

	// Open Data Base host
	protected static function connect(): iterable {

		$dbx_link = mysqli_init();

		// Establish connections with SSL cert
		if( self::$useSSL ) {

			if( self::$path_to_cert ) {

				$dbx_link->ssl_set(

					null, null,

					self::$path_to_cert, 

					null, null

				);

			}

			$dbx_link->real_connect(

				self::$host,
				self::$user,
				self::$pass,
				self::$name,
				self::$port,

				null,

				MYSQLI_CLIENT_SSL | MYSQLI_CLIENT_COMPRESS

			);

		}
		else {

			$dbx_link->real_connect(

				self::$host,
				self::$user,
				self::$pass,
				self::$name,
				self::$port,

				null,

				MYSQLI_CLIENT_COMPRESS

			);

		}

		if( !$dbx_link ) {

			$s = [

				'CONNECTION' => null,
				'DEBUG'		 => mysqli_connect_error() .' [#'. mysqli_connect_errno() .']'

			];

		}
		else {

			$dbx_link->set_charset('utf8');

			$s = [

				'CONNECTION' => true,

				'SERVER'	=> $dbx_link->server_info,
				'HOST'		=> $dbx_link->host_info

			];

			self::$connect_counter++;

		}

		self::$dbx_lnk = [ $dbx_link, $s ];

		// Store info
		return [ $dbx_link, $s ];

	}

	protected static function setHash( string $hash ): string {

		self::$sql_action = '';

		if( is_readable( self::$cache_directory_path . self::$sql_hash_table .'.hash' ) ) {

			unlink( self::$cache_directory_path . self::$sql_hash_table .'.hash' );

		}

		return 'UPDATE `hash__'. self::innerEscape( self::$sql_hash_table ) .'` SET `hash`=\''. $hash .'\' WHERE `id`=\'1\';';

	}

	protected static function getHashes( iterable $tables ): iterable {

		$hashes = [];

		foreach( $tables as $table ) {

			$hashFile = self::$cache_directory_path . $table .'.hash';

			// Get hash from file if present
			if( is_readable( $hashFile ) ) {

				$hashes[] = file_get_contents( $hashFile );

				self::$queries_hash_counter++;

			}
			else {

				$hash = self::stmtSQL( 'SELECT `hash` FROM `hash__'. $table .'` LIMIT 1;', true )[ 0 ]['hash'];

				$hashes[] = $hash;

				// Put hash into file
				file_put_contents( $hashFile, $hash );

			}

		}

		return $hashes;

	}

	// Describe table
	protected static function describe(): ?iterable {

		return self::stmtSQL('DESCRIBE '. self::$sql_hash_table .';', true);

	}

	// Get tables difference
	protected static function getDiff( iterable $existed_table_sruct ): iterable {

		$struct = [];

		$kcount = 0;

		foreach( $existed_table_sruct as $field ) {

			$field_name = '';

			foreach( $field as $f => $p ) {

				if( $f === 'Field') {

					$field_name = $p;

				}

                if( $f === 'Type' ) {

					$struct[ $field_name ]['type'] = strtoupper(explode('(', $p)[0]);

					$length = preg_replace('/[^0-9]/', '', $p);

					if( (bool)strlen($length) ) {

						$struct[ $field_name ]['length'] = $length;

					}

				}

				if( $f === 'Null' ) {

					$struct[ $field_name ]['fill'] = $p === 'YES' ? 0 : 1;

				}

				if( $f === 'Key' ) {

					$field_extra_1 = $p;

					if( strlen($field_extra_1) === 3 ) {

						$kcount++;

					}

				}

				if( $f === 'Extra' ) {

					$field_extra_2 = $p;

				}

				if( strlen($field_extra_1) === 3 && strlen($field_extra_2) === 14 ) {

					$struct[ $field_name ]['auto'] = 1;

				}
				else {

					$struct[ $field_name ]['auto'] = strlen($field_extra_1) === 3 ? 1 : 0;

				}

			}

		}

		$nstruct = [];

		foreach( self::$struct as $field => $ff ) {

			$field_name = $field;

			foreach( $ff as $f => $v ) {

				if( $f === 'auto' ) {

					$nstruct[ $field_name ]['auto'] = strlen($v) ? 1 : 0;

				} 

				if( $f === 'length' ) { 

					$nstruct[ $field_name ]['length'] = $v;    

				} 

				if( $f === 'fill' ) {

					$nstruct[ $field_name ]['fill'] = strlen($v) ? 1 : 0;

				} 

				if( $f === 'type' ) { 

					$field_type = '';

					switch ($v) {

						case 'text':

						$field_type = 'VARCHAR'; 

						break;

					case 'bigtext':

						$field_type = 'MEDIUMTEXT'; 

						break;

					case 'longtext':

						$field_type = 'LONGTEXT';

						break;

					case 'minnum':

						$field_type = 'SMALLINT';

						break;

					case 'num':

						$field_type = 'INT'; 

						break; 

					case 'bignum':

						$field_type = 'BIGINT';

						break;

					case 'floatnum':

						$field_type = 'FLOAT';

						break;

					case 'date':

						$field_type = 'DATE';

						break;

					case 'time':

						$field_type = 'TIME'; 

						break;

					case 'stamp':

						$field_type = 'TIMESTAMP'; 

						break;

					}

					$nstruct[ $field_name ]['type'] = $field_type;

				}

			}

		}

		return [ $struct, $nstruct, $kcount ];

	}

	// Prepeare and execute SQL statement 
	protected static function stmtSQL( string $sql, ?bool $m ): ?iterable {

		$storeResult = null;

		/* Log MySQL */
		file_put_contents( $_SERVER['DOCUMENT_ROOT'] .'/private/SQL_log.txt', $sql ."\n\n", FILE_APPEND );

		// Establish connection with Data Base server host
		self::connect();

		// Store info
		self::$result['info'] = self::$dbx_lnk[1];

		if( (bool)self::$dbx_lnk[ 1 ]['CONNECTION'] ) {

			if( self::$strict ) {

				$result = self::$dbx_lnk[ 0 ]->real_query( 'SET SESSION sql_mode=\'STRICT_ALL_TABLES\';' );

			} 
			else {

				$result = self::$dbx_lnk[ 0 ]->real_query( 'SET SESSION sql_mode=\'\';' );

			}

			// Set DataBase engne options
			if( self::$sql_action === 'create' ) { 

				$result = self::$dbx_lnk[ 0 ]->real_query('SET default_storage_engine=INNODB;');

				$result = self::$dbx_lnk[ 0 ]->real_query('SET GLOBAL innodb_file_format=Barracuda;');

				$result = self::$dbx_lnk[ 0 ]->real_query(

					$sql

				);

				$result = self::$dbx_lnk[ 0 ]->real_query('INSERT INTO `hash__'. self::innerEscape( self::$sql_hash_table ) .'` (`id`, `hash`) VALUES (\'1\', \'0\')');

			}

			$chash = md5( $sql );

			$hash = 'prepared::'. $chash;

			if( self::$sql_action !== 'create' ) { 

				$context = self::$dbx_lnk[ 0 ]->stmt_init();

				self::$pq_hash[] = $chash;

				$storeResult = [];

				$sgmntx = explode('SELECT DISTINCT `', $sql);

				$format = isset( $sgmntx[ 1 ] ) ? 'extra' : 'simple';

				if( $context->prepare( self::escapeOuter( $sql ) ) ) { 

					$context->execute();

					self::$queries_counter++;

					$R = $context->get_result();

					if( is_object( $R ) ) {

						if( $format === 'simple' ) {

							while( $row = mysqli_fetch_assoc( $R ) ) {

								$storeResult[] = $row;

							}

						}
						else {

		                    $fields = mysqli_fetch_fields( $R );

		                    $row_count = 0;

							while( $row = mysqli_fetch_array($R, MYSQLI_NUM) ) {

								$fields_count = 0;

								foreach ($row as $field) {

									$row_data[ $fields[ $fields_count ]->table ][ $fields[ $fields_count ]->name ] = $field;

									$fields_count++;

								}

								$storeResult[ $row_count ] = $row_data;

								$row_count++;

							}

						}

						$R->free();

					}
					else {

						$storeResult = null;

					}

					// Logging prepared SQL
					if( (bool)$context->errno ) {

						self::$result['info']['!Q'][ $hash ]['MESSAGES']['errors']['error'] = $context->error .' :: '. $context->errno; 

						if( count($context->error_list) ) {

							self::$result['info']['!Q'][ $hash ]['MESSAGES']['errors']['list'] = $context->error_list;

						}

					}

					if( (bool)$context->field_count ) {

						self::$result['info']['!Q'][ $hash ]['CHANGES_R:F'] = '['. $context->affected_rows .':'. $context->field_count .']';

					}
	 
					self::$result['info']['!Q'][ $hash ]['QUERY'] = $sql;


					$context->close();

				}

			}

			// Logging common SQL
			if( !in_array($chash, self::$pq_hash) ) {

				if( (bool)self::$dbx_lnk[ 0 ]->warning_count ) {

					self::$result['info']['!Q'][ 'common::'. $chash ]['MESSAGES']['warnings'] = self::$dbx_lnk[ 0 ]->warning_count;

				}

				if( (bool)self::$dbx_lnk[ 0 ]->errno ) {

					self::$result['info']['!Q'][ 'common::'. $chash ]['MESSAGES']['errors']['error'] = self::$dbx_lnk[ 0 ]->error .' :: '. self::$dbx_lnk[ 0 ]->errno; 

					if( count(self::$dbx_lnk[ 0 ]->error_list) ) {

						self::$result['info']['!Q'][ 'common::'. $chash ]['MESSAGES']['errors']['list'] = self::$dbx_lnk[ 0 ]->error_list;

					}

				}

				if( (bool)self::$dbx_lnk[ 0 ]->field_count && (bool)self::$dbx_lnk[ 0 ]->affected_rows ) {

					self::$result['info']['!Q'][ 'common::'. $chash ]['CHANGES_R:F' ] = self::$dbx_lnk[ 0 ]->affected_rows .':'. self::$dbx_lnk[ 0 ]->field_count;

				}

				if( (bool)self::$dbx_lnk[ 0 ]->field_count && (bool)self::$dbx_lnk[ 0 ]->affected_rows ) { 

					self::$result['info']['!Q'][ 'common::'. $chash ]['QUERY'] = self::$sql_query;

				}

			}

			// Drop Data Base host connection
			self::disconnect( self::$dbx_lnk );


		}

		if( $m ) {

			return $storeResult;

		}
		else {

			self::$result['result'] = $storeResult;

			return null;

		}

	}

	// Execute SQL
	protected static function executionSQL( string $SQL ): void {

		if( self::$sql_action === 'create' ) { 

			if( self::$sql_hash_table !== 'revolver__statistics' ) {

				self::stmtSQL('CREATE TABLE IF NOT EXISTS `hash__'. self::$sql_hash_table .'` (id INT(255) AUTO_INCREMENT PRIMARY KEY NOT NULL, hash VARCHAR(500));', null);

			}

			self::stmtSQL($SQL, null);

		}

		if( self::$sql_action === 'drop' ) {

			self::stmtSQL('DROP TABLE `hash__'. self::$sql_hash_table .'`;', null);

		}

		// SELECT QUERIES
		if( in_array(self::$sql_action, ['hash', 'parametrized_select'], true) && self::$sql_action !== 'join' ) {

			if( self::$sql_hash_table !== 'revolver__statistics' ) {

				$currentHash = self::getHashes( [self::$sql_hash_table] )[0];

				$hash = (bool)$currentHash ? $currentHash : null;

				$qhash = md5( $SQL ) .'-'. $hash .'-'. self::$sql_hash_table;

				$cache = self::getCache($qhash);

			}
			else {

				$hash = 'revolver_ignore_cache';

			}

			if( $cache[1] ) {

				self::$result['result'] = $cache[0];

			}
			else {

				if( $hash ) {

					if( self::$sql_hash_table === 'revolver__nodes' && self::$sql_action !== 'parametrized_select' ) { 

						$SQL = str_replace('ASC', 'DESC', $SQL);

					}

					self::stmtSQL($SQL, null); 

					if( self::$sql_hash_table !== 'revolver__statistics' ) {

						self::saveCacheFiles( self::$result['result'], $qhash );

					}

				}

			}

		}
		else {

			if( self::$sql_action === 'join' ) {

				$qhash = md5($SQL) .'-';

				foreach( self::$sql_hash_table as $hashTable ) {

					$hash = self::getHashes( [$hashTable] );

					foreach( $hash as $h ) {

						$qhash .= (bool)$h ? $hashTable .'-'. $h .'-' : null;

					}

				}

				$qhash = rtrim($qhash, '-');

				$cache = self::getCache($qhash);

				if( $cache[1] ) {

					self::$result['result'] = $cache[0];

				}
				else {

					if( $qhash ) {

						// Execute simple join
						self::stmtSQL( $SQL, null );

						// Save caches
						self::saveCacheFiles( self::$result['result'], $qhash );

					}

				}

			} 

			if( self::$sql_action === 'xjoin' ) {

				$qhash = md5($SQL) .'-';

				foreach( self::$sql_hash_table as $hashTable ) {

					$hash = self::getHashes( [$hashTable] );

					foreach( $hash as $h ) {

						$qhash .= (bool)$h ? $hashTable .'-'. $h .'-' : null;

					}

				}

				$qhash = rtrim($qhash, '-');

				$cache = self::getCache($qhash);

				if( $cache[1] ) {

					self::$result['result'] = $cache[0];

				}
				else {

					if( $qhash ) {

						// Execute xJoin
						self::stmtSQL( $SQL, null );

						// Save caches
						self::saveCacheFiles( self::$result['result'], $qhash );

					}

				}

			}
			else {

				$flag_allow_query = true;

				switch( self::$sql_action ) {

					case 'parametrized_select':
					case 'create':
					case 'join':
					case 'xjoin':

						$flag_allow_query = null;

					break;

				}

				if( $flag_allow_query ) {

					self::stmtSQL( $SQL, null );

				}

			}

		}

		if( self::$sql_action === 'rehash' ) {

			if( self::$sql_hash_table !== 'revolver__statistics' ) {

				self::$sql_actual_hash = self::stmtSQL('CHECKSUM TABLE `'. self::$sql_hash_table .'`;', true)[ 0 ]['Checksum'];

				// Define matched for changes of cache files mask
				$cacheFilesMask = null; 

				$prefix = 'revolver__';

				switch ( self::$sql_hash_table ) {

					case $prefix .'categories':

						$cacheFilesMask = 'homepage|contents|categories';

						break;

					case $prefix .'nodes':

						$cacheFilesMask = 'homepage|contents|categories|aggregator|sitemap';

						break;

					case $prefix .'comments':

						$cacheFilesMask = 'homepage|contents';

						break;

					case $prefix .'users':

						$cacheFilesMask = 'user';

						break;

					case $prefix .'subscriptions':

						$cacheFilesMask = 'contents';

						break;

				}

				if( $cacheFilesMask ) {

					foreach( self::getCachesList( true ) as $fnumber => $fname ) {

						if( preg_match('/(^'. $cacheFilesMask .')\w*.?/is', $fname) ) {

							unlink( TCache . $fname );

						}

					}

				}

				self::stmtSQL(

					self::setHash( self::$sql_actual_hash ), null

				);

			}
			else {

				self::$sql_actual_hash = '0';

			}

		}

		// Collect table info
		if( self::$sql_action === 'describe' ) {

			self::stmtSQL( $SQL, null );

		}

		// Refresh indexes
		if( self::$sql_action === 'index' ) {

			if( self::$sql_action !== 'create' ) {

				foreach( ['full', 'spatial', 'unique', 'simple'] as $i ) {

					self::stmtSQL( 'DROP INDEX `'. self::$sql_hash_table .'_INDEX_'. $i .'` ON `'. self::$sql_hash_table .'`;', null );

				}

				self::stmtSQL( $SQL, null );

			}

		}

		// Modify tables
		if( self::$sql_action === 'alter' ) { 

			// [0] - from db
			// [1] - from sructure
			$diff  = self::getDiff(

				self::describe()

			);

			$diff_match_add     = array_intersect_assoc( $diff[0], $diff[1] );

			$diff_match_remove  = array_diff_assoc( $diff[0], $diff[1] );

			// Remove fields
			if( count($diff_match_remove) ) {

				foreach( $diff_match_remove as $field_name => $fn ) {

					self::stmtSQL( 'ALTER TABLE `'. self::$sql_hash_table .'` DROP COLUMN `'. $field_name .'`;', null );

				}

			}

			// Add fields
			if( count($diff_match_add) !== count($diff[1]) ) {

				foreach( array_diff_assoc( $diff[1], $diff_match_add) as $field_name => $fn ) {

					$length = isset( $fn['length'] ) || (bool)$fn['length'] ? '('. $fn['length'] .')' : '';

					$fill   = (bool)$fn['fill'] ? ' NOT NULL' : ' NULL';

					$auto   = isset($fn['auto']) && (bool)$fn['auto'] ? ' AUTO_INCREMENT PRIMARY KEY NOT NULL' : '';

					if( (bool)$fn['auto'] ) {

						$fill = str_ireplace( ['NOT NULL', 'NULL'], ['', ''], $fill );

					}

					self::stmtSQL('ALTER TABLE `'. self::$sql_hash_table .'` ADD '. $field_name .' '. $fn['type'] . $length . $auto . $fill .';', null);

				}

			}

			// [0] - from db
			// [1] - from sruct

			$diff  = self::getDiff(

				self::describe()

			);

			// Fields to be converted
			if( count($diff[ 0 ]) === count($diff[ 1 ]) ) {

				foreach( $diff[1] as $f => $fn ) {

					$difference_fields = array_diff_assoc( $diff[ 1 ][ $f ], $diff[ 0 ][ $f ] );

					if( count($difference_fields) ) {

						$AUTO = $FILL = $TYPE = $LENGTH = $DROP = '';

						$FILL_FLAG = true;

						if( !isset($difference_fields['auto']) ) {

							if( (bool)$diff[ 0 ][ $f ]['auto'] ) {

								$AUTO = ' AUTO_INCREMENT NOT NULL';

								if( (bool)$diff[2] ) {

									$DROP = ' DROP PRIMARY KEY,';

								}

								$FILL_FLAG = null;

							}

							if ( !(bool)$diff[ 0 ][ $f ]['auto'] ) {

								$AUTO = '';

							}

						}
						else {

							if( (bool)$diff[ 0 ][ $f ]['auto'] && !(bool)$difference_fields['auto'] ) {

								if( (bool)$diff[2] ) {

									$DROP = ' DROP PRIMARY KEY,';

								}

							}

							if( (bool)$diff[ 1 ][ $f ]['auto'] && (bool)$difference_fields['auto'] ) {

								if( (bool)$diff[2] ) {

									$DROP = ' DROP PRIMARY KEY,';

								}

								$AUTO = ' AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (`'. self::innerEscape($f) .'`)';

								$FILL_FLAG = null;

							}

						}

						// Null or not propertie
						if( isset($difference_fields['fill']) ) {

							if( $FILL_FLAG ) {

								$FILL = (bool)$difference_fields['fill'] ? ' NOT NULL' : ' NULL';

							}

						}
						else {

							if( $FILL_FLAG ) { 

								$FILL = (bool)$diff[0][$f]['fill'] ? ' NOT NULL' : ' NULL';

							}

						}

						// Type propertie
						if( isset($difference_fields['type']) ) {

							$TYPE = ' '. $difference_fields['type'];

						} 
						else {

							$TYPE = ' '. $diff[0][$f]['type'];

						}

						// Length propertie
						if( isset($difference_fields['length']) ) {

							$LENGTH = '('. $difference_fields['length'] .')';

						}
						else {

							if( isset($diff[0][$f]['length']) ) {

								$LENGTH = '('. $diff[0][$f]['length'] .')';

							}

						}

						self::stmtSQL('ALTER TABLE `'. self::$sql_hash_table .'`'. $DROP .' MODIFY `'. self::innerEscape( $f ) .'`'. $TYPE . $LENGTH . $FILL . $AUTO .';', null);

					}

				}

			}

		}

	}

	// Get chunked cache into one piece
	protected static function getCache( string $hash ): ?iterable {

		$chunks = (int)self::getCacheFile( self::$cache_directory_path . $hash .'.chunks' );

		$caches = [];

		$c = null;

		if( $chunks >= 0 ) {

			if( !(bool)$chunks ) {

				$file = self::getCacheFile( self::$cache_directory_path . $hash .'.cache' );

				if( strlen($file) ) {

					$caches = json_decode(

						explode( '#cache#', $file )[1], true

					);

					$c = true;

				}

			}
			else {

				for( $i = 0; $i < $chunks; $i++ ) {

					$segm = !(bool)$chunks ? '.cache' : '_'. $i .'.cache';

					$file = self::getCacheFile( self::$cache_directory_path . $hash . $segm );

					if( strlen( $file ) ) {

						if( (bool)$chunks ) {

							$xchunks = json_decode(

								explode( '#cache#', $file )[1], true

							);

							foreach( $xchunks as $chunk ) {

								$caches[] = $chunk;

							}

						}
						else {

							$caches = json_decode(

								explode( '#cache#', $file )[1], true

							);

						}

						$c = true;

					}

				}

			}

		}

		return [ $caches, $c ];

	} 

	// Get caches
	public static function getCachesList( ?bool $mode = null ): iterable {

		$i = 0;

		$cache_dir = $mode ? rtrim( TCache, '/' ) : self::$cache_directory_path; 

		if( is_readable( $cache_dir ) ) {

			foreach( scandir( $cache_dir ) as $v ) {

				if( !in_array( $v, ['.', '..'] ) && !is_dir( $cache_dir .'/'. $v ) ) {  

					yield $i++ => $v . ( !$mode ? '|'. filemtime( $cache_dir .'/'. $v ) : '' );

				}

			}

		}

	}

	// Get cache from files
	protected static function getCacheFile( string $f ): ?string {

		if( is_readable( $f ) ) {

			$cache = self::$encrypted_cache ? self::$cipher::crypt( 'decrypt', file_get_contents( $f ) ) : file_get_contents( $f );

			if( preg_match('/\.chunks/i', $f) ) {

				if( strlen( $cache ) ) {

					self::$queries_cache_counter++;

				}

			}

			return $cache;

		}
		else {

			return null;

		}

	}

	// Save cache into files
	protected static function saveCacheFiles( iterable $r, string $f ): void {

		foreach( self::getCachesList() as $fnumber => $fname ) {

			$FileName = explode('|', $fname)[0];

			if( preg_match('/'. explode('-', $f)[0] .'/i', $FileName) ) {

				unlink( self::$cache_directory_path . $FileName );

			}

		}

		if( !preg_match('/revolver__statistics/i', $f) && (bool)count($r) ) {

			$chunksCount = count($r) / self::$sql_cache_segments;

			if( $chunksCount > 0 ) { 

				self::saveCacheFile( $f .'#cache#'. json_encode( $r ), self::$cache_directory_path . $f .'.cache' );
				self::saveCacheFile( 0, self::$cache_directory_path . $f .'.chunks' );

			}
			else {

				$payload = 0;

				$chunks = array_chunk( $r, $chunksCount, true );

				self::saveCacheFile( count($chunks), self::$cache_directory_path . $f .'.chunks' );

				foreach( $chunks as $chunk ) {

					self::saveCacheFile( $f .'#cache#'. json_encode( $chunk ), self::$cache_directory_path . $f .'_'. $payload .'.cache' );

					$payload++;

				}

			}

		}

	}

	// Save cache file
	protected static function saveCacheFile( string $r, string $f ): void {

		$rsc = fopen( $f, 'w' );

		fwrite( $rsc, self::$encrypted_cache ? self::$cipher::crypt('encrypt', $r) : $r );

		fclose( $rsc );

	}

	// Complite some list values for create query and insert query
	private static function compileListValues( iterable $l, string $q ): string {

		$SQL = '';

		$c = 0;

		foreach( $l as $f => $v ) {

			if( $q === 'c' || $q === 'xc' ) {

				if( (bool)$c ) {

					$SQL .= ', ';

				}

				$SQL .= '`'. $f .'` '. self::parseFieldsSyntax( $q, $v );

				$c++;

			}

			if( $q === 'i' ) {

				if( (bool)$c ) {

					$SQL .= ', ';

				}

				$SQL .= '`'. $f . '`';

				$c++;

			}

		}

		return $SQL;

	}

	// Complite indexes
	private static function compliteIndexes( string $dbx_t, iterable $dbx_f ): string {

		// Alter stack
		$SQL_I = [];

		// Collect indexing fields
		foreach( $dbx_f as $k => $v ) {

			if( isset( $v['index']['type'] ) ) {

				$SQL_I[ $v['index']['type'] ][] = '`'. $k .'`';

			}

		}

		if( (bool)count($SQL_I) ) {

			 $SQL .= ', ';

		}

		// Make SQL string
		foreach( $SQL_I as $k => $v ) {

			switch( $k ) {

				case 'simple':

					$SQL .= ' INDEX `'. $dbx_t .'_INDEX_'. $k .'`';

					break;

				case 'unique':

					$SQL .= ' UNIQUE KEY `'. $dbx_t .'_INDEX_'. $k .'`';

					break;

				case 'spatial':

					$SQL .= ' SPATIAL INDEX `'. $dbx_t .'_INDEX_'. $k .'`';

					break;

				case 'full':

					$SQL .= ' FULLTEXT KEY `'. $dbx_t .'_INDEX_'. $k .'`';

					break;

			}

			$SQL .= ' (';

			if( in_array( $k, [ 'simple', 'unique' ] ) ) {

				$SQL .=	'`field_id`, ';

			}

			foreach( $v as $f ) {

				$SQL .= $f .', ';

			}

			$SQL = rtrim($SQL, ', ') . ')'. ( !in_array( $k, [ 'spatial', 'full' ] ) ? ' USING BTREE, ' : ', ' ); // HASH

		}

		return rtrim( $SQL, ', ');

	}	

	// Complite some SQL entries with fields
	private static function parseFieldsSyntax( string $m, iterable $s ): string {

		$sql_0 = $sql_1 = $sql_2 = $sql_3 = '';

		$c = 0;

		foreach( $s as $k => $v ) {

			if( $m === 'c' || $m === 'xc' ) {

				if( $k === 'type' ) {

					switch( $v ) {

						case 'text':

							$sql_0 = 'VARCHAR'; 

							break;

						case 'bigtext':

							$sql_0 = 'MEDIUMTEXT'; 

							break;

						case 'longtext':

							$sql_0 = 'LONGTEXT';

							break;

						case 'minnum':

							$sql_0 = 'SMALLINT';

							break;

						case 'num':

							$sql_0 = 'INT'; 

							break; 

						case 'bignum':

							$sql_0 = 'BIGINT';

							break;

						case 'floatnum':

							$sql_0 = 'FLOAT';

							break;

						case 'date':

							$sql_0 = 'DATE';

							break;

						case 'time':

							$sql_0 = 'TIME'; 

							break;

						case 'stamp':

							$sql_0 = 'TIMESTAMP'; 

							break;

					}

				}

				if( $k === 'length' ) {

					$sql_1 = '('. $v .')';

				}

				if( $k === 'auto' && $auto_counter <= 0 ) {

					$sql_2 = 'AUTO_INCREMENT PRIMARY KEY';

					$c++;

				}

				if( $k === 'fill' && $k !== 'auto' ) {

					$sql_3 = 'NOT NULL';

				}

				$sql = $sql_0 . $sql_1 .' '. $sql_2 .' '. $sql_3 . ',';

				$sql = rtrim($sql, ',');

			}

		}

		return $sql;

	}

	// Cleanup outer SQL
	protected static function escapeOuter( string $s ): string {

		return preg_replace(

				['/(\/\*\s*\w*\s*(?!.\/\*))/si', '/(\-\-\s*\w*\s*(?!.\-\-))/si', '/(or\s*\w*\s*=\s*\w(?!.*or)|\|\|\s*\w*\s*=\s*\w(?!.\|\|))/si', '/[\x{10000}-\x{10FFFF}]/u'], 

				[';', ';', '', '\xEF\xBF\xBD'],

				str_replace(

					['--+', '"', "\x1a", '%', 'qq ', '--', '/*!', '*/'], 

					[';', '&quot;', '\\Z', "\%", '&#45;&#45;', '&#47;&#42;&#33;', '&#42;&#47;'], 

					trim($s),

				)

		);

	}

	// Cleanup inner SQL
	protected static function innerEscape( string $v ): string {

		// Secure stage means that inner SQL clauses fixed to be secure
		$secureStage = str_ireplace(

			[' OR ', '||', ' AND ', '&&', ' ON ', "'", '--+', 'qq', '"', '--', '%', '/*!', '*/'], 

			[' &#111;&#114; ', ' &#124;&#124; ', ' &#97;&#110;&#100; ', ' &amp;&amp; ', ' &#111;&#110; ', '\'', ';', '', '&quot;', '&#45;&#45;', '&percnt;', '&#47;&#42;&#33;', '&#42;&#47;'],

			addslashes(

				htmlspecialchars($v)

			)

		);

		// Not available to use built in escape future when DB connection not established
		if( isset( self::$dbx_lnk[ 1 ] ) ) {

			if( (bool)self::$dbx_lnk[ 1 ]['CONNECTION'] ) {

				return mysqli_real_escape_string( self::$dbx_lnk[ 0 ], $secureStage ); 

			}
			else {

				return $secureStage;

			}

		}
		else {

			return $secureStage;

		}

	}

	// Disconnect database
	protected static function disconnect( iterable $dbx ): void {

		mysqli_close( $dbx[ 0 ] );

		self::$dbx_lnk[ 1 ]['CONNECTION'] = null;

	}

}

?>
