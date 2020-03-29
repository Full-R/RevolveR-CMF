
<?php

 /* 
  * 
  * RevolveR Node Terminal
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

/* Terminal */
if( ROLE === 'Admin' ) {


}


$title = TRANSLATIONS[ $ipl ]['Terminal'];

// User Profile Form Structure
$form_parameters = [

	// main parameters
	'id'		=> 'terminal-form',
	'class'		=> 'revolver__terminal revolver__terminal-fetch',
	'action'	=> '/terminal-s/',
	'method'	=> 'post',
	'encrypt'	=> true,
	'captcha'	=> true,
	'submit'	=> 'Send',

	'fieldsets' => [

		// fieldset contents parameters
		'fieldset_1' => [

			'title' => 'Command shell',

			// wrap fields into label
			'labels' => [

				'label_1' => [

					'title'  => 'Shell session',
					'access' => 'preferences',
					'auth'	 => 1,

					'fields' => [

						0 => [

							'type' 			=> 'input:text',
							'name' 			=> 'revolver_command_shell',
							'placeholder'	=> 'Type command',
							'value'			=> $command ? $command : '',
							'required'		=> true,

						],

					],

				],

			],

		],

	]

];

$sense = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

//$sense = 'Start session with UNIX shell typing commands';
//$sense = 'Some Moooooooooooooooo for my friends :)';

$ls = 33;

foreach( explode("\n", $sense) as $str ) {

	$c = 0;

	$xl = str_split($str, 60);

	foreach( $xl as $s ) {

		if( !(bool)$c ) {

			$ls = strlen($s);

		}

		$sense_lines .= '  '. $s;

		if( $c !== count($xl) - 1 ) {

			$sense_lines .= '-' . "\n" . ' ';

		} 
		else {

			$sense_lines = ' '. $sense_lines ."\n";

		}

		$c++;

	}	

}

$xline  = str_repeat( '_', $ls + 3 );
$space  = str_repeat( ' ', $ls + 3 );
$xspace = str_repeat( ' ', $ls - 10 );

$contents  = '<output class="revolver__terminal-session-store" style="color:#333">';
$contents .= '<ul>';

$contents .= '<li><pre class="cow">
  '. $xline .'
 /'. $space .'\ 

'.

$sense_lines

.'
 \\'. $xline .'/

'. $xspace .'		^__^
'. $xspace .'		(oo)\________
'. $xspace .'		(__)\        )\?
'. $xspace .'		     ||----ω||
'. $xspace .'		     ||     ||
'. $xspace .'		     ˆˆ     ˆˆ

</pre></li>';

$contents .= '</ul>'; 
$contents .= '</output>';

$contents .= $form::build( $form_parameters );

$node_data[0] = [

	'title'		=> $title,
	'id'		=> 'terminal',
	'route'		=> '/terminal/',
	'contents'	=> $contents,
	'teaser'	=> false,
	'footer'	=> false,
	'time'		=> false,
	'published' => 1

];

?>
