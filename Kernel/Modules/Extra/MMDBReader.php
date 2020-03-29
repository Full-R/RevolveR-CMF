<?php

 /* 
  * Max Mind format Data Base Reader
  *
  * v.1.8.0
  *
  *
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
  * Developer: { 1 } Dmitry Maltsev; 
  * .......... { 2 } Max Mind Developers.
  *
  * License: Apache 2.0
  *
  */

final class Reader {

  // Max Mind Sign
  const SIGN = "\xAB\xCD\xEFMaxMind.com";

  // Databases
  protected static $dbdir;
  protected static $mmdbs = [

    'LOC' => [

      'file' => 'GeoLite2-City.mmdb',

    ],

    'ASN' => [

      'file' => 'GeoLite2-ASN.mmdb',

    ]

  ];

  public static $status = [];

  function __construct( ?string $dir = null ) {

    self::$dbdir = $dir ? $_SERVER['DOCUMENT_ROOT'] .'/private/IPDB/' : $_SERVER['DOCUMENT_ROOT'];

    // Generate initialize info
    foreach( self::$mmdbs as $dbn => $dbf ) {

      self::$mmdbs[ $dbn ][ 'init' ] = [

        'filepath' => self::$dbdir . $dbf['file'],

        'filesize' => filesize( self::$dbdir . $dbf['file'] ),

        'resource' => fopen(self::$dbdir . $dbf['file'], 'r')

      ];

      // Initialize metadata
      $fsSize = fstat( self::$mmdbs[ $dbn ]['init']['resource'] )['size'];

      $start = 0;

      for( $o = $fsSize - 14; $o >= ($fsSize - min(131072, $fsSize)); --$o ) { // 128 * 1024 = 128KB -- 132072

        if( (bool)fseek(self::$mmdbs[ $dbn ]['init']['resource'], $o) ) {

          break;

        }

        if ( fread(self::$mmdbs[ $dbn ]['init']['resource'], 14) === self::SIGN ) {

          $start = $o + 14;

        }

      }

      $metaDecoder = new MMDBDecoder([ self::$mmdbs[ $dbn ]['init']['resource'] ], $start);

      // Create metatdata
      $meta = [];

      foreach ( $metaDecoder->decode($start)[0] as $k => $v ) {

       $c = 0;
       $i = '';

       foreach( explode('_', $k) as $n ) {

          $i .= $c > 0 ? ucfirst($n) : $n;

          $c++;

        }

        $meta[ $i ] = $v; 

      }

      $meta['nodeByteSize']   = $meta['recordSize'] / 4;

      $meta['searchTreeSize'] = $meta['nodeCount'] * $meta['nodeByteSize'];

      self::$mmdbs[ $dbn ]['init']['metadata'] = (object)$meta;

      unset( $metaDecoder );

    }

  }

  protected function __clone() {

  }

  // Retrieves the record for the IP address.
  public static function get( string $ip, string $m = 'LOC' ): iterable {

    self::$mmdbs[ $m ]['init']['decode'] = new MMDBDecoder( 

      [

        self::$mmdbs[ $m ]['init']['resource']

      ], 
      
      self::$mmdbs[ $m ]['init']['metadata']->searchTreeSize + 16

    );

    return self::getWithPrefixLen($ip, $m)[0];

  }

  protected static function utilRead( string $m, int $o, int $numberOfBytes ): string {

    if ( !(bool)$numberOfBytes ) {

      return '';

    }

    $s = self::$mmdbs[ $m ]['init']['resource'];

    if ( !(bool)fseek($s, $o) ) {

      $v = fread($s, $numberOfBytes);

      // We check that the number of bytes read is equal to the number
      // asked for. We use ftell as getting the length of $value is
      // much slower.
      if (ftell($s) - $o === $numberOfBytes) {

        return $v;

      }

    }

    self::$status[] = 'The MaxMind DB file contains bad data';

  }

  // Retrieves the record for the IP address and its associated network prefix length.
  private static function getWithPrefixLen( string $ip, string $m ): iterable {

    if ( !filter_var($ip, FILTER_VALIDATE_IP) ) {

      self::$status[] = 'The value \'$ip\' is not a valid IP address.';

    }

    list($p, $prefixLen) = self::findAddressInTree($ip, $m);

    if ( !(bool)$p ) {

      return [ null, $prefixLen ];

    }

    $xbytes = self::resolveDataPointer($p, $m);

    self::$status = array_merge(self::$status, $xbytes[1]);

    return [ $xbytes[0], $prefixLen ];

  }

  private static function findAddressInTree( string $ip, string $m): iterable {

    $rawAddress = unpack('C*', inet_pton($ip));

    $bitCount = count($rawAddress) * 8;

    // The first node of the tree is always node 0, at the beginning of the value
    $node = 0;

    // Check if we are looking up an IPv4 address in an IPv6 tree. If this
    // is the case, we can skip over the first 96 nodes.
    if (self::$mmdbs[ $m ]['init']['metadata']->ipVersion === 6) {

      if ($bitCount === 32) {

        $node = self::ipV4StartNode($m);

      }

    } 
    else if (self::$mmdbs[ $m ]['init']['metadata']->ipVersion === 4 && $bitCount === 128) {            

      self::$status[] = 'Error looking up ipAddress. You attempted to look up an IPv6 address in an IPv4-only database.';

    }

    $nc = self::$mmdbs[ $m ]['init']['metadata']->nodeCount;

    for ($i = 0; $i < $bitCount && $node < $nc; ++$i) {

      $bit = 1 & (0xFF & $rawAddress[($i >> 3) + 1] >> 7 - ($i % 8));

      $node = self::readNode($node, $bit, $m);

    }

    if ($node === $nc) {

      // Record is empty
      return [ 0, $i ];

    } 
    else if ($node > $nc) {

      // Record is a data pointer
      return [ $node, $i ];

    }

    self::$status[] = 'Something wrong that Max Mind don\'t understand ... WTF?';

  }

  private static function ipV4StartNode( string $m ): int {

    // If we have an IPv4 database, the start node is the first node
    if (self::$mmdbs[ $m ]['init']['metadata']->ipVersion === 4) {

      return 0;

    }

    $node = 0;

    for ($i = 0; $i < 96 && $node < self::$mmdbs[ $m ]['init']['metadata']->nodeCount; ++$i) {

      $node = self::readNode($node, 0, $m);

    }

    return $node;

  }

  private static function readNode( int $nn, int $i, string $m ): int {

    $baseOffset = $nn * self::$mmdbs[ $m ]['init']['metadata']->nodeByteSize;

    switch (self::$mmdbs[ $m ]['init']['metadata']->recordSize) {

      case 24:

        return unpack('N', "\x00" . self::utilRead($m, $baseOffset + $i * 3, 3))[ 1 ];

      case 28:

        $bytes = self::utilRead($m, $baseOffset + 3 * $i, 4);

        if ( !(bool)$i ) {

            $middle = (0xF0 & ord($bytes[3])) >> 4;

        } 
        else {

            $middle = 0x0F & ord($bytes[0]);

        }

        return unpack('N', chr($middle) . substr($bytes, $i, 3))[ 1 ];

      case 32:

        return unpack('N', self::utilRead($m, $baseOffset + $i * 4, 4));

      default:

        self::$status[] = 'Unknown record size: '. self::$mmdbs[ $m ]['init']['metadata']->recordSize;

    }

  }

  private static function resolveDataPointer( int $p, string $m ): iterable {

    $resolved = $p - self::$mmdbs[ $m ]['init']['metadata']->nodeCount + self::$mmdbs[ $m ]['init']['metadata']->searchTreeSize;

    if ( $resolved >= self::$mmdbs[ $m ]['init']['filesize'] ) {

      self::$status[] = 'The MaxMind DB file\'s search tree is corrupt';

    }

    $r = self::$mmdbs[ $m ]['init']['decode']->decode($resolved); 

    unset( self::$mmdbs[ $m ]['init']['decode'] );

    return [ $r[0], $r[ count($r) - 1 ] ];

  }

  public static function close(): void {

    foreach (self::$mmdbs as $dbn => $dbp) {

      unset( self::$mmdbs[ $dbn ]['init']['decode'], self::$mmdbs[ $dbn ]['init']['metadata'] );

      if( is_resource( self::$mmdbs[ $dbn ]['init']['resource'] ) ) {

        fclose( self::$mmdbs[ $dbn ]['init']['resource'] );

      }

    }

  }

}

?>
