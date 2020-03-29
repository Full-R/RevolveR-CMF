
 /* 
  * RevolveR Front-end :: main interface
  *
  * v.1.9.0
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

if( !self.run ) {

	const R = new R_CMF_i[ 0 ]( 'R' );

	R.axys_test = null;

	self.run = true;

}

self.searchAction = null;

R.logging = ( x, e = 'html' ) => {

	let lines = x.split('</samp>')[0].split('<samp>')[1].replace('<!--', '').replace('-->', '').trim().split("\n");

	let s = [];

	let l = 0;

	for ( let i of lines ) {

		if( l > 1 && l < lines.length - 2 ) {

			if( i.length ) {

				s.push( i );

			}

		}

		l++;

	}

	s.push( new Date().toString() );

	s.push( 'Route: '+ self.route );	

	let tpause = 1000;

	let tshift = 0;

	for ( let line of s ) {

		void setTimeout(() => {

			console.log( line );

		}, tpause + ( tshift * 500 ) );	

		tshift++;

	}

};

R.switchAttendanceDate = ( x ) => {

	for( let d in x ) {

		if( x[ d ][ 0 ] === 'choosen' ) {

			R.loadURI(

				document.location.origin + document.location.pathname + '?date='+ x[ d ][ 1 ], 'attendance'

			);

		}

	}

};

R.useCaptcha = (p) => {

	self.overprint = atob( p.split('*')[2] );
	self.oprintvar = atob( p.split('*')[0] );

	self.flag = null;

	let pixels = R.sel('#drawpane div');

	if( pixels ) {

		for( let i of R.sel('#drawpane div') ) {

			i.addEventListener('click', function(e) {

				e.preventDefault();

				let choosen = this.dataset.selected;

				if( e.isTrusted ) {

					self.flag = true;

					if( choosen === 'null' || choosen === 'false' ) {

						this.style = 'background: #7e333d;';
						this.dataset.selected = 'true';
						this.className = 'active';

					}
					else {

						this.style = 'background: #a2a2a2; transform: scale(1);';
						this.dataset.selected = 'null';
						this.className = 'inactive';

					}

				}

			});

		}

	}

	const finger = R.sel('#overprint');

	if( finger ) {

		function oPrint( m ) {

			let c = finger[0].getContext('2d');

			function walk( e, i ) {

				let s = e.split(':');

				let style = '#' + ( s[ 0 ] == 1 ? '888888' : 'D99FAF' );

				c.fillStyle = style;

				c.fillRect( s[ 1 ], s[ 2 ], 24, 24 );

				c.stroke();

			}

			m.forEach( walk );

		}

		let d = atob( p.split('*')[ 1 ] ).split('|').sort();

		let xy = [];

		for( let i of d ) {

			xy.push( i.split('-')[ 1 ] );

		}

		oPrint( xy );

	}

};

R.cleanNotifications = (t) => {

	let timeShift = 0;

	for( let e of t ) {

		void setTimeout(() => {

			R.styleApply(e.querySelectorAll('.revolver__statuses-heading') , ['display:none'], () => {

				R.animate(e.children, ['height:0px:800:elastic']);

				R.animate([e], ['height:0px:1500:wobble', 'color:rgba(255,255,255,.1):700:elastic', 'opacity:0:1200:harmony'], () => {

					R.rem([e]);

				});

			});

		}, 10000 / ++timeShift);

	}

};

// Lazy load future
R.lazyLoad = () => {

	let list = R.sel('img[data-src]');

	R.lazyList = [];

	if( list ) {

		for( let p of list ) {

			R.lazyList.push([ p, 0 ]);

		};

	}

	if( R.lazyList.length > 0 ) {

		let lazy = () => {

			for( let l of R.lazyList ) {

				if( l[1] === 0 ) {

					if( l[0].offsetTop < ( self.innerHeight + self.pageYOffset + 450 ) ) {

						l[0].src = l[0].dataset.src;

						l[1] = 1;

						l[0].style = 'opacity: 0; transform: scale(.1, .1, .1);';

						setTimeout(() => {

							R.animate([ l[0] ], ['opacity:1:1400:wobble', 'transform:scale(1, 1, 1):1650:elastic'] );

							l[0].className = 'lazy-preload';

						}, 1000);

						console.log( 'Lazy load :: '+ l[0].src );

					}

				}

			}

		};

		R.event(self, 'scroll', lazy);

		R.event(self, 'resize', lazy);

	}

};

// Make interface
R.fetchRoute = ( intro ) => {

	// Privacy policy
	R.fetch('/secure/?policy=get', 'get', 'json', null, function() {

		const key = atob( this.privacy ).split('::');

		if( key[0] !== 'accepted' ) {

			const nPolicy = {

				html: '<div class="revolver__statuses-heading">... Privacy policy notification <i>+</i></div><div class="privacy-policy-notification"><p>This domain use cookies only to improve privacy and make possible correct work our services.</p><p>You can <a href="/privacy/">read domain cookie policy</a> and <a href="'+ document.location.pathname +'?notification=accept-privacy-policy">accept</a> it.</p></div>',

				attr: {

					class : 'revolver__status-notifications revolver__notice'

				}

			};

			R.new('div', '.revolver__main-contents', 'before', nPolicy);

			let forms = R.sel('form');

			let c = 0;

			for( let f of forms ) {

				if( c >= 1 ) {

					R.event([f.parentElement], 'click', (e) => {

						if( e.isTrusted ) {

							R.new('div', '.revolver__form-wrapper', 'before', nPolicy);

						}

					});

					for(let i of f.querySelectorAll('input, textarea')) {

						i.disabled = 'disabled';

					}

				}

				c++;

			}

			R.styleApply('.revolver__captcha-wrapper', ['display:none']);

			R.nullRun = null;

			R.setAllow( null );

		}
		else {

			if( intro ) {

				let cform = R.sel('#comment-add-form');

				let route = cform ? cform[0].action.replace( document.location.origin, '' ) : document.location.pathname;

				if( route !== '/' && route !== '/logout/' ) {

					R.fetch('/secure/?route='+ route, 'get', 'json', null, function() {

						R.useCaptcha( this.key );

					});

				}

			}

			R.setAllow( key[1] );

		}

		// Lazy load
		R.lazyLoad();

		// Sense objects
		clearInterval( R.axys_test );

		R.SenseMove = null;

		R.lastActive = null;

		R.SenseListActivity = [];

		if( !R.isM ) {

			R.event('article', 'mousemove', (e) => {

				if( !R.isU(RR.curxy) ) {

					var axys = [

						RR.curxy[0], RR.curxy[1] 

					];

					R.axys_test = void setInterval(() => {

						if( RR.curxy[0] === axys[0] && RR.curxy[1] === axys[1] ) {

							clearInterval( R.axys_test );

							R.SenseMove = null;

						}
						else {

							R.SenseMove = true;

						}

						axys = [

							RR.curxy[0], RR.curxy[1]

						];

					}, 500);

				}

				if( e.isTrusted ) {

					if( RR._privacyPolicyAccepted && RR.senseAllowed ) {

						let includes = e.target.querySelectorAll('*');

						if( includes.length > 0 ) {

							R.event(includes, 'mouseenter', function(evt) {

								var sense_hash = R.treeHacks(

									R.attr( this, 'data-xhash' )

								);

								if( !R.isO(sense_hash) ) {

									if( ( R.lastActive !== sense_hash ) && R.lastActive ) {

										for( let i of R.SenseListActivity ) {

											if( i[ 0 ] ) {

												R.animate([ i[ 0 ] ], ['transform: scale(1):400:elastic', 'opacity:1:500:wobble']);

											}

											if( i[1] === sense_hash ) {

												i[0].removeEventListener('mouseenter', null, false);

											}

										}

										R.SenseListActivity = [];

										console.log( 'Sense inactive for: '+ sense_hash);

									}

									let allowSense = true;

									for( let i of R.SenseListActivity ) {

										if( i[1] === sense_hash ) {

											allowSense = null;

										}

									}

									if( allowSense ) {

										if( R.SenseMove ) {

											R.SenseListActivity.push( [this, sense_hash] ); 

											R.styleApply([this], ['opacity:.1']);

											R.animate([this], ['transform: scale(.98):400:elastic', 'opacity:.9:500:wobble']);

											console.log( 'Sense active for: '+ sense_hash);

											R.lastActive = sense_hash;

										}

									}

								}

							});

						}

					}

				}

			});

		} 
		else {
			
			var xDown, yDown, stouch = null;

			R.event('article', 'touchstart', e => {

				stouch = e.target;

				xDown = stouch.clientX;
				yDown = stouch.clientY;

				R.styleApply([stouch], ['opacity:.1']);

				R.animate([stouch], ['transform: scale(.98):400:elastic', 'opacity:.9:500:wobble']);

				let sense_hash = R.attr( [stouch], 'data-xhash' );

				if( sense_hash ) {

					console.log( 'Sense active for: '+ sense_hash);

				}

			});

			R.event('article', 'touchend', (e) => {

				if( !xDown || !yDown ) {

					return;

				}

				stouch = e.target;

				const { clientX: xUp, clientY: yUp } = stouch;

				const xDiff = xDown - xUp;
				const yDiff = yDown - yUp;

				const xDiffAbs = Math.abs( xDown - xUp );
				const yDiffAbs = Math.abs( yDown - yUp );

				// at least <offset> are a swipe
				if( Math.max( xDiffAbs, yDiffAbs ) < 100 ) {

					return;

				}

				R.animate([ stouch ], ['transform: scale(1):400:elastic', 'opacity:1:500:wobble']);

				let sense_hash = R.attr( [stouch], 'data-xhash' );

				if( sense_hash ) {

					console.log( 'Sense inactive for: '+ sense_hash);

				}


				/*
				if( xDiffAbs > yDiffAbs ) {

					if( xDiff > 0 ) {

						//console.log('left');

					}
					else {

						//console.log('right');

					}

				}
				else {

					if( yDiff > 0 ) {

						//console.log('up');

					}
					else {

						//console.log('down');

					}

				}
				*/

			});

		}

		// Stop preview
		clearInterval( R.preview );

		// Hide status messages
		clearInterval( R.notificationsInterval );

		R.notificationsInterval = null;

		R.notificationsInterval = setInterval(() => {

			let notifications = R.sel('.revolver__status-notifications');

			if( notifications ) {

				R.cleanNotifications( notifications );

			}

		}, 20000);

		R.event('.revolver__status-notifications .revolver__statuses-heading i', 'click', function(e) {

			e.preventDefault();

			if( e.isTrusted ) {

				R.styleApply([this.parentElement], ['display:none'], () => {

					R.animate(this.parentElement.parentElement.children, ['height:0px:500:elastic']);

					R.animate([this.parentElement.parentElement], ['height:0px:1500:wobble', 'color:rgba(255,255,255,.1):700:elastic', 'opacity:0:1000:harmony']);

					void setTimeout(() => {

						R.rem([this.parentElement.parentElement]);

					}, 1300);

				});

			}

		});

		for( let i of R.sel('a') ) {

			if( i.target === '_blank') {

				R.addClass( [ i ], ['external'] );

			}

		}

		R.event('a', 'click', function(e) {

			e.preventDefault();

			if( e.isTrusted ) {

				if( R.hasClass( [ this ], 'external' ) ) {

					self.open( e.target.href );

					return;

				}

				if( !this.href.includes( 'webp', 'svg' ,'png', 'jpg', 'jpeg', 'gif' ) ) {

					R.loadURI(

						this.href, this.innerText

					);

				}

			}

		});

	});



	// Intro
	if( intro ) {

		R.attr('.revolver__header h1 a, .revolver__main-menu ul li a, .revolver__main-contents', {

			style: null

		});

		R.styleApply('.revolver__header h1 a', ['color: rgba(220, 220, 220, .8)', 'display:inline-block', 'opacity:.1'], () => {

			R.styleApply('.revolver__main-menu ul li a', ['display:inline-block', 'opacity:.1']);

			R.animate('.revolver__header h1 a', ['transform: scale(.5, .5, .5) rotate(360deg, 360deg, 360deg):1500:bouncePast']);
			R.animate('.revolver__header h1 a', ['opacity:.9:1500:bouncePast', 'transform: scale(1, 1, 1) rotate(0deg,0deg,0deg):2000:elastic', 'color:rgba(111, 111, 111, 0.9):6000:wobble']);

			R.animate('.revolver__main-menu ul li a ', ['opacity:1:1000:bouncePast']);

			R.styleApply('.revolver__main-contents', ['opacity:0.5', 'display:inline-block']);

			R.animate('.revolver__main-contents', [

				'opacity:1:1000:elastic',
				'transform:scale(.5,.5,.5):500:bouncePast', 
				'transform:scale(1,1,1):1550:elastic'

			]);

		});

	}

	// Highlight menu
	const menu = R.sel('.revolver__main-menu li');

	if( menu ) {

		for( let e of menu ) {

			if( document.location.href === e.children[0].href ) {

				void setTimeout(() => {

					R.addClass([e], 'route-active');

				}, 2000);

			}

		}

	}

	// Forms styles
	R.formBeautifier();

	// Enable editor
	if( R.sel('textarea') ) {

		R.markupEditor();

	}

	// Tabs
	if( R.sel('#tabs') ) {

		R.tabs('#tabs li.revolver__tabs-tab', '#tabs div');

	}

	// Collapsible elements
	if( R.sel('.collapse dd') ) {

		for ( let i of R.sel('.collapse dd') ) {

			R.toggle( [ i ] );

		}

	}

	R.expand('.collapse dt, .revolver__collapse-form-legend');

	R.event('input[type="submit"]', 'click', (e) => {

		if( e.isTrusted ) {

			if( self.flag ) {

				let m = [];
				let c = 0;

				let draw = R.sel('#drawpane div');

				for( let a of draw ) {

					m[ c ] = ( a.dataset.selected === 'true' ? 1 : 0 ) +':'+ a.dataset.xy;

					c++;

				}

				function encoder( s ) {

					let e = '';

					for ( let j = 0; j < s.length; j++ ) {

						e += String.fromCharCode( s.charCodeAt( j ) ^ 51 );

					}

					return e;

				}

				let s = '';
				let e = encoder( '{\"value\":'+ '"'+ self.oprintvar +'*'+ m.join('|') +'"'+ '}' );

				for ( let i = 0; i < e.length; i++ ) {

					s += e.charCodeAt( i );

					if( i < e.length - 1 ) {

						s += '|';

					}

				}

				R.attr('.revolver__captcha-wrapper input[type="hidden"]', {

					'value': btoa( s ) +'*'+ btoa( self.overprint ) +'*'+ btoa( document.location.pathname )

				});

			}

		}

	});

	// Fetch Submit
	R.fetchSubmit('form.revolver__new-fetch', 'text', function() {

		// Prevent search box fetching
		if( !self.searchAction ) {

			R.sel('#RevolverRoot')[0].innerHTML = '';

			for( let i of R.convertSTRToHTML(this) ) {

				if( i.tagName === 'TITLE' ) {

					var title = i.innerHTML;

				}

				if ( i.id === 'RevolverRoot' ) {

					var contents = i.innerHTML;

				}

				if( i.tagName === 'META') {

					if( i.name === 'host') {

						eval( 'window.route="'+ i.content +'";' );

					}

				}

				if( i.className === 'revolver__privacy-key' ) {

					R.sel('.revolver__privacy-key')[0].dataset.xprivacy = i.dataset.xprivacy;

				}

			}

			R.insert( R.sel('#RevolverRoot'), contents );

			R.location(title, self.route);

			R.scroll();

			R.logging(this, 'body');

			clearInterval( R.notificationsInterval );

			R.notificationsInterval = null;

			R.fetchRoute( true );

		}

	});

	// Search
	R.event('.revolver__search-box form', 'submit', function(e) {

		e.preventDefault();

		if( e.isTrusted ) {

			// Prevent search box fetching
			self.searchAction = true; 

			R.fetch('/search/?query='+ this.querySelectorAll('input[type="search"]')[0].value, 'get', 'html', true, function() {

				if( this.length ) {

					R.insert('.revolver__main-contents', '<article class="revolver__article published"><div class="revolver__article-contents">'+ this +'<div></article>');

					R.logging(this, 'div');

					void setTimeout(() => {

						self.searchAction = null;

						clearInterval( R.notificationsInterval );

						R.notificationsInterval = null;

						R.fetchRoute( null );

					}, 500);

				}

			});

		}

	});

	// Terminal fetch
	R.fetchSubmit('form.revolver__terminal-fetch', 'json', function() {

		// Prevent search box fetching
		if( !self.searchAction ) {

			RR.new('li', '.revolver__terminal-session-store ul', 'after', {

				html: '<span class="revolver__collapse-form-legend">'+ this.command +'</span><pre class="revolver__collapse-form-contents" style="overflow: hidden; width: 0; height: 0; line-height: 0; display: inline-block;">'+ this.output +'</pre>'

			});

			R.fetch('/secure/?route=/terminal/', 'get', 'json', null, function() {

				R.useCaptcha( this.key );

			});

			R.fetchRoute( null );

		}


	});

	R.loadURI = ( url, title ) => {

		R.fetch(url, 'get', 'html', true, function() {

			R.sel('#RevolverRoot')[0].innerHTML = '';

			for( let i of R.convertSTRToHTML( this ) ) {

				if( i.tagName === 'TITLE' ) {

					var title = i.innerHTML;

				}

				if ( i.id === 'RevolverRoot' ) {

					var contents = i.innerHTML;

				}

				if( i.tagName === 'META') {

					if( i.name === 'host') {

						eval( 'window.route="'+ i.content +'";' );

					}

				}

				if( i.className === 'revolver__privacy-key' ) {

					R.sel('.revolver__privacy-key')[0].dataset.xprivacy = i.dataset.xprivacy;

				}

			}

			R.insert( R.sel('#RevolverRoot'), contents );

			R.location( title, self.route );

			let hash = url.split('#');

			if( !R.isU( hash[ 1 ] ) ) {

				setTimeout(

					R.scroll('#'+ hash[ 1 ] ), 2500

				);

			}
			else {

				R.scroll();

			}

			R.logging(this);

			clearInterval( R.notificationsInterval );

			R.notificationsInterval = null;

			R.fetchRoute( true );

		});

	};

	// History states
	self.onpopstate = void function(e) {

		R.loadURI(

			e.state.url, e.state.title

		);

	}

};

// Perform parametrized fetch query
if( typeof R === 'object' ) {

	R.fetchRoute( true );

}
else {

	console.log(

		decodeURIComponent(

			atob( 'JUYwJTlGJTlCJTkxJTIwSW5zdGFuY2UlMjBub3QlMjBhbGxvd2VkJTIwLi4u' )

		)

	);

}
