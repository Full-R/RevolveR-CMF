<?php

 /* 
  * 
  * RevolveR Markup Class
  *
  * Makes markup valid and secure
  *
  * v.1.9.0
  *
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

final class Markup {

	// Tags whitelist
	private static $allowed_tags = [

		// service tag
		'wrapper', 

		// extra
		'style',  
		'code', 
		'pre',

		// headings
		'h1', 
		'h2', 
		'h3', 
		'h4', 
		'h5', 
		'h6',

		// blocks
		'div',
		'p',

		// media
		'figcaption', 
		'figure', 
		'img',

		// tables
		'colgroup',
		'caption',
		'table', 
		'thead', 
		'tfoot', 
		'tbody',
		'col', 
		'th', 
		'tr', 
		'td', 

		// semantics
		'abbr',
		'acronym',
		'address',
		'blockquote',
		'time',
		'dfn',
		'q',
		
		// lists 
		'dl', 
		'ul', 
		'ol', 
		'dt', 
		'dd',
		'li', 

		// hyper
		'a', 

		// inline
		'strong', 
		'b', 
		
		'em', 
		'i',

		'sub',
		'sup',
		
		'mark',
		'small',
		'span',
		
		's', 
		'u',

		// omit tags
		//'meta',
		'br',
		'hr',

	];

	// Attributes whitelist
	private static $allowed_attr = [

		// query
		'class',
		'id',

		// uri
		'href',
		'src', 

		// info
		'title',
		'alt', 

		// view
		'style', 

		// align
		'target',
		'rel',

		// language
		'hreflang',
		'lang',

		// semantics scope
		'scope',

		// omit tags attrs
		//'charset',

		// Relation id for copyright protection and sense
		//'data-xhash',

	];

	// SVG tags (version 2020/01/13)
	private static $allowed_svg_tags = [

		'a',
		'animate',
		'animatemotion',
		'animatetransform',
		'circle',
		'clippath',
		'color-profile',
		'defs',
		'desc',
		'discard',
		'ellipse',
		'feblend',
		'fecolormatrix',
		'fecomponenttransfer',
		'fecomposite',
		'feconvolvematrix',
		'fediffuselighting',
		'fedisplacementmap',
		'fedistantlight',
		'fedropshadow',
		'feflood',
		'fefunca',
		'fefuncb',
		'fefuncg',
		'fefuncr',
		'fegaussianblur',
		'feimage',
		'femerge',
		'femergenode',
		'femorphology',
		'feoffset',
		'fepointlight',
		'fespecularlighting',
		'fespotlight',
		'fetile',
		'feturbulence',
		'filter',
		'foreignobject',
		'g',
		'hatch',
		'hatchpath',
		'image',
		'line',
		'lineargradient',
		'marker',
		'mask',
		'mesh',
		'meshgradient',
		'meshpatch',
		'meshrow',
		'metadata',
		'mpath',
		'path',
		'pattern',
		'polygon',
		'polyline',
		'radialgradient',
		'rect',
		'script',
		'set',
		'solidcolor',
		'stop',
		'style',
		'svg',
		'switch',
		'symbol',
		'text',
		'textpath',
		'title',
		'tspan',
		'unknown',
		'use',
		'view'
	
	];

	// SVG attributes (version 2020/01/13)
	private static $allowed_svg_attrs = [

		'accent-height',
		'accumulate',
		'additive',
		'alignment-baseline',
		'allowreorder',
		'alphabetic',
		'amplitude',
		'arabic-form',
		'ascent',
		'attributename',
		'attributetype',
		'autoreverse',
		'azimuth',
		'basefrequency',
		'baseline-shift',
		'baseprofile',
		'bbox',
		'begin',
		'bias',
		'by',
		'calcmode',
		'cap-height',
		'class',
		'clip',
		'clippathunits',
		'clip-path',
		'clip-rule',
		'color',
		'color-interpolation',
		'color-interpolation-filters',
		'color-profile',
		'color-rendering',
		'contentscripttype',
		'contentstyletype',
		'cursor',
		
		'cx',
		'cy',
		'd',
		
		'decelerate',
		'descent',
		'diffuseconstant',
		'direction',
		'display',
		'divisor',
		'dominant-baseline',
		'dur',
		'dx',
		'dy',
		'edgemode',
		'elevation',
		'enable-background',
		'end',
		'exponent',
		'externalresourcesrequired',
		
		'fill',
		'fill-opacity',
		'fill-rule',
		'filter',
		'filterres',
		'filterunits',
		
		'flood-color',
		'flood-opacity',
		
		'font-family',
		'font-size',
		'font-size-adjust',
		'font-stretch',
		'font-style',
		'font-variant',
		'font-weight',
		
		'format',
		'from',
		'fr',
		'fx',
		'fy',
		'g1',
		'g2',
		
		'glyph-name',
		'glyph-orientation-horizontal',
		'glyph-orientation-vertical',
		'glyphref',
		
		'gradienttransform',
		'gradientunits',
		
		'hanging',
		'height',
		'href',
		'hreflang',
		'horiz-adv-x',
		'horiz-origin-x',
		
		//'id',
		'ideographic',
		'image-rendering',
		'in',
		'in2',
		'intercept',
		'k',
		'k1',
		'k2',
		'k3',
		'k4',
		'kernelmatrix',
		'kernelunitlength',
		'kerning',
		'keypoints',
		'keysplines',
		'keytimes',
		'lang',
		'lengthadjust',
		'letter-spacing',
		'lighting-color',
		'limitingconeangle',
		'local',
		'marker-end',
		'marker-mid',
		'marker-start',
		'markerheight',
		'markerunits',
		'markerwidth',
		'mask',
		'maskcontentunits',
		'maskunits',
		'mathematical',
		'max',
		'media',
		'method',
		'min',
		'mode',
		'name',
		'numoctaves',
		'offset',
		'opacity',
		'operator',
		'order',
		'orient',
		'orientation',
		'origin',
		'overflow',
		'overline-position',
		'overline-thickness',
		'panose-1',
		'paint-order',
		'path',
		'pathlength',
		'patterncontentunits',
		'patterntransform',
		'patternunits',
		'ping',
		'pointer-events',
		'points',
		'pointsatx',
		'pointsaty',
		'pointsatz',
		'preservealpha',
		'preserveaspectratio',
		'primitiveunits',
		'r',
		'radius',
		'referrerpolicy',
		'refx',
		'refy',
		'rel',
		'rendering-intent',
		'repeatcount',
		'repeatdur',
		'requiredextensions',
		'requiredfeatures',
		'restart',
		'result',
		'rotate',
		'rx',
		'ry',
		'scale',
		'seed',
		'shape-rendering',
		'slope',
		'spacing',
		'specularconstant',
		'specularexponent',
		'speed',
		'spreadmethod',
		'startoffset',
		'stddeviation',
		'stemh',
		'stemv',
		'stitchtiles',
		'stop-color',
		'stop-opacity',
		'strikethrough-position',
		'strikethrough-thickness',
		'string',
		'stroke',
		'stroke-dasharray',
		'stroke-dashoffset',
		'stroke-linecap',
		'stroke-linejoin',
		'stroke-miterlimit',
		'stroke-opacity',
		'stroke-width',
		'style',
		'surfacescale',
		'systemlanguage',
		'tabindex',
		'tablevalues',
		'target',
		'targetx',
		'targety',
		'text-anchor',
		'text-decoration',
		'text-rendering',
		'textlength',
		'to',
		'transform',
		'type',
		
		'u1',
		'u2',
		
		'underline-position',
		'underline-thickness',
		'unicode',
		'unicode-bidi',
		'unicode-range',
		'units-per-em',
		
		'v-alphabetic',
		'v-hanging',
		'v-ideographic',
		'v-mathematical',
		'values',
		'vector-effect',
		//'version',
		'vert-adv-y',
		'vert-origin-x',
		'vert-origin-y',
		'viewtarget',
		'visibility',
		
		'width',
		'widths',
		'word-spacing',
		'writing-mode',
		
		'x',
		'x-height',
		'x1',
		'x2',
		
		'xchannelselector',
		'xlink:actuate',
		'xlink:arcrole',
		'xlink:href',
		'xlink:role',
		'xlink:show',
		'xlink:title',
		'xlink:type',
		
		'xml:base',
		'xml:lang',
		//'xml:space',
		//'xmlns:xlink',
		
		'viewbox',
		'xmlns',
		
		'y',
		'y1',
		'y2',
		'ychannelselector',
		'zoomandpan',

		'cx',
		'cy',
		'r'

	];

	// Current stacks
	public static $ctags;
	public static $cattrs;
	public static $mode;

	// Lazy list
	public static $lazyList = [];

	// Define parse instance
	protected static $parse;

	function __construct() {

		self::$parse = new Parse();

	}

	protected function __clone() {

	}

	// Make markup valid and secure
	public static function Markup(string $s, ?array $opts = null): string {

		$default = [

			'length' => 0,
			'xhash'  => 1,
			'lazy'	 => 0

		];

		if( $opts ) {

			$options = array_merge( $default, $opts );

		}
		else {

			$options = $default;

		}

		// Prepare given markup, makes it's valid and secure
		$pre = self::extract( self::crop( $s, $options['length'] ) );

		$pmc = '';

		if( (bool)count( $pre[0] ) ) {

			$pmc = self::markupSecure( // filter allowed markup and XSS

					self::markupFix(   // close opened tags

						$pre, true

					), (bool)$options['xhash'], 'HTML', (bool)$options['lazy']

				);

		}
		else {

			// Only trim by length because nothing to fix and filtrate
			$pmc = self::crop( $s, $options['length'] );

		}

		// Formating
		return preg_replace(

				[

					'/<(p|figure|figcaption|h1|h2|h3|h4|h5|h6|ul|ol|li|dl)+([^>]*?)([\s]*\>)/mi',
					'/<\/(figure|code|ol|ul|dl).*([^>])?/mi', 
					'/<\/(figcaption|pre|dt|dd|p|h1|h2|h3|h4|h5|h6)>?/mi',
					'/<(figure)+([^>]*?)([\s]*\>)/mi',
					'/<\/(ol|ul|dl).*([^>])?/mi'

				],

				[

					"\n" . '$0',
					'$0' . "\n\n",
					'$0' . "\n",
					'$0' . "\n",
					"\n" . '$0'
				],

				$pmc

			);

	}

	public static function CleanSVG(string $src, ?array $opts = null): string {

		self::$mode = 'SVG';

		$default = [

			'xhash'	=> 1,
			'b64'	=> 1 

		];

		if( $opts ) {

			$options = array_merge($default, $opts);

		}
		else {

			$options = $default;

		}

		$pre = self::extract( $src );

		$pmc = '';

		if( (bool)count( $pre[0] ) ) {

			$pmc = self::markupSecure( // filter allowed markup and XSS

					self::markupFix(   // close opened tags 

						$pre

					), (bool)$options['xhash'], 'SVG'

				);

		}
		else {

			$pmc = $src;

		}

		return (bool)$options['b64'] ? 'data:image/svg+xml;utf-8;base64,'. base64_encode( $pmc ) : $pmc;

	}

	// Makes fix of main wrapper and extract useful opened nodes parts
	protected static function extract( ?string $s = null ): iterable {

		if( $s ) {

			/*
			if( self::$mode === 'SVG' ) {

				$s = str_replace(

					['id="Layer_1" data-name="Layer 1"', 'x="0px"', 'y="0px"', 'style="enable-background:new 0 0 64 64;"'],

					['', '', '', '', ''], $s

				);

				$s = preg_replace('#\\s?<\w*>(\\s|&nbsp;)+</\w*>\\s?#mi', '', $s);
				$s = preg_replace('/<title>(.*?)<\/title>/si', '', $s);

			}
			*/ 

			// Restore html entites into special characters
			$s = str_ireplace(

				['&amp;', '&quot;', '&#039;', '&apos;', '&lt;', '&gt;', '>'],

				['&', '"', '\'', '\'', '<', '>', '> '],

			$s);

			/* RegExp PCRE fix */
			$s = trim(

				preg_replace(

						[ '/<!--(.*?)-->/m' ],

						[ '' ],

						str_replace(

							[ '(', ')', '[', ']' ],

							[ '#@--#', '#--@#', '#*--#', '#--*#' ],

							'@**xMarkup**@'. $s

						)

				)

			);

			if( self::$mode !== 'SVG' ) {

				// Add xhash to omittags
				preg_match_all('/<img[^>]+>/i', $s, $omittags, PREG_OFFSET_CAPTURE);

				foreach( $omittags[0] as $omt ) {

					$halfOmitTag = explode('>', $omt[0])[0];

					$s = str_replace( $halfOmitTag, rtrim( $halfOmitTag, '/' ) .' data-xhash="'. md5( $omt[1] ) .':'. md5( $omt[0] ) .'"' .' />' , $s);

				}

			}

			// Explode nodes range without omittags
			preg_match_all('#<(?!meta|img|br|hr|input|polyline|polygon|circle|rect|path|rect)([a-z]+)(?: .*)?(?<![\s*/\s*])>#iU', $s, $t, PREG_OFFSET_CAPTURE);

			$xdsp = null;

			// Detect or add closing final tag of main segment
			if( !(bool)$t[0][0][1] ) {

				$e = trim(

						explode(' ',

							trim('</'. str_replace('<', '', $t[0][0][0] )

						)

					)[0]

				);

			}
			else {

				$xdsp = true;

				$s = '<wrapper>'. $s;
				$e = ' </wrapper>';

			}

			// Check if main wrapper is closed
			$w = $e === substr($s, strlen( $s ) - strlen( $e )) ? true : null;

			// Close segment if ender not present
			$contents = !$w ? $s . $e : $s; 

			$movex = 0;
			$xmove = 0;
			$move  = 0;

			$xt = [];

			$h  = 0;

			$vm = 0;

			foreach( $t[ 0 ] as $x ) {

				// Make disposition because wrapper added
				$xoff = $xpos = (!$xdsp ? $x[1] : (int)$x[1] + 10) + $xmove;

				$hash = md5( $x[ 0 ] . $xpos );

				$xlen = strlen(

					str_replace(

						[ '<', '>' ],

						[ '', '' ],

						explode(' ', $x[ 0 ])[ 0 ]

					)

				);

				$xtag = '';

				for( $i = 0; $i < $xlen; $i++ ) {

					$xtag .= $x[ 0 ][ $i + 1 ];

				}

				// Moving
				$xidn = md5($xoff);

				$xnew = $xtag .'-'. $xidn .':'. $hash .'*'. explode('<'. $xtag, $x[ 0 ])[1];

				$move = strlen($xnew) - strlen($x[0]); 

				$lngx = strlen( $x[ 0 ] );

				$xnew = ltrim(rtrim($xnew, '>'), '<');

				if( $h > 0 ) {

					$xpos += ($move * $h);

					$xoff = $xpos + ($h === 1 ? 1 : 0);

				}

				// Add opener hash 
				$contents = substr_replace($contents, $xnew .'>', $xpos, $lngx);

				$xt[] = [ $xnew, $xoff, $hash ];

				$h++;

			}

			return [ $xt, preg_replace('/<?!\//mi', ' <', $contents) ];

		}

	}

	// Close ending tags
	protected static function markupFix( iterable $xt = [ null, null ], ?bool $truncated = null ): string {

		if( (bool)count( $xt[ 0 ] ) && strlen( $xt[1] ) ) {

				$xshift = 0;

				foreach( $xt[ 0 ] as $t ) {

					$halfFullTag = $t[ 0 ];

					$tagOpenInner = str_replace( // escaped tag info with attributes

						['<', '>'], 

						['', ''], 

						$halfFullTag

					);

					$tagPosition = $t[1] + $xshift;

					$tag = trim( explode(' ', trim( $tagOpenInner ) )[ 0 ] );

					$etag = explode(':', $tag);

					$ntag = explode('-', $etag[0]);

					$tagHash = rtrim( $etag[1], '*');

					if( substr_count( $xt[1], '<'. $ntag[0] /*$etag[0]*/ ) > substr_count( $xt[1], '</'. $ntag[0] .'>' ) ) {

						$eos = ' </'. $ntag[0] /*str_replace('-'. $ntag[1], '', $etag[0])*/ .'>';

						$segment = explode('<', substr($xt[1], $tagPosition));

						$xsgm = strlen($segment[ 0 ]) ? $segment[ 0 ] : explode('</', substr($xt[1], $tagPosition))[ 0 ];

						if( !preg_match('/<([\w]+)([^>]*?)([\s]*\>)/mi', $xsgm ) ) {

							foreach ($segment as $p) {

								if( preg_match('%'. $tagOpenInner .'>%mi', $p) ) {

									$xsgm = trim($p);

									break;

								}

							}

						}

						$xshift += strlen($eos);

						if( in_array( $ntag[0], ['ol', 'ul', 'dl', 'code'] ) && $truncated ) {

							$xt[1] = $xt[1] . $eos; 

						}
						else {

							$xt[1] = str_replace( [ $xsgm, ':'. $etag[1] ], [ $xsgm . $eos, ' data-xhash="'. $ntag[1] .':'. $tagHash .'"' ], $xt[1] );

						}

					}
					else {

						if( in_array( $ntag[0], ['ol', 'ul', 'dl', 'code'] ) && $truncated ) {

							$xt[1] = $xt[1] . '</'. $ntag[0] .'>'; 

						}
						else {

							$xt[1] = str_replace( [ ':'. $etag[1] ], [ ' data-xhash="'. $ntag[1] .':'. $tagHash .'"' ], $xt[1] );

						}

					}

					$xshift += 45;

				}

			}

			return trim(

					preg_replace(

						['/\s+/mi', '/\s*<\//mi', '#<\w>(\s|&nbsp;|</?\s?br\s?/?>)*</?\w>#', '#\-[[:xdigit:]]{32,}#mi'], 

						[' ', '</', '', ''], 

						str_replace(

							['#@--#', '#--@#', '#*--#', '#--*#', '@**xMarkup**@', '<wrapper>', '</wrapper>'], 

							['(', ')', '[', ']', '', '', ''], 

							$xt[ 1 ]

						)

					)

				);

	}

	// Crop snippets and returns correct markup
	protected static function crop(string $s, int $l): string {

		if( !(bool)$l ) {

			return $s;

		}

		$xtext = substr( $s, $l );

		return strpos( $xtext, '>' ) !== null ? substr( $s, 0, $l + strpos( $xtext, '>' ) + 1 ) : substr( $s, 0, $l );

	}

	// Makes markup secure
	protected static function markupSecure( ?string $s = null, bool $xh, string $mode = 'HTML', ?bool $lazy = null ): string {

		switch ( $mode ) {

			case 'HTML':

				self::$ctags  = self::$allowed_tags;

				self::$cattrs = self::$allowed_attr;

				break;

			case 'SVG':

				self::$ctags  = self::$allowed_svg_tags;

				self::$cattrs = self::$allowed_svg_attrs;

				break;

		}

		// Get segment 0 to test for fist words without tags
		$sText = preg_split('/(<[^>]*[^\/]>)/i', $s, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE)[0];

		$aConc = null; 

		if( !preg_match('/(<[^>]*[^\/]>)/i', $sText) ) {

			$aConc = true;

		}

		// Complite string to output
		$secured = '';

		$eotstack = [];

		if( $s ) {

			foreach( iterator_to_array( self::$parse::extract( $s ) ) as $nx) {

				foreach( $nx['explode'] as $ex ) {

					if( preg_match_all('/<([\w]+)([^>]*?)*>/mi', $ex, $m) ) {

						if( in_array(strtolower($m[ 1 ][ 0 ]), self::$ctags, true) ) {

							$slheot = preg_match( '/\/>/', $m[ 0 ][ 0 ] ) ? '' : '</'. strtolower($m[1][0]) .'>';

							$exp = iterator_to_array( self::$parse::extract( $ex . $slheot ) );

							foreach( $exp as $e ) {

								$section = '<'. $e['tagname'];

								$tagAttrs = ' ';

								if( isset($e['attrs']) ) {

									preg_match_all('/\\s.*=([\'\"]).*\\1/mUi', ' '. trim($e['attrs']), $attrs, PREG_SET_ORDER);

									foreach( $attrs as $a ) {

										$aparts = explode( '=', $a[ 0 ] );

										if( in_array(strtolower(trim($aparts[ 0 ])), self::$cattrs, true) || ($xh && strtolower(trim($aparts[ 0 ])) === 'data-xhash') ) {

											if( preg_match('/"(.*?)"|\'(.*?)\'/mi', $a[0], $ax) ) {

												$aname = trim( $aparts[ 0 ] );
												$acont = self::cleanXSS( trim($ax[ count($ax) - 1 ]) );

												if( $lazy ) {

													if( $e['tagname'] === 'img' ) {

														if( $aname === 'src' ) {

															$tagAttrs .= /*$aname .'="'. $acont ."*/' data-'. $aname .'="'. $acont .'" ';

															self::$lazyList[] = $acont;

														}
														else {

															$tagAttrs .= $aname .'="'. $acont .'" ';

														}

													}
													else {

														$tagAttrs .= $aname .'="'. $acont .'" ';

													}

												} 
												else {

													if( $e['tagname'] === 'img' ) {

														if( $aname === 'src' ) {

															//$tagAttrs .= $aname .'="'. $acont .'" ';

															self::$lazyList[] = $acont;


														}

													} 
													/*
													else {
													*/	

														$tagAttrs .= $aname .'="'. $acont .'" ';

													//}

												}

											}

										}

									}

								}

								if( ( $mode === 'SVG' && isset($e['attrs']) ) || $mode !== 'SVG' ) {

									$secured .= trim($section) . rtrim($tagAttrs, ' ') . ( in_array(

										strtolower( $e['tagname'] ), [

											'polyline',
											'polygon',
											'circle',
											'rect',
											'path',
											'rect',
											'meta',
											'img',
											'br',
											'hr',
											'input'

										], true) ? ' />' : '>' );

								} 

							}

						}

					}
					else {

						if( !self::isHTML($ex) ) {

							$secured .= self::cleanTagInnerXSS($ex);

						}
						else {

							if( preg_match_all('/<\/([\w]+)([^>]*?)*>/mi', $ex, $m) ) {

								if( in_array(strtolower($m[ 1 ][ 0 ]), self::$ctags, true) ) {

									if( $mode === 'SVG' && substr_count( $secured, (bool)str_replace(['</', '>'], ['<', ''], $ex) ) ) {

										$secured .= $ex;

									}
									else if( $mode !== 'SVG') {

										$secured .= $ex;

									}

								}

							}

						}

					}

				}

			}

			if( $mode === 'SVG' ) {

				$secured = preg_replace('!\s+!', ' ', str_replace(

					["\r", "\n", "\t"], 

					['', '', ''], 

					$secured

				));

			}

			return $aConc ? $sText . $secured : $secured;

		}

	}

	protected static function isHTML( ?string $string ): ?bool {

		return $string != strip_tags( $string ) ? true : null;

	}

	protected static function hexEntToLetter( string $ord ): string {

		$ord = $ord[1];

		if( preg_match('/^x([0-9a-f]+)$/i', $ord, $match) ) {

			$ord = hexdec($match[1]);

		} 
		else {

			$ord = intval($ord);

		}

		$no_bytes = 0;

		$byte = [];

		if ($ord < 128) {

			return chr($ord);

		}
		else if ($ord < 2048) {

			$no_bytes = 2;

		}
		else if ($ord < 65536) {

			$no_bytes = 3;

		}
		else if ($ord < 1114112) {

			$no_bytes = 4;

		}
		else {

			return ''; 

		}

		switch( $no_bytes ) {

			case 2: 

				$prefix = [ 31, 192 ];

				break;

			case 3: 

				$prefix = [ 15, 224 ];

				break;

			case 4: 

				$prefix = [ 7, 240 ];

		}

		for( $i = 0; $i < $no_bytes; $i++ ) {

			$byte[ $no_bytes - $i - 1 ] = (($ord & (63 * pow(2, 6 * $i))) / pow(2, 6 * $i)) & 63 | 128;

		}

		$byte[0] = ( $byte[0] & $prefix[0] ) | $prefix[1];

		$ret = '';

		for ($i = 0; $i < $no_bytes; $i++) {

			$ret .= chr($byte[$i]);

		}

		return $ret;

	}

	protected static function hexToSymbols( string $s ): string {

		return preg_replace_callback('/&#([0-9a-fx]+);?/mi', 'self::hexEntToLetter', preg_replace('/\\\\u?{?([a-f0-9]{4,}?)}?/mi', '&#x$1;', urldecode($s)));

	}

	protected static function escape( string $s, string $m = 'attr' ): string {

		preg_match_all('/data:\w+\/([a-zA-Z]*);base64,(?!_#_#_)([^)\'"]*)/mi', $s, $b64, PREG_OFFSET_CAPTURE);

		if( (bool)count( array_filter( $b64 ) ) ) {

			switch ($m) {

				case 'attr': 

					$xclean = self::cleanXSS( 

										urldecode( 

											base64_decode(

												$b64[2][0][0]

											)

										)

								);

					break;

				case 'tag':

					$xclean = self::cleanTagInnerXSS( 

										urldecode(

											base64_decode(

												$b64[2][0][0]

											)

										)

								);

					break;

			}

			return substr_replace(

				$s,

				'_#_#_'. base64_encode( $xclean ), 

				$b64[2][0][1],

				strlen( $b64[2][0][0] )

			);

		}
		else {

			return $s;

		}

	}

	protected static function cleanTagInnerXSS( string $s ): string {

		// base64 injection prevention
		$st = self::escape( $s, 'tag' );

		return preg_replace([

				// JSON unicode
				'/\\\\u?{?([a-f0-9]{4,}?)}?/mi',												 // [1] unicode JSON clean

				// Script tag encoding mutation issue 
				'/\¼\/?\w*\¾\w*/mi', 															 // [2] mutation KOI-8
				'/\+ADw-\/?\w*\+AD4-\w*/mi',													 // [3] mutation old encodings

				// Malware payloads
				'/:?e[\s]*x[\s]*p[\s]*r[\s]*e[\s]*s[\s]*s[\s]*i[\s]*o[\s]*n[\s]*(:|;|,)?\w*/mi', // [4]  (:expression) evalution
				'/l[\s]*i[\s]*v[\s]*e[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t[\s]*(:|;|,)?\w*/mi',	 // [5]  (livescript:) evalution
				'/j[\s]*a[\s]*v[\s]*a[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t[\s]*(:|;|,)?\w*/mi', 	 // [6]  (javascript:) evalution
				'/j[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t[\s]*(:|;|,)?\w*/mi', 					 // [7]  (jscript:) evalution
				'/b[\s]*e[\s]*h[\s]*a[\s]*v[\s]*i[\s]*o[\s]*r[\s]*(:|;|,)?\w*/mi',				 // [8]  (behavior:) evalution
				'/v[\s]*b[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t[\s]*(:|;|,)?\w*/mi',				 // [9]  (vsbscript:) evalution
				'/v[\s]*b[\s]*s[\s]*(:|;|,)?\w*/mi',											 // [10] (vbs:) evalution
				'/e[\s]*c[\s]*m[\s]*a[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t*(:|;|,)?\w*/mi',		 // [11] (ecmascript:) possible ES evalution
				'/b[\s]*i[\s]*n[\s]*d[\s]*i[\s]*n[\s]*g*(:|;|,)?\w*/mi',						 // [12] (-binding) payload 
				'/\+\/v(8|9|\+|\/)?/mi',														 // [13] (UTF-7 mutation)
				'/\/*?%00*?\//m',																 // [14] tag space evalution

				// base64 escaped restore
				'/_#_#_/mi',																	 // [15] base64 escaped marker cleanup

			],

			// Replacements steps total :: 15
			['&#x$1;', '', '', '', '', '', '', '', '', '', '', '', '', '', ''], 

			str_ireplace(

				['\u0', '&colon;', '&tab;', '&newline;'], 
				['\0', ':', '', ''], 

			// Unicode HEX prepare step
			self::hexToSymbols( $st ))

		);

	}

	protected static function cleanXSS( string $s ): string {

		// base64 injection prevention
		$st = self::escape( $s, 'attr' );

		return preg_replace([

				// JSON unicode
				'/\\\\u?{?([a-f0-9]{4,}?)}?/mi',												 // [1] unicode JSON clean

				// Data b64 safe
				'/\*\w*\*/mi',																	 // [2] unicode simple clean

				// Malware payloads
				'/:?e[\s]*x[\s]*p[\s]*r[\s]*e[\s]*s[\s]*s[\s]*i[\s]*o[\s]*n[\s]*(:|;|,)?\w*/mi', // [3]  (:expression) evalution
				'/l[\s]*i[\s]*v[\s]*e[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t[\s]*(:|;|,)?\w*/mi',	 // [4]  (livescript:) evalution
				'/j[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t[\s]*(:|;|,)?\w*/mi', 					 // [5]  (jscript:) evalution
				'/j[\s]*a[\s]*v[\s]*a[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t[\s]*(:|;|,)?\w*/mi', 	 // [6]  (javascript:) evalution
				'/b[\s]*e[\s]*h[\s]*a[\s]*v[\s]*i[\s]*o[\s]*r[\s]*(:|;|,)?\w*/mi',				 // [7]  (behavior:) evalution
				'/v[\s]*b[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t[\s]*(:|;|,)?\w*/mi',				 // [8]  (vsbscript:) evalution
				'/v[\s]*b[\s]*s[\s]*(:|;|,)?\w*/mi',											 // [9]  (vbs:) evalution
				'/e[\s]*c[\s]*m[\s]*a[\s]*s[\s]*c[\s]*r[\s]*i[\s]*p[\s]*t*(:|;|,)?\w*/mi',		 // [10] (ecmascript:) possible ES evalution
				'/b[\s]*i[\s]*n[\s]*d[\s]*i[\s]*n[\s]*g*(:|;|,)?\w*/mi',						 // [11] (-binding) payload
				'/\+\/v(8|9|\+|\/)?/mi',														 // [12] (UTF-7 mutation)

				// Some entities
				'/&{\w*}\w*/mi',																 // [13] html entites clenup
				'/&#\d+;?/m', 																	 // [14] html entites clenup

				// Broken string tag injections 
				'/x0{0,5}?3c;?/mi',																 // [15] < - symb
				'/x0{0,5}?60;?/mi',																 // [16] ` - symb
				'/&lt;?/mi',																	 // [17] < - symb
				'/</m', 																		 // [18] < - symb
				'/%3c/mi',																		 // [19] < - symb
				'/\/?>/mi',																		 // [20] /> - symbs

				// Script tag encoding mutation issue 
				'/\¼\/?\w*\¾\w*/mi', 															 // [21] mutation KOI-8
				'/\+ADw-\/?\w*\+AD4-\w*/mi',													 // [22] mutation old encodings

				// base64 escaped
				'/_#_#_/mi',																	 // [23] base64 escaped marker cleanup
				
			],

			// Replacements steps :: 23
			['&#x$1;', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''], 

			str_ireplace(

				['\u0', '&colon;', '&tab;', '&newline;'], 
				['\0', ':', '', ''], 

			// U-HEX prepare step
			self::hexToSymbols( $st ))

			);

	}

}

?>
