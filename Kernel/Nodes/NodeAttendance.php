<?php

 /*
  * RevolveR Attendance Node
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

if( in_array(ROLE, ['Admin', 'Writer'], true) )  {

	$infoByIP = new Reader('/private/IPDB/');

	// Statistics
	$tracker_stack = [];

	$curent_month_dates = [];

	// Date change
	if( isset(SV['g']['date']['value']) ) {

		$curent_stats_date = htmlspecialchars( SV['g']['date']['value'] );

		$uriSegment = explode('/', $curent_stats_date);

		$highlight	= !empty( $uriSegment[ 2 ] ) ? $calendar::leadingZeroFix( $uriSegment[ 2 ] ) : '01';

		$fzd = $calendar::leadingZeroFix( $uriSegment[ 1 ] );

		$regexp 	= $uriSegment[ 0 ] .'\/'. $fzd .'\/';

		$filter		= $uriSegment[ 0 ] .'/'. $fzd .'/';

	}
	else {

		$curent_stats_date = date('Y/m/d');

		$uriSegment = explode('/', $curent_stats_date);

		$highlight	= $uriSegment[ 2 ];
		
		$regexp		= $uriSegment[ 0 ] .'\/'. $uriSegment[ 1 ] .'\/';

		$filter 	= $uriSegment[ 0 ] .'/'. $uriSegment[ 1 ] .'/';

	}

	$hits = iterator_to_array(

		$model::get('statistics', [

			'criterion' => 'date::' . '^'. $regexp .'[0-9]{2}',
			'course'	=> 'forward',
			'sort' 		=> 'id',
			'expert'	=> true

		])

	)['model::statistics'];

	if( isset($hits[0]) ) {

		foreach( $hits as $s ) {

			$curent_month_render = explode( '/', $s['date'] );

			if( $filter . $highlight === $s['date'] ) { // today

				$ua_data = $uaInfo::getInfo( $s['user_agent'] );

				if( empty($ua_data['platform']) ) {

					$ua_data['platform'] = 'bot';

					if( empty($ua_data['browser']) ) {

						$ua_data = null;

					}

				}

				if( $ua_data ) {

					try {

						if( filter_var( $s['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE ) ) {

							$ipInfo = $infoByIP::get( $s['ip'] );

							$location = $ipInfo['country']['names']['en'] .' :: '. ( isset( $ipInfo['city']['names']['en'] ) ? $ipInfo['city']['names']['en'] : 'unknown' );

							$attr = $ipInfo['registered_country']['iso_code'];

						}
						else {

							$location = 'private';

							$attr = 'private';

						}

					}
					catch( AddressNotFoundException $e ) {

						$location = 'unknown';

						$attr = 'unknown';

					}

					if( !in_array( $s['route'], ['/secure/', '/comments-d/', '/contents-d/', '/category-d/', '/user-d/', '/favicon.ico', '/apple-touch-icon-precomposed.png', '/apple-touch-icon.png'], true ) ) {

						$tracker_stack[ $s['ip'] ][ $ua_data[ 'platform' ] .'/'. $ua_data[ 'browser' ] .'/'. $location ][] = [

							explode(';', 

								rtrim( 

									ltrim( $os[0][0],'(' ), ')'

								)

							)[0] .'/'. $browser[0] .'/'. $location  => [

								'time'		=> $s['time'],
								'identify'	=> $s['track'],
								'route'		=> $s['route'],
								'referer'	=> $s['referer'],
								'ip'		=> $s['ip'],

								'iso_code'	=> $attr

							]

						];

					}

				}

			} 

			// Complare only current Month statistics
			if( $calendar::leadingZeroFix( $uriSegment[ 1 ] ) === $curent_month_render[1] ) {

				$curent_month_dates[ (int)$curent_month_render[2] ] = true;

			}

		}

	}

	// Close and free
	$infoByIP::close();

	$complite_tracker_stack = [];

	foreach( $tracker_stack as $visitor ) {

		$total_time_h = 0;

		$total_time_m = 0;

		$total_time_s = 0;

		foreach( $visitor as $hit => $a ) {

			$start_time_h = 0;

			$start_time_m = 0;

			$start_time_s = 0;

			$counter = 0;

			$xkey = '';

			$qkey = '';

			$key = '';

			foreach( $a as $f => $v ) {

				$key  = key($v);

				$xkey = $v[ $key ];

				$qkey = key($visitor);

				$time = explode(':', $xkey[ 'time' ]);

				$s_time = explode(':', $a[0][ $key ][ 'time' ]);

				$start_time_h = $s_time[0];

				$start_time_m = $s_time[1];

				$start_time_s = $s_time[2];

				$p_time = explode(':', $a[ $counter - 1 ][ $key ]['time']);

				$prev_time_s = $p_time[2];

				$prev_time_m = $p_time[1];

				if( !(bool)$counter ) {

					$total_time_s = $time[2] - $start_time_s;

					$total_time_m = $time[1] - $start_time_m;

					$apple_icons = explode('png', $xkey['route']);

					if( explode('ico', $xkey['route'])[0] !== '/fav' && $apple_icons[0] !== '/apple-touch-icon-precomposed.' && $apple_icons[0] !== '/apple-touch-icon.') {

						$complite_tracker_stack[ $xkey[ 'ip' ] ][ $qkey ][] = [

							'stay_time' => [

								'h' => 0,
								'm' => 0,
								's' => 0

							],

							'total_time' => [

								'h' =>  $total_time_h,
								'm' =>  $total_time_m,
								's' =>  $total_time_s

							],

							'time' 		=> $xkey['time'],
							'identify' 	=> $xkey['identify'],
							'route'		=> $xkey['route'],
							'referer'	=> $xkey['referer'],
							'iso_code'	=> $xkey['iso_code'],
							'ip'		=> $xkey['ip']

						];

					}

				}
				else {

					$total_time_freeze_s = $calendar::timeDiffCalc( $time[ 2 ] - $prev_time_s );
					$total_time_freeze_m = $calendar::timeDiffCalc( $time[ 1 ] - $prev_time_m );
					
					$total_time_s += $time[ 2 ] - $prev_time_s;
					$total_time_m += $time[ 1 ] - $prev_time_m;

					if( (bool)($prev_time_s - $time[ 2 ]) ) {

						$total_time_m++;

					}

					if( (bool)($prev_time_m - $time[ 1 ]) ) {

						$total_time_h++;

					}

					$complite_tracker_stack[ $xkey[ 'ip' ] ][ $qkey ][ $counter - 1 ][ 'stay_time' ][ 'm' ] = $calendar::leadingZeroFix($total_time_freeze_m);
					$complite_tracker_stack[ $xkey[ 'ip' ] ][ $qkey ][ $counter - 1 ][ 'stay_time' ][ 's' ] = $calendar::leadingZeroFix($total_time_freeze_s);

					$route = $xkey['route'] === $complite_tracker_stack[ $xkey['ip']][ $qkey ][$counter - 1]['route'] ? '...' : $xkey['route'];

					$apple_icons = explode('png', $xkey['route']);

					if( explode('ico', $xkey['route'])[0] !== '/fav' && $apple_icons[0] !== '/apple-touch-icon-precomposed.' && $apple_icons[0] !== '/apple-touch-icon.' ) { 

						$add_to_stack = [

							'total_time' => [

								'h' => $total_time_h,
								'm' => $total_time_m,
								's' => $total_time_s

							],

							'time' 		=> $xkey['time'],
							'identify' 	=> $xkey['identify'],
							'referer'	=> $xkey['referer'],
							'iso_code'	=> $xkey['iso_code'],
							'ip'		=> $xkey['ip'],
							'route'		=> $route

						];

						$complite_tracker_stack[ $xkey[ 'ip' ] ][ $qkey ][] = $add_to_stack;

					}

				}

				$counter++;
			}

		}

	}

	$contents .= '<div class="revolver__form-wrapper">';
	$contents .= '<form class="revolver__new-fetch">';

	$contents .= '<fieldset>';
	$contents .= '<legend style="min-width:40%">'. TRANSLATIONS[ $ipl ]['Summary for the day'] .'</legend>';

	// Visitors
	$count_guest = 0;

	$count_bot   = 0;

	$count_users = 0;

	// Referers
	$count_referers = 0;

	$count_internal = 0;

	$count_search_engines = 0;

	$referers = [];

	$key = '';

	foreach( $complite_tracker_stack as $visitor => $v ) { 

		$key = $v[ key($v) ];

		$total_hits = count( $key );
		
		$ua_data = explode('/', key($v));

		if( $ua_data[0] === 'bot' ) {

			$count_bot++;

		}
		else {

			if( $key[ count( $key ) - 1]['identify'] !== 'guest' ) {

				$count_users++;

			}
			else {

				$count_guest++;

			}

			foreach( $key as $g ) {

				if( $g['referer'] !== 'straight' ) {

				$host = explode('.', parse_url($g['referer'])['path'] );

					if( !in_array( $host[0] != 'www' ? $host[0] : $host[1], ['yandex', 'rambler', 'bing', 'nigma', 'baidu', 'duckduckgo', 'google'] ) ) {

						$count_referers++;

						$referers[ $g['referer'] ] = $g['referer'];

					}
					else {

						$count_search_engines++;

					}

				}
				else {

					$count_internal++;

				}

			}

		}

	}

	$total_visits =  $count_users + $count_guest + $count_bot;
	$total_visits_counter = $count_referers + $count_search_engines + $count_internal;

	$contents .= '<div class="revolver__attendance-panel">';
	$contents .= '<output class="revolver__stats-total-counters">';
	$contents .= '<dfn class="revolver__stats-counter-hits">';

	$contents .= '<u>';
	$contents .= '<span class="revolver__interface-icon icon-mind-map">'. TRANSLATIONS[ $ipl ]['users'] .': <b>'. $count_users .'</b></span> / '; 
	$contents .= '<span class="revolver__interface-icon icon-enter">'. TRANSLATIONS[ $ipl ]['guests'] .': ';
	$contents .= '<b>'. $count_guest .'</b>';
	$contents .= '</span> / ';
	$contents .= '<span class="revolver__interface-icon icon-collect">'. TRANSLATIONS[ $ipl ]['scanners'] .': <b>'. $count_bot .'</b></span>';
	$contents .= '</u>';

	if( (bool)$total_visits ) {

		$contents .= '<i><b class="revolver__interface-icon icon-bullish">'. TRANSLATIONS[ $ipl ]['humans'] .': '. (int)( $count_users + $count_guest / $total_visits * 100 ) .'%</b></i>';

	} 
	else {

		$contents .= '<i>'. TRANSLATIONS[ $ipl ]['Insufficient statistics'] .'</i>';

	}

	$contents .= '</dfn>';
	$contents .= '</output>';

	$contents .= '<output class="revolver__stats-total-counters">';
	$contents .= '<dfn class="revolver__stats-counter-hits">';
	
	$contents .= '<u>';
	$contents .= '<span class="revolver__interface-icon icon-enter">'. TRANSLATIONS[ $ipl ]['internal'] .': <b>'. $count_internal .'</b></span> / ';
	$contents .= '<span class="revolver__interface-icon icon-open-in-browser">'. TRANSLATIONS[ $ipl ]['referer'] .': <b>'. $count_referers .'</b></span> / ';
	$contents .= '<span class="revolver__interface-icon icon-find-and-replace">'. TRANSLATIONS[ $ipl ]['search engines'] .': <b>'. $count_search_engines .'</b></span>';
	$contents .= '</u>';

	if( (bool)$total_visits_counter ) {

		$humansPercent = $count_search_engines / $total_visits_counter * 100;
		$externalPercent = $count_referers / $total_visits_counter * 100;

		$contents .= '<i><b class="revolver__interface-icon icon-org-unit">'. TRANSLATIONS[ $ipl ]['search engines'] .': '. round($humansPercent) .'%</b> / <b class="revolver__interface-icon icon-open-in-browser">'. TRANSLATIONS[ $ipl ]['referer'] .': '. round($externalPercent) .'%</b></i>';

	} 
	else {

		$contents .= '<i>'. TRANSLATIONS[ $ipl ]['Insufficient statistics'] .'</i>';

	}

	$contents .= '</dfn>';
	$contents .= '</output>';
	$contents .= '</div>';
	$contents .= '</fieldset>';

	if( (bool)count($referers) ) {

		$contents .= '<fieldset>';

		if( count($referers) >= 5 ) {

			$contents .= '<legend style="min-width:40%;" class="revolver__collapse-form-legend">'. TRANSLATIONS[ $ipl ]['External transitions'] .'</legend>';
			$contents .= '<output style="margin-bottom: 40px; overflow: hidden; width: 0; height: 0; line-height: 0; display: inline-block;" class="revolver__collapse-form-contents">';

		}
		else {

			$contents .= '<legend style="min-width:40%;">'. TRANSLATIONS[ $ipl ]['External transitions'] .'</legend>';

		}

		$contents .= '<dl class="revolver__stats-list"><dd>';
		$contents .= '<ol>';

		$list = 0;

		foreach( $referers as $r ) {

			if( strlen($r) > 3 ) {

				$contents .= '<li><a href="//'. $r .'" title="'. $r .'">'. $r .'</a></li>';

				$list++;

			}

		}

		$contents .= '</ol>';
		$contents .= '</dd></dl>';

		if( count($referers) >= 5 ) {

			$contents .= '</output>';

		} 

		$contents .= '</fieldset>';

	}

	$contents .= '<fieldset>';
	$contents .= '<legend style="min-width:40%;" class="revolver__collapse-form-legend">'. TRANSLATIONS[ $ipl ]['Internal transitions'] .'</legend>';
	$contents .= '<output style="overflow: hidden; width: 0; height: 0; line-height: 0; display: inline-block;" class="revolver__collapse-form-contents">';
	$contents .= '<dl class="revolver__stats-list collapse">';


	$xkey = '';
	$key  = '';

	foreach( $complite_tracker_stack as $visitor => $v ) {

		$key  = key($v);

		$xkey = $v[ $key ];

		$total_hits = count( $xkey );

		$total_time = $calendar::timeShit( $xkey[ $total_hits - 2 ][ 'total_time' ] );

		$ua_data = explode('/',  $key );

		if( $ua_data[0] !== 'bot' ) {

			$contents .= '<dt>';
			$contents .= '<dfn>';

			$contents .= '<div class="revolver__stats-group-identify">';

			$contents .= '<span class="revolver__stats-ua">'. $ua_data[0] .'</span>';
			$contents .= '<span class="revolver__stats-system">'. $ua_data[1] .'</span>&nbsp;';

			$contents .= TRANSLATIONS[ $ipl ]['from'] .'<span class="revolver__stats-country"><span class="state-attribution revolver__sa-iso-'. strtolower( str_replace(' ', '-', $xkey[0]['iso_code']) ) .'"></span>'. $ua_data[2] .'</span>';

			$contents .= '</div>';

			$contents .= '<div class="revolver__stats-group-time">';

			if( isset( $total_time['s'] ) ) {

				$contents .= '<span class="revolver__stats-time">  [ ';

				$contents .= $total_hits .' '. TRANSLATIONS[ $ipl ]['total hits'] .' '. TRANSLATIONS[ $ipl ]['at_2'] .' ';
				$contents .= $total_time['h'] .' '. TRANSLATIONS[ $ipl ]['hours'] .' '. $total_time['m'] .' ';
				$contents .= TRANSLATIONS[ $ipl ]['minutes'] .' '. $total_time['s'] .' '. TRANSLATIONS[ $ipl ]['seconds'];

				$contents .= ' ]  </span>';
			
			}
			
			$contents .= '</div>';

			$contents .= '<div class="revolver__stats-group-ip">';
			$contents .= '<span class="revolver__stats-ip-address revolver__interface-icon icon-collect">'. $visitor .'</span>';
			$contents .= '</div>';

			$contents .= '</dfn>';
			$contents .= '</dt>';
			
			$contents .= '<dd>';
			$contents .= '<ol>';

			$contents .= '<li class="revolver__stats-header"><span>#</span>';
			$contents .= ' <span><b class="revolver__interface-icon icon-expired">'. TRANSLATIONS[ $ipl ]['visit time'] .'</b></span>';
			$contents .= ' <span><b class="revolver__interface-icon icon-expired">'. TRANSLATIONS[ $ipl ]['residence time'] .'</b></span>';
			$contents .= ' <span><b class="revolver__interface-icon icon-open-in-browser">'. TRANSLATIONS[ $ipl ]['route'] .'</b></span>';
			$contents .= ' <span><b class="revolver__interface-icon icon-info-popup">'. TRANSLATIONS[ $ipl ]['identify'] .'</b></span>';
			$contents .= '</li>';

			$c = 1;

			foreach( $xkey as $g ) {

				$contents .= '<li class="revolver__stats-row">';
				$contents .= '<span class="revolver__stats-counter">'. $c++ .'</span>';
				$contents .= '<span class="revolver__stats-time">'. $g['time'] .'</span>';

				if( isset( $g['stay_time'] ) ) { 

					$contents .= '<span class="revolver__stats-residence-time">'. $calendar::leadingZeroFix( $g['stay_time']['m'] ) .':'. $calendar::leadingZeroFix( $g['stay_time']['s'] ) .'</span>';

				} 
				else {

					$contents .= '<span class="revolver__stats-residence-time"></span>';

				}

				$contents .= '<span class="revolver__stats-route">'. $g['route'] .'</span>';
				$contents .= '<span class="revolver__stats-identify">'. $g['identify'] .'</span>';
				$contents .= '</li>';

			}

			$contents .= '</ol></dd>';

		}

	}

	$contents .= '</dl>';
	
	// Max Mind attribution requred for usage MMDB with share-alike license
	$contents .= '<p style="font-size:.8vw; text-align: right; padding-right: 1.2vw;">[ This section includes GeoLite2 data created by MaxMind, available from <a href="https://www.maxmind.com" target="_blank">https://www.maxmind.com</a> ]</p>';
	
	$contents .= '</output>';
	$contents .= '</fieldset>';

	// get selected date
	$dateNow = explode('/', $curent_stats_date);

	$contents .= '<fieldset>';
	$contents .= '<legend style="min-width:40%;" class="revolver__collapse-form-legend">';

	$contents .= TRANSLATIONS[ $ipl ][ $calendar::monthName( date($dateNow[0] .'-'. $dateNow[1])) ] .' '. $dateNow[0] .', [ '. (int)$highlight .' ]';

	$contents .= '</legend>';

	$contents .= '<output style="overflow: hidden; width: 0; height: 0; line-height: 0; display: inline-block;" class="revolver__collapse-form-contents">';

	/* Extra SQL with Years and Months ranges */
	unset( $STRUCT_STATISTICS['criterion_field'], $STRUCT_STATISTICS['criterion_format'], $STRUCT_STATISTICS['criterion_regexp'], $STRUCT_STATISTICS['criterion_value'] );

	/* Get Years-Months Ranges */
	$STRUCT_STATISTICS['extra_select_sql'] = 'SELECT DISTINCT DATE_FORMAT(`field_date`, \'[%Y%m]\') YearMonth, DATE_FORMAT(`field_date`, \'[%Y]\') Year, DATE_FORMAT(`field_date`, \'[%m]\') Month FROM `revolver__statistics`;';

	$dbx::query('p', 'revolver__statistics', $STRUCT_STATISTICS); // be carefull becuase this query unescaped

	$year_month_r = [];

	$years_r = [];

	$month_r = [];

	// Get ranges
	foreach( $dbx::$result['result'] as $k => $v ) {

		$years = str_replace(['\\', ']', '['], ['', '', ''], $v['Year']);

		$month = str_replace('\\', '', $v['Month']);

		$ym = explode('\\', $v['YearMonth']);

		$years_r[ $years ] = $years;

		$month_r[ $month ] = $month;

		$year_month_r[]    = [ $ym[1], rtrim($ym[2], ']') ];

	}

	$contents .= '<div class="revolver__multiple-selects-wrapper" style="text-align:center;">';
	$contents .= '<label style="width:46% !important" class="styled-select">';
	$contents .= 'Choose year:';

	$contents .= '<select name="revolver__attendance-year" data-callback="switchAttendanceDate">';

	// Statistics Years list
	foreach( $years_r as $y ) {

		$lastMonthInYear = '';

		foreach( $year_month_r as $ymr ) {

			if( $y === $ymr[0] ) {

				$lastMonthInYear = $ymr[1];

			}

		}

		$contents .= '<option value="'. $y .'/'. $lastMonthInYear .'/'. $calendar::dayCountInMonth( [0, 0, 0, $lastMonthInYear, 10] ) .'" '. ( $uriSegment[0] === $y ? 'selected="selected"' : '' ) .'>'. $y .'</option>';

	}

	$contents .= '</select>';
	$contents .= '</label>';

	$contents .= '<label style="width:46% !important" class="styled-select">';
	$contents .= 'Choose month:';

	// Statistics Month list
	$contents .= '<select name="revolver__attendance-month" data-callback="switchAttendanceDate">';

	foreach( $year_month_r as $ym ) {

		if( $uriSegment[0] === $ym[0] ) {

			if( $uriSegment[1] === $ym[1] ) {

				$attr = ' selected="selected"';

			} 
			else {

				$attr = '';

			}

			$contents .= '<option value="'.  $ym[ 0 ] .'/'. $ym[ 1 ] .'/'. $calendar::dayCountInMonth( [0, 0, 0, $ym[ 1 ], 10] ) .'" '. $attr .'>'. $calendar::monthName( [0, 0, 0, $ym[ 1 ], 10] ) .'</option>';

		}

	}

	$contents .= '</select>';
	$contents .= '</label>';
	$contents .= '</div>';

	$yearMonth = explode('/', $curent_stats_date);

	$contents .= $calendar::make( $yearMonth[1], $yearMonth[0], $curent_month_dates, $highlight, $ipl );

	$contents .= '</fieldset>';
	$contents .= '</form>';
	$contents .= '</div>';

}

$node_data[] = [

	'title'		=> TRANSLATIONS[ $ipl ]['Attendance'],
	'route'		=> '/attendance/',
	'id'		=> 'attendance',
	'contents'	=> $contents,
	'teaser'	=> false,
	'footer'	=> false,
	'time'		=> false,
	'published' => 1

];

?>
