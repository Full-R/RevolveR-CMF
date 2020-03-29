<?php

 /* 
  * Max Mind Data Base Decoder
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
  * Developer: { 1 } Dmitry Maltsev; 
  * .......... { 2 } Max Mind Developers.
  *
  * License: Apache 2.0
  *
  */

define('MM_MAX', log(PHP_INT_MAX, 2) - 1 / 8);

final class MMDBDecoder {

	// Pointers
	protected static $pointer_2;
	protected static $pointer_1;

	// Maximum config
	protected static $maximum;

	// Store Resource 
	protected static $resource;

	// Is Little Endian
	protected static $isPLE;

	protected static $allowD;

	// Logging
	public static $status = [];

	function __construct( iterable $r, int $p = 0 ) {

		self::$isPLE    = self::isPlatformLittleEndian();
		self::$maximum  = MM_MAX;
		self::$resource	= $r[0];

		self::$pointer_1 = $p;
		self::$pointer_2 = $p > 0 ? log($p, 2) / 8 : 0;

	}

	protected function __clone() {

	}

	public static function decode( int $o ): iterable {

		$ctrlByte = ord( self::utilRead($o, 1) );

		++$o;

		$t = $ctrlByte >> 5;

		// Pointers are a special case, we don't read the next $size bytes, we
		// use the size to determine the length of the pointer and then follow it.
		if( $t === 1 ) {

			list($p, $o) = self::decodePointer($ctrlByte, $o);

			return [ self::decode($p)[0], $o ];

		}

		if( !(bool)$t ) {

			$n = ord( self::utilRead($o, 1) );

			$t = $n + 7;

			if( $t < 8 ) {

				self::$status[] = 'Something went horribly wrong in the decoder. An extended type ' . 'resolved to a type number < 8 ('. $t .')'; 

			}

			++$o;

		}

		list($s, $o) = self::sizeFromCtrlByte($ctrlByte, $o);

		return self::decodeByType($t, $o, $s);

	}

	private static function utilRead( int $o, int $n ): string {

		if( !(bool)$n ) {

			return '';

		}

		if( is_resource( self::$resource ) ) {

			if( !(bool)fseek( self::$resource, $o ) ) {

				$v = fread( self::$resource, $n );

				// We check that the number of bytes read is equal to the number
				// asked for. We use ftell as getting the length of $value is much slower.
				if( ftell( self::$resource ) - $o === $n) {

					return $v;

				}

			}

		} 
		else {

			return '';

		}

		self::$status[] = 'The MaxMind DB file contains bad data';

	}

	protected static function decodeByType( int $t, int $o, int $s ): iterable {

		$r = [];

		switch ($t) {

			case 7:

				$r = self::decodeMap($s, $o);

				break;

			case 11:

				$r = self::decodeArray($s, $o);

				break;

			case 14:

				$r = [ 

					self::decodeBoolean($s), 
					$o

				];

				break;

		}

		$newOffset = $o + $s;

		$b = self::utilRead($o, $s);

		switch ($t) {

			case 4:
			case 2:

				$r = [ $b, $newOffset ];

				break;

			case 3:

				self::verifySize(8, $s);

				$r = [

					self::decodeDouble($b), 
					$newOffset

				];

				break;

			case 15:

				self::verifySize(4, $s);

				$r = [

					self::decodeFloat($b), 
					$newOffset

				];

				break;

			case 8:

				$r = [

					( !(bool)$s ? 0 : self::decodeInt32($b, $s) ), 
					$newOffset

				];

				break;

			case 5:
			case 6:
			case 9:
			case 10:

				$r = [

					self::decodeUint($b, $s), 
					$newOffset

				];

				break;

		}

		$r[] = self::$status; 

		return $r;

	}

	private static function verifySize( int $e, int $a ): void {

		if ($e !== $a) {

			self::$status[] = 'The MaxMind DB file\'s data section contains bad data (unknown data type or corrupt data)'; 

		}

	}

	protected static function decodeArray( int $s, int $o ): iterable {

		$a = [];

		for ($i = 0; $i < $s; ++$i) {

			list($v, $o) = self::decode($o);

			$a[] = $v;

		}

		return [ $a, $o ];

	}

	protected static function decodeBoolean( int $s ): ?bool {

		return (bool)$s ? true : null;

	}

	protected static function decodeDouble( string $b ): string {

		// This assumes IEEE 754 doubles, but most (all?) modern platforms use them.
		// We are not using the "E" format as that was only added in
		// 7.0.15 and 7.1.1. As such, we must switch byte order on
		// little endian machines.

		return unpack('d', self::maybeSwitchByteOrder($b))[1];

	}

	protected static function decodeFloat( string $b ): string {

		// This assumes IEEE 754 floats, but most (all?) modern platforms use them.
		// We are not using the "G" format as that was only added in
		// 7.0.15 and 7.1.1. As such, we must switch byte order on
		// little endian machines.

		return unpack('f', self::maybeSwitchByteOrder($b))[1];

	}

	protected static function decodeInt32( string $b, int $s ): string {

		switch ($s) {

			case 1:
			case 2:
			case 3:

				$b = str_pad($b, 4, "\x00", STR_PAD_LEFT);

				break;

			case 4:

				break;

			default:

				self::$status[] = 'The MaxMind DB file\'s data section contains bad data (unknown data type or corrupt data)'; 

		}

		return unpack('l', self::maybeSwitchByteOrder($b))[1];

	}

	protected static function decodeMap( int $size, int $o ): iterable {

		$m = [];

		for ($i = 0; $i < $size; ++$i) {

			list($k, $o) = self::decode($o);
			list($v, $o) = self::decode($o);

			$m[$k] = $v;

		}

		return [ $m, $o ];

	}

	protected static function decodePointer( int $ctrlByte, int $o ): iterable {

		$pointerSize = (($ctrlByte >> 3) & 0x3) + 1;

		$b = self::utilRead($o, $pointerSize);

		$o = $o + $pointerSize;

		switch ($pointerSize) {

			case 1:

				$p = unpack('n', chr($ctrlByte & 0x7) . $b)[1] + self::$pointer_1;

				break;

			case 2:

				$p = unpack('N', "\x00" . \chr($ctrlByte & 0x7) . $b)[1] + self::$pointer_1 + 2048;

				break;

			case 3:

				// It is safe to use 'N' here, even on 32 bit machines as the first bit is 0.
				$p = unpack('N', chr($ctrlByte & 0x7) . $b)[1] + self::$pointer_1 + 526336;

				break;

			case 4:

				// We cannot use unpack here as we might overflow on 32 bit machines
				$pointerOffset = self::decodeUint($b, $pointerSize);

				if ($pointerSize + self::$pointer_2 <= self::$maximum) {

					$p = $pointerOffset + self::$pointer_1;

				}
				else if (extension_loaded('gmp')) {

					$p = gmp_strval(gmp_add($pointerOffset, self::$pointer_1));

				}
				else if (extension_loaded('bcmath')) {

					$p = bcadd($pointerOffset, self::$pointer_1);

				}
				else {

					self::$status[] = 'The gmp or bcmath extension must be installed to read this database.';

			}

		}

		return [ $p, $o ];

	}

	protected static function decodeUint( string $bytes, int $byteLength ): int {

		if( !(bool)$byteLength ) {

			return 0;

		}

		$int = 0;

		for ($i = 0; $i < $byteLength; ++$i) {

			$part = ord($bytes[$i]);

			// We only use gmp or bcmath if the final value is too big
			if ($byteLength <= self::$maximum) {

				$int = ($int << 8) + $part;

			}
			else if (extension_loaded('gmp')) {

				$int = gmp_strval(gmp_add(gmp_mul($int, 256), $part));

			}
			else if (extension_loaded('bcmath')) {

				$int = bcadd(bcmul($int, 256), $part);

			}
			else {

				self::$status[] = 'The gmp or bcmath extension must be installed to read this database.';

			}

		}

		return $int;

	}

	protected static function sizeFromCtrlByte( int $ctrlByte, int $o ): iterable {

		$s = $ctrlByte & 0x1f;

		if ($s < 29) {

			return [ $s, $o ];

		}

		$bytesToRead = $s - 28;

		$b = self::utilRead($o, $bytesToRead);

		if ($s === 29) {

			$s = 29 + ord($b);

		}
		else if ($s === 30) {

			$s = 285 + unpack('n', $b)[1];

		}
		else if ($s > 30) {

			$s = unpack('N', "\x00" . $b)[1] + 65821;

		}

		return [ $s, $o + $bytesToRead ];

	}

	protected static function maybeSwitchByteOrder( string $b ): string {

		return self::$isPLE ? strrev($b) : $b;

	}

	protected static function isPlatformLittleEndian(): ?bool  {

		return 0x00FF === current(unpack('v', pack('S', 0x00FF)));

	}

}

?>
