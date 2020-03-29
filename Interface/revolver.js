
 /*
  *
  * RevolveR CMF interface :: ECMA Script 7
  *
  * v.1.9.0
  *
  * RevolveR ECMA Script is a fast, simple and
  *
  * powerfull solution without any third party.
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

(() => {

	self.RR = {

		// Protected futures
		_privacyPolicyAccepted: null,

		_protection: null,

		_privacyKeys: [],

		// Set first index for locations api work correct
		title: document.title,

		// Store document context as `this.that` to make inner links relative for main window via self proxy
		that: self.document,

		// Allow Sense
		setAllow: function( k ) {

			((_k, _this, _f) => {

				_f( _k, _this );

			})(k, this, ( _k, _this ) => {

				RR._manageKeys( _k, _this );

			});

		},

		_manageKeys: function( k, _that ) {

			if( !k ) {

				RR._privacyKeys = [];

			}
			else {

				RR._privacyKeys.push( k );

			}

			if( !RR._protection && 

				JSON.stringify( 

					Object.keys( _that )

				) === JSON.stringify(

					Object.keys( RR )

				) 

			) {

				const keyChain_ = void setInterval(() => {

					const xPrivacy = RR.sel('.revolver__privacy-key')[0].dataset.xprivacy;

					if( !_that.hasOwnProperty('launch') ) {

						RR._privacyPolicyAccepted = null;

						RR.senseAllowed = null;

					}

					if( RR.isO(RR._privacyKeys) ) {

						if( RR._privacyKeys.length > 0 ) {

							if( RR._privacyKeys[ RR._privacyKeys.length - 1 ] == JSON.parse(

										atob(

											xPrivacy

										)

									).xkey.split('::')[ 1 ] 

								) {

									RR._privacyPolicyAccepted = true;

									RR.senseAllowed = null;

								}
								else {

									RR._privacyPolicyAccepted = null;

									RR.senseAllowed = null;

									RR.nullRun = null; 

								}

						}
						else {

							RR._privacyPolicyAccepted = null;

							RR.senseAllowed = null;

						}

					}
					else {

						RR._privacyPolicyAccepted = null;

						RR.senseAllowed = null;

					}

					RR._protection = true;


				}, 500);

			}

		},

		// Eneble some browser futures 
		get browser() {

			// Interface application version
			RR.appVer = '1.9.0';

			// Is mobile support available
			RR.isM = /(Privacy|Android|BackBerry|phone|iPad|iPod|IEMobile|Nokia|Mobile)/.test(navigator.userAgent);

			// Store screen width and size
			// available in RR.sizes[0, 1]
			RR.sizes = [self.screen.width, self.screen.height];

			// Get available CSS styles list from body element
			RR.styles = RR.sel('body')[0].style;

			// Make absolutely positioned child elements of body element  is relative to parent 
			RR.styles.position = 'relative';

			// Events stack
			RR.events = [];

			RR.AxisEvent = null;

			// Refresh window size on every resize
			// values stored in RR.currentSizes[ 0, 1 ];
			void setInterval(() => {

				RR.currentSizes = [ RR.that.documentElement.clientWidth, RR.that.documentElement.clientHeight ];

				if( !RR.AxisEvent ) {

					RR.that.body.addEventListener('touchmove', (e) => {

						if( e.isTrusted ) {

							RR.curOffset = [ self.scroollX, self.scrollY ];	
							RR.sizes     = [ self.screen.width, self.screen.height ];

							RR.CXY = [ self.scroollX, self.scrollY ];

						}

					}, null);

					RR.events.push([RR.that.body, 'touchmove', () => {}]);

					RR.that.body.onmousemove = (e) => {

						if( e.isTrusted ) {

							RR.curxy = [ e.clientX, e.clientY, e ];
							RR.curOffset =	[ self.scrollX, self.scrollY ];

							RR.CXY = [ e.clientX, e.clientY, e ];

						}

					};

					RR.events.push([RR.that.body, 'mousemove', () => {}]);

				}

				RR.AxisEvent = true; 

			}, 500);

		},

		// Launch RevoveR Inteface and allow Senses
		set launch( xhash = true ) {

			RR.nullRun = null;

			// Zeem prevention
			RR.event('html', 'keydown:lock', e => {

				if( e.ctrlKey ) {

					switch( e.which - 0 ) {

						case 61:
						case 107: 
						case 173:
						case 109:
						case 187:
						case 189:

							console.log('... keyboard zoom prevented when View Port Units interface active');

							e.preventDefault();

							break;

					}

				}

			});

			RR.event('html', 'wheel:lock', e => {

				if( e.ctrlKey ) {

					console.log('... mousewheel zoom prevented when View Port Units interface active');

					e.preventDefault();

				}

			});

			const xfullBlock = 'Zm9yY2UtZGlzcG9zYWw6IHVudGlsIGtub2NrZWQgb3V0KiBzZW5zZTogMTAwJSogdm9sdW1lOiAxMDAlKiBjYXB0dXJlOiBudWxsKiB2aXNpb246IG51bGwqIGNvbm5lY3Rpdml0eTogbnVsbCogZW1wYXRoeTogbnVsbA==';

			const relationsBlock = 'c2Vuc2U6IG51bGwqIHZvbHVtZTogbnVsbCogbm9pc2luZXNzOiBudWxsKiBjYXB0dXJlOiBudWxsKiB2b2ljZTogbnVsbCogdmlzaW9uOiBudWxsKiBjb25uZWN0aXZpdHk6IG51bGwqIGVtcGF0aHk6IG51bGw=';

			const relationsAllow = Object.freeze(self.relations) || undefined;

			// Preload to resource
			for( let l of RR.sel('[rel="preload"]') ) {

				l.rel = 'stylesheet';

			}

			// Mobile support
			if( RR.isM ) {

				RR.addClass('html, #RevolverRoot', 'revolver__mobile-friendly');

				RR.new('style', 'head', 'in', {

					html: ':root { --scale-factor: 3; }',

					attr: {

						'media': 'all',

					}

				});

			}

			// Block any connections
			function SenseBlock(Rx) {

				RR.hash(Rx, 512, function(x) {

					if( RR.sel('style') ) {

						RR.rem('style');

					}

					if( (this +'') === ((('9000' - 0) * .01) +'WbadVzev8tD6ngVFst1o3E5plFqog0EdR5vKFzLmmn4EKsqeg5ZaDnGLtpsvTpjUWH7wJEYlsHbod9opg3qg==') ) {

						self.relations = atob(Rx).replace(/\*/g, '; ').split('; ');

						RR.new('style', 'head', 'in', {

							html: 'head {'+ atob(Rx).replace(/\*/g, '; ') +'; } .revolver__logo::before, .revolver__logo::after { color: #b00000cf; }',

							attr: {

								'data-hash': this, 'media': 'all'

							}

						});

						RR.senseAllowed = null;

						RR.nullRun = true;

						console.log('🚫 Access to sense denied ... ');

					}
					else {

						xBlock( xfullBlock );

						void setInterval( RR.detachEvents , 15000 );

					}

				});

			}

			// Make block
			function xBlock(Dx) {

				RR.hash(Dx, 384, function(x) {

					self.relations = atob(Dx).replace(/\*/g, '; ').split('; ');

					if( RR.sel('style') ) {

						RR.rem('style');

					}

					RR.new('style', 'head', 'in', {

						html: 'head {'+ atob(Dx).replace(/\*/g, '; ') +'; } .revolver__logo::before, .revolver__logo::after { color: #b00000cf; }',

						attr: {

							'data-hash': this, 'media': 'all'

						}

					});

					RR.senseAllowed = null;

					RR.nullRun = true;

					console.log('🚫 Detected sense policy violation');

				});

			}

			// Make unwired
			function Sense(R, Rx) {

				if( !RR.senseAllowed ) {

					RR.hash(R.join('*'), 384, function(x) {

						if( RR.sel('style') ) {

							RR.rem('style');

						}

						if( (this +'') === 'dP4is3dg7KFbr8lud8rEBfsvaUo/PWdz5K6QlKEDbY3WVgbqrIdqUaXO7FYXLder') {

							self.relations = R;

							RR.new('style', 'head', 'in', {

								html: 'head {'+ R.join('; ') +'; } .revolver__logo::before, .revolver__logo::after { color: rgba(100, 100, 100, .4); }',

								attr: {

									'data-hash': this, 'media': 'all'

								}

							});

							console.log('❤️ Access to sense granted ... ');

							RR.senseAllowed = true;

						}
						else {

							RR.hash(Rx, 512, function(x) {

								self.relations = atob(Rx).replace(/\*/g, '; ').split('; ');

								if( (this +'') === ((('9000' - 0) * .01) +'WbadVzev8tD6ngVFst1o3E5plFqog0EdR5vKFzLmmn4EKsqeg5ZaDnGLtpsvTpjUWH7wJEYlsHbod9opg3qg==') ) {

									SenseBlock(Rx);

								}
								else {

									for( i of RR.sel('[data-xhash]') ) {

										i.dataset.xhash = null;

									}

									xBlock( xfullBlock );

									void setInterval( RR.detachEvents, 15000 );

								}

							});

							RR.senseAllowed = null;

						}

					});
				}

			}

			// Check Sense policy
			void setInterval(() => {

				if( RR._privacyPolicyAccepted ) {

					RR.isU( relationsAllow ) || document.location.protocol === 'http:' ? SenseBlock( relationsBlock ) : Sense( relationsAllow, relationsBlock );	

				} 
				else {

					if( !RR.nullRun ) {

						SenseBlock( relationsBlock );

					}

				}


			}, 3000);

			// Set title fot history
			self.onpopstate = (e) => {

				document.title = (self.history.state) ? self.history.state.title : RR.title;

			}

		},

		// Screen position future
		screenPosition: (current, maximum, mode) => {

			(() => {

				if( !RR.sel('#screen-position') ) {

					// define events lock
					RR.screenPositionDefined = [null, null];

					// screen position progress
					RR.new('progress', 'body', 'before', {

						attr: {

							id: 'screen-position',
							style: 'position: fixed; bottom: 0; height: .4vw; width: 100vw; z-index: 10000;'

						}

					});

				}

			})();

			function setPosition(current, maximum, m) {

				var current = m ? current : self.scrollY;
				var maximum = m ? maximum : self.document.body.scrollHeight - self.innerHeight;
				let style   = m ? 'yellowProgress' : 'greenProgress';

				RR.attr('#screen-position', {

					'max': maximum,
					'value': current,
					'class': style

				});

			}

			if( !mode ) {

				if( !RR.screenPositionDefined[0] ) {

					RR.event(document, 'scroll:lock', () => {

						setPosition();

						RR.screenPositionDefined[0] = true;

					});

				}

				if( !RR.screenPositionDefined[1] ) {

					RR.event(self, 'resize:lock', () => {

						setPosition();

						RR.screenPositionDefined[1] = true;

					});

				}

				void setTimeout(() => {

					setPosition();

				}, 100);

			}
			else {

				setPosition(current, maximum, true);

			}

		},

		// Browser events futures support
		event: (e, evt, c) => {

			var e = (e.length) ? RR.htmlObj(e) : [e];

			var eMode = evt;
			var eLock = null;

			if(e) {

				for (let i of e) {

					if(RR.isC(c)) {

						if( evt.includes(':lock') ) {

							eMode = evt.split(':')[0];  
							eLock = true;

						}

						switch( eMode ) {

							case 'click':
							case 'dblclick': 
							case 'mouseover':
							case 'keyup':
							case 'keydown':
							case 'wheel':
							case 'mouseout':
							case 'mousemove':
							case 'mouseenter':
							case 'mouseleave':
							case 'mouseup':
							case 'mousedown':
							case 'select':
							case 'contextmenu':
							case 'scroll':
							case 'resize':
							case 'submit':
							case 'touchstart':
							case 'touchmove':
							case 'touchcancel':
							case 'touchend':
							case 'touchenter':
							case 'touchleave':

								var m = eMode;

								break;

							default:

								return;

						}

						// log event
						RR.events.push([i, eLock ? m +'::lock' : m, c]);

						i.addEventListener(m, c, {

							passive: null // {passive: true} || false - true

						});

					}

				}

			}

		},

		// Fetch future
		fetch: function(u = null, m = 'get', d = 'text', e = null , f = null) {

			let params = {

				credentials: 'same-origin',
				mode: 'same-origin',
				redirect: 'follow',
				referrer: 'client',
				cache: 'default',
				method: m

			};

			if( ['POST', 'PUT'].includes(m.toUpperCase()) && f ) {

				params.body = RR.FormData ? RR.FormData : d;

			}

			const R = new Request(u, params);

			// Fetch URI
			fetch(R).then(( r ) => {

				RR.screenPosition(.4, 1, true);

				if( r.ok ) {

					let f;

					switch(d) {

						default:
						case 'text':

							f = r.text();

							break;

						case 'json':

							f = r.json();

							break;

					}

					RR.screenPosition(.7, 1, true);

					return f;

				}

			}).then(( k ) => {

				// Detach all events
				if( e ) {

					RR.detachEvents();

				}

				RR.screenPosition(1, 1, true);

				RR.FormData = null;

				if( k ) {

					f.call( k );

				}

			});

		},

		// Events detach future
		detachEvents: () => {

			for( let i = RR.events.length; i--; ) { 

				RR.events[i][0].removeEventListener(RR.events[i][1], RR.events[i][2], false);

				if ( !RR.events[i][1].includes('::lock')) { // ignore locked events

					RR.events.pop();

				}

			}

		},

		// Form submission future based on fetch API
		fetchSubmit: (f, t = 'text', c) => {

			RR.event(f, 'submit', function(e) {

				e.preventDefault();

				if( e.isTrusted ) {

					let action = this.action !== document.location.pathname ? this.action : document.location.pathname;
					let method = RR.attr(this, 'method')[0].toUpperCase();

					let formInputs = this.querySelectorAll("input[type='text'], input[type='file'], input[type='hidden'], input[type='email'], input[type='number'], input[type='password'], input[type='date'], input[type='time'], input[type='tel'], input[type='url'], input[type='month'], input[type='week'], input[type='search'], input[type='color'], input[type='range']"); 
					let formRadiosCheckboxes = this.querySelectorAll("input[type='radio'], input[type='checkbox']");
					let formTextareas = this.querySelectorAll('textarea');
					let formSelect = this.querySelectorAll('select');

					let data = new FormData();

					// text and other formats
					if(formInputs.length) {

						for(let j of formInputs) { 

							if( j.type === 'file' ) {

								let fn = 0;

								for( let k of j.files ) {

									if( RR.isO(k)) {

										data.append( btoa(j.name +'-'+ fn), k );

										++fn;

									}

								}

							} 
							else {

								data.append( btoa(j.name), RR.utoa(j.value +'~:::~'+ j.type +'~:::~'+ ( j.maxLength ? j.maxLength : -1)) );

							}

						}

					}

					// multi string long text
					if(formTextareas.length) {

						for(let u of formTextareas) {

							data.append( btoa(u.name), RR.utoa(u.value +'~:::~text' +'~:::~'+ ( u.maxLength ? u.maxLength : -1)) );

						}

					}

					// boolean elements
					if(formRadiosCheckboxes.length) {

						for(let l of formRadiosCheckboxes) {

							if( RR.attr(l, 'checked').includes('checked') ) {

								data.append( btoa(l.name), RR.utoa(l.value + '~:::~'+ l.type +'~:::~'+ ( l.maxLength ? l.maxLength : -1)) );

							}

						}

					}

					if(formSelect.length) {

						// selects elements
						for(let s of formSelect) {

							if( !RR.isU(s.name) ) {

								let options = s.querySelectorAll('option'), name = s.name, c = 0;

								for(let i of options) {

									let option = i;

									if( RR.attr(i, 'selected').includes('selected') ) {

										data.append( btoa(name +'***'+ c), RR.utoa(i.value +'~:::~option' +'~:::~-1') );

										c++;

									}

								}

							}

						}

					}

					RR.FormData = data;

					// Perform parameterized fetch request
					RR.fetch(action, method, t, true, function() {

						c.call(this);

					});

				}

			});

		},

		// Perform HTML forms design improvements
		formBeautifier: () => {

			let checkboxes = RR.sel('input[type="checkbox"], input[type="radio"]');

			if( checkboxes ) {

				for (let i of checkboxes) {

					let parent = i.parentElement;
					let checked = RR.attr(i, 'checked')[0];

					i.outerHTML = '<div class="revolver__form-hidden-input">'+ i.outerHTML +'</div>';

					if( parent.tagName !== 'LABEL' ) {

						parent = parent.parentElement;

					} 

					RR.addClass([parent], 'checkbox-style');

					if( RR.attr(i, 'type')[0] === 'checkbox' ) {

						RR.addClass([parent], 'checkbox');

					} 
					else {

						RR.addClass([parent], 'radiobox');

					}

					parent.innerHTML = parent.innerHTML + '<div class="revolver__form-input-replacer checkbox-marker"></div>';

					if( ['checked', ''].includes(checked) ) {

						RR.addClass([parent], 'label-active');
						RR.addClass(parent.querySelectorAll('.checkbox-marker'), 'checkbox-checked');

					}

				}

				RR.event('label.checkbox-style', 'click', function(evt) {

					if( evt.isTrusted ) {

						let check = this.querySelectorAll('input[type="checkbox"], input[type="radio"]');
						let label = this.querySelectorAll('.checkbox-marker'); 

						if( RR.attr(check[0], 'type')[0] === 'radio' ) { 

							let allRadios   = this.closest('fieldset').querySelectorAll('input[type="radio"]');
							let allLabels   = this.closest('fieldset').querySelectorAll('.checkbox-marker');
							let allWrappers = this.closest('fieldset').querySelectorAll('.checkbox-style');

							let cnt = 0;

							for (let x of allRadios) {

								RR.attr(x, {'checked': null});

								RR.removeClass([allWrappers[cnt]], 'label-active');
								RR.removeClass(label, 'checkbox-checked');

								RR.addClass(label, 'checkbox-unchecked');
								RR.removeClass([allLabels[cnt]],'checkbox-checked');

								RR.addClass(label, 'checkbox-checked');

								RR.addClass([this], 'label-active');

								RR.removeClass(label, 'checkbox-unchecked');

								cnt++;
							}

							RR.attr(this.querySelectorAll('input[type="radio"]')[0], {'checked': 'checked'});

						} 

						if( RR.attr(check[0], 'type')[0] === 'checkbox' ) {

							if( !check[0].disabled ) {

								if( check[0].checked ) {

									RR.attr(check[0], {'checked': null});

									RR.removeClass([this], 'label-active');

									RR.removeClass(label, 'checkbox-checked');
									RR.addClass(label, 'checkbox-unchecked');

								} 
								else {

									RR.attr(check[0], {'checked': 'checked'});

									RR.addClass([this], 'label-active');

									RR.addClass(label, 'checkbox-checked');
									RR.removeClass(label, 'checkbox-unchecked');

								}

							}

						}

					}

				});

			}

			let selects = RR.sel('select');

			if( selects ) {

				RR.event('body', 'click', function(x) {

					let target = this.querySelectorAll('.revolver__form-option-target')[0];

					if( target && x.isTrusted ) {

						x.cancelBubble = true;

						if( !RR.hasClass([x.target], 'revolver__form-option-target') ) {

							let listToHide = RR.htmlObj('.styled-select dfn');

							for (let h of listToHide) {

								if( h ) {

									h.style.visibility = 'hidden';	

								}

							}

							RR.removeClass('.target', 'select-opened');

						}

					}

				});

				for (let u of selects) {

					let options = u.querySelectorAll('option');
					let parent  = u.parentElement;
					let opts    = '';
					let cnt     = 0;

					for (let x of options) {

						let cls = '';

						if( RR.attr(x, 'selected').includes('selected') ) {

							cls = 'selected';

						}

						if( cnt <= options.length ) {

							opts += '<div data-index="'+ cnt +'" class="revolver__form-option-replacer styled-option '+ cls +'">'+ x.innerHTML +'</div>';

						}

						cnt++;
					}

					if( parent.tagName === 'LABEL' ) {

						RR.addClass([parent], 'styled-select' );

					} 
					else {

						RR.wrap([u], 'label');
						RR.addClass([parent], 'styled-select');

					}

					parent.innerHTML = '<dfn class="revolver__form-options-container" style="display: block; visibility: visible; width: 48.8%;">'+ opts +'</dfn>'+ parent.innerHTML;

					var selected = parent.querySelectorAll('div.selected'); 

					if( selected.length ) {

						var wrp = selected[0].closest('label');

						wrp.innerHTML = '<span style="width:50%" class="revolver__form-option-target target">'+ selected[0].innerText +'</span>'+ wrp.innerHTML;

					} 
					else {

						selected = parent.querySelectorAll('div');

						if( selected.length ) {

							var wrp = selected[0].closest('label');

							wrp.innerHTML = '<span style="width:50%" class="revolver__form-option-target target">'+ selected[0].innerText +'</span>'+ wrp.innerHTML;

						}

					}

					void setTimeout(() => { 

						parent.querySelectorAll('.revolver__form-options-container')[0].style.visibility = 'hidden';

					}, 1150);

				}

				RR.event('.target', 'click', function(e) {

					e.preventDefault();

					if( e.isTrusted ) {

						let list = this.closest('label').querySelectorAll('dfn')[0];
						let tgtx = this;

						RR.addClass([tgtx], 'select-opened');

						list.style.visibility = 'visible';

					}

				});

				RR.event('.styled-option', 'click', function(e) {

					e.preventDefault();

					if( e.isTrusted ) {

						let selectItem = this.parentNode.parentNode.querySelectorAll('select')[0];
						let callback   = selectItem.dataset.callback;

						let label = this.closest('label');
						let list  = label.querySelectorAll('dfn')[0];

						let tgt   = label.querySelectorAll('.target');
						let slc   = label.querySelectorAll('option');
						let cls   = label.querySelectorAll('.styled-option');

						label.querySelectorAll('.target')[0].innerHTML = this.innerText;

						let current = this.dataset.index - 0;

						for(let k of slc) {

							RR.attr(k, {'selected': null});

						}

						for(let x of cls) {

							RR.removeClass([x], 'selected');

						}

						RR.attr(slc[current], {'selected': 'selected'});
						RR.addClass([cls[current]], 'selected');;

						list.style.visibility = 'hidden';

						RR.removeClass(tgt, 'select-opened');

						// Perform callback when select an option
						if( !RR.isU(callback) ) {

							let fn = RR[ callback ];

							if( RR.isF( fn ) ) { 

								let sopts = [];

								for(let o of slc) {

									sopts.push( o.selected ? ['choosen', o.value] : ['inlist', o.value] );

								}

								fn(sopts);

							}

						}

					}

				});

			}

		},

		// RevolveR Markup editor 
		markupEditor: () => {

			const textareas = RR.sel('textarea');

			if(textareas.length > 0) {

				// Place editor buttons before textareas
				for(let i of textareas) {

					let className = 'revolver__editor-'+ ( i.name.includes('=') ? atob( i.name ) : i.name );

					RR.addClass([i.parentElement], className);

					RR.wrap('textarea', 'output');

					RR.addClass([i.parentElement], 'revolver__editor-area');

					RR.new('dfn', '.revolver__editor-area', 'before', {

						html: '<i>[H2]</i> <i>[H3]</i> <i>[H4]</i> <i>[P]</i> <i>[DL]</i> <i>[UL]</i> <i>[OL]</i> <i>[BR]</i> <i>[B]</i> <i>[I]</i> <i>[U]</i> <i>[S]</i> <i>[IMG]</i> <i>[A]</i> <i>[Smiles]</i> <i class="revolver__content-preview">[Preview]</i>', 

						attr: { 

							class: 'revolver__editor_buttons'

						}

					});

					RR.event('.'+ className +' i', 'click', function(e) {

						e.preventDefault();

						if( e.isTrusted ) {

							let tag = this.innerText.replace('[', '').replace(']', '');

							function cursorPosition(textarea) {

								return [textarea.selectionStart, textarea.selectionEnd];

							}

							function getSelection(textarea, positions) {

								return textarea.value.substring(positions[0], positions[1]);

							}

							function makeTag( t, text ) {

								let HTMLMarkup;

								switch( t ) {

									default:

										HTMLMarkup = '<'+ t +'>'+ text +'</'+ t +'>';

										break;

									case 'dl': 

										HTMLMarkup = '<'+ t +'>\n<dt>'+ text +'</dt>\n<dd></dd>\n</'+ t +'>'; 

										break;

									case 'ol':
									case 'ul': 

										HTMLMarkup = '<'+ t +'>\n<li>'+ text +'</li>\n</'+ t +'>';

										break;

									case 'br': 

										HTMLMarkup = text +' <'+ t +' />';

										break;

								}

								return HTMLMarkup;

							}

							function wrappedText( textarea, t ) {

								return makeTag(t, getSelection(textarea, cursorPosition( textarea )));

							}


							function insertText( textarea, contents ) {

								textarea.focus();

								textarea.value = textarea.value.substring(0, cursorPosition(textarea)[0]) + contents + textarea.value.substring(cursorPosition(textarea)[1], textarea.value.length);

							}

							if( tag === 'IMG') {

								RR.modal('Insert image media', '<form id="revolver__editor-insert"><input name="alt" type="text" placeholder="Type alternative text" /><input type="url" placeholder="Input url" name="address"/><input type="button" value="Insert"></form>');

								RR.event('#revolver__editor-insert input[type="button"]', 'click', function(e) {

									e.preventDefault();

									if( e.isTrusted ) {

										insertText( i, '<figure>\n<img src="'+ RR.sel('#revolver__editor-insert input[name="address"]')[0].value +'" alt="'+ RR.sel('#revolver__editor-insert input[name="alt"]')[0].value +'" />\n<figcaption>'+ RR.sel('#revolver__editor-insert input[name="alt"]')[0].value +'</figcaption>\n</figure>' );

									}

								});

							}
							else if( tag === 'A') {

								RR.modal('Insert anchor','<form id="revolver__editor-insert"><input name="text" type="text" placeholder="Type anchor text" /><input type="url" placeholder="Type href url" name="address"/><input type="button" value="Insert"></form>');

								RR.event('#revolver__editor-insert input[type="button"]', 'click', function(e) {

									e.preventDefault();

									if( e.isTrusted ) {

										insertText( i, '<a href="'+ RR.sel('#revolver__editor-insert input[name="address"]')[0].value +'" title="'+ RR.sel('#revolver__editor-insert input[name="text"]')[0].value +'">'+ RR.sel('#revolver__editor-insert input[name="text"]')[0].value +'</a>' );

									}

								});

							} 
							else if( tag === 'Smiles' ) {

								RR.modal(

									'Smiles',

									'<div clas="smiles-list">\
										<div class="smiles-row"><b>😁</b><b>😂</b><b>😃</b><b>😄</b><b>😆</b><b>😉</b><b>🥵</b><b>😊</b><b>😋</b><b>😌</b><b>😍</b><b>😏</b><b>😒</b></div>\
										<div class="smiles-row"><b>😔</b><b>😖</b><b>😘</b><b>😚</b><b>😜</b><b>😝</b><b>😞</b><b>😠</b><b>😡</b><b>😢</b><b>😣</b><b>😤</b><b>😥</b></div>\
										<div class="smiles-row"><b>😩</b><b>😭</b><b>😰</b><b>😱</b><b>😲</b><b>😳</b><b>😵</b><b>😷</b><b>🥳</b><b>🤠</b><b>🥶</b><b>😓</b><b>😨</b></div>\
									<div>'

								);

								RR.event('#mBoxContent .smiles-row b', 'click', function(e) {

									e.preventDefault();

									if( e.isTrusted ) {

										insertText( i, e.target.innerText );

									}

								});

							}
							else if( tag === 'Preview' ) {

								RR.toggleClass([ e.target ], 'preview-active');

								let diff_values = {};

								let diff_flag = true;

								let first = true;

								let locked = null;

								let tObserver = null;

								let eObserved = R.sel('.revolver__new-fetch');

								// Type observer future 
								// prevent preview loading 
								// while typewriting or formating
								R.event(eObserved, 'click', (e) => {

									if( e.isTrusted ) {

										clearTimeout( tObserver );

										locked = true;

										tObserver = void setTimeout(() => {

											locked = null;

										}, 5000);

									}

								});

								R.event(eObserved, 'keydown', (e) => {

									if( e.isTrusted ) {

										clearTimeout( tObserver );

										locked = true;

										tObserver = void setTimeout(() => {

											locked = null;

										}, 5000);

									}

								});

								R.event(eObserved, 'keyup', (e) => {

									if( e.isTrusted ) {

										clearTimeout( tObserver );

										locked = true;

										tObserver = void setTimeout(() => {

											locked = null;

										}, 5000);

									}

								});
								
								if( !RR.hasClass([ e.target ], 'preview-active') ) {

									clearInterval( RR.preview );

									var preview = RR.sel('.revolver__preview');

									if( preview ) {

										RR.rem( preview );

									}

								} 
								else {

									let eaction = null;

									RR.preview = void setInterval(() => {

										if( !locked ) {

											RR.FormData = new FormData();

											let aform = this.closest('form').closest('form');
											let pmode = null;

											let fiteq = 1;

											if( aform.id.match('node') ) {

												pmode = 'node';

											}
											else if( aform.id.match('comment') ) {

												pmode = 'comment';

											}
											else if( aform.id.match('message')) {

												pmode = 'message';

											}
											else if( aform.id.match('feedback') ) {

												pmode = 'feedback';

												fiteq++;

											}

											RR.FormData.append(btoa('revolver_preview_mode'), RR.utoa(pmode +'~:::~text~:::~-1'));

											let inputs = RR.sel('.revolver__content-preview')[0].closest('form').querySelectorAll('input, textarea');

											let pass = true;

											RR.FormData.delete(btoa('revolver_country_code'));

											for( let i of inputs ) {

												let type = 'text';

												if( i.type === 'radio' ) {

													if( i.name === 'revolver_country_code' ) {

														if( i.checked ) {

															RR.FormData.append(btoa(i.name), RR.utoa( i.value +'~:::~text~:::~'+ (i.maxLength ? i.maxLength : -1)));

														}

													}

												}

												switch( i.type ) {

													case 'textarea':
													case 'hidden':
													case 'email':
													case 'text':
													case 'tel':
													case 'url':

													if( i.type !== 'hidden' && i.type !== 'textarea' ) {

														type = i.type;

													}

													switch( i.name ) {

														case 'revolver_feedback_message_title':
														case 'revolver_feedback_message_message':
														case 'revolver_feedback_message_sender_name':
														case 'revolver_feedback_message_sender_email':
														case 'revolver_feedback_message_sender_phone_number':

														case 'revolver_node_edit_description':
														case 'revolver_comment_user_email':
														case 'revolver_node_edit_content':
														case 'revolver_comment_user_name':
														case 'revolver_comment_content':
														case 'revolver_node_edit_title':
														case 'revolver_node_edit_route':
														case 'revolver_mailto_nickname':
														case 'revolver_mailto_message':
														case 'revolver_user_name':

															if( i.value.length <= 5 ) {

																pass = null;

															}

														break;

														case 'revolver_comments_action_edit':

															eaction = true;

															break;

													}

													if( first ) {											

														diff_flag = true;

													}

													if( !first ) {

														if( diff_values[ i.name ] !== RR.utoa( i.value ) ) {

															diff_flag = true;

														}

													}

													diff_values[ i.name ] = RR.utoa( i.value );

													RR.FormData.append(btoa(i.name), RR.utoa( i.value +'~:::~'+ type +'~:::~'+ (i.maxLength ? i.maxLength : -1)));

												}

											}

											// Render
											if( pass && diff_flag ) {

												var preview = RR.sel('.revolver__preview');

												if( preview ) {

													RR.styleApply(preview, ['margin-bottom:2vw']);

													RR.animate(preview, ['opacity:0:2500:flicker', 'height:0px:3000:wobble', 'margin-bottom:5vw:2500:spring'], () => {

														RR.toggle(preview);

														RR.styleApply(preview, ['overflow: hidden', 'height:0px']);

														RR.rem(preview);

													});

												}

												RR.fetch('/preview/', 'post', 'html', null, function() {

													// Render preview
													RR.new('div', '#'+ aform.id, 'fit-in:'+ fiteq, {

														html: this,

														attr: {

															'class': 'revolver__preview'+ ( eaction ? ' edit_form' : '' ),
															'style': 'opacity: 0;'

														}

													});

													var preview = RR.sel('.revolver__preview');

													if( RR.isset(preview[0]) ) {

														RR.event(preview[0].querySelectorAll('.revolver__status-notifications .revolver__statuses-heading i'), 'click', function(e) {

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

													}

													RR.animate(preview, ['opacity:.9:2500:flicker'], () => {

														if( RR.isset(preview[1]) ) {

															RR.rem([preview[1]]);

														}

													});

												});

												void setTimeout(() => {

													let route = aform.action.replace( document.location.origin, '' );

													RR.fetch('/secure/?route='+ route, 'get', 'json', null, function() {

														RR.useCaptcha( this.key );

													});

													diff_flag = first = null;

												}, 2500);

											}

										}

									}, 5000);

								}

							}
							else {

								insertText(i, wrappedText(i, tag.toLowerCase()));

							}

						}

					});

				}

			}

		},

		// Location API 
		// c - is a callback
		location: (title, url, c = null) => {

			document.title = title;

			self.history.pushState({'title': title, 'url': url}, '', url);

			RR.callback(c, [title, url]);

		},

		// Modal window future
		modal: (t = 'mBox title', d = 'mBox contents', q, c) => {

			// Make new modal window with overlay or without
			var q = (q) ? '#overlay' : 'body';

			// Calculate default sizes s[0:width,1:height]
			if(d && d.length > 10 && !RR.lockModalBox) {

				let setPosition = (e, xy) => {

					RR.styleApply([e], ['left:'+ Math.round(xy[0]) +'px', 'top:'+ Math.round(xy[1]) +'px']);

				};

				RR.lockModalBox = true;
				RR.StopMoving = null;

				// Apply modal window
				RR.new('div', 'body', 'in', {

					html: '<div style="opacity:.1;" id="mBox"><header><i id="mBoxTitle">'+ t +'</i><b id="mBoxClose">X</b></header><div id="mBoxContent">'+ d +'</div></div>',
					attr: {

						id: 'overlay',

					}

				});

				let modalBox = RR.htmlObj('#mBox')[0];

				// Centering positions 
				let CenterTop  = ( RR.currentSizes[1] - (modalBox.offsetHeight - 0) ) / 3;
				let CenterLeft = ( RR.currentSizes[0] - (modalBox.offsetWidth - 0) ) / 2;

				// Center modal window
				setPosition(modalBox, [CenterLeft, CenterTop]);

				// Animate opacity
				RR.animate('#mBox', ['opacity:1:1500:linear']);

				// Redraw position
				RR.event(self, 'resize', () => {

					setPosition(modalBox, [(RR.currentSizes[0] - (modalBox.offsetWidth - 0) ) / 2, ( RR.currentSizes[1] - (modalBox.offsetHeight - 0) ) / 3]);

				});

				// Drag modal window event
				RR.event('#mBox header', 'mousedown', function(e) {

					if( e.isTrusted ) {

						let mFixRealPosL = RR.curxy[0] - RR.stripNum(RR.styleGet(modalBox, 'left'));
						let mFixRealPosT = RR.curxy[1] - RR.stripNum(RR.styleGet(modalBox, 'top'));

						RR.StopMoving = null;

						let x = this;

						RR.event('#overlay', 'mousemove', (e) => {

							if( !RR.StopMoving && e.isTrusted ) {

								setPosition(x.parentElement, [RR.curxy[0] - mFixRealPosL, RR.curxy[1] - mFixRealPosT]);

							}

						});

						RR.event('#overlay', 'mouseup', (e) => {

							if( e.isTrusted ) {

								RR.StopMoving = true;

							}

						});

					}

				});

				// Perform close event and frees execution
				RR.event('#mBoxClose', 'click', (e) => {

					if( e.isTrusted ) {

						RR.animate('#mBox', ['opacity:0:800:harmony']);

						void setTimeout(() => {

							RR.rem('#overlay');

						}, 900);

						RR.lockModalBox = null;

					}

				});

			}

		},

		// Show or hide elements
		toggle: (e, c) => {

			for(let i of RR.htmlObj(e)) {

				var x = RR.treeHacks(i);

				if( x.style.overflow === 'hidden' ) {

					RR.styleApply([x], ['overflow:visible', 'width', 'height', 'display', 'line-height']);

				}
				else {

					RR.styleApply([x], ['display:inline-block', 'overflow:hidden', 'width:0px', 'height:0px', 'line-height:0px']);

				};

				RR.callback(x, c)
			}

		},

		// Scroll screen position to an element
		scroll: (e = 'body') => {

			let t = RR.htmlObj(e);

			if( t ) {

				let y = t[0].offsetTop - t[0].offsetHeight - 50;

				RR.styleApply([t[0]], ['opacity:.1']);

				RR.animateMove(t[0], 'scroll', [RR.curOffset[1], y], 1500, 'linear', e);

			}

		},

		// Expand future for animatable elements 
		expand: (s, c) => {

			// Collapsible toggle
			RR.event(s, 'click', function(e) {

				e.preventDefault();

				if( e.isTrusted ) {

					let expander = this.nextSibling;
					var expanded = null;

					// RevolveR CMF Exception :: Definition Lists Expand future
					if(this.tagName.toLowerCase() === 'dt') {

						RR.toggle([expander]);

						return;

					}

					RR.toggleClass([this], 'collapse-expanded');
					RR.toggleClass([expander], 'expander-expanded');

					if( RR.hasClass([this], 'collapse-expanded') ) {

						RR.toggle([expander]);

						RR.styleApply([expander], ['width: 100%', 'display: inline-block', 'min-height:'+ expander.offsetHeight +'px', 'opacity: 0', 'transform:scale(.1,.1,.1)']);

						RR.animate([expander], ['opacity:1:2000:linear','transform:scale(1,1,1):2000:elastic'], () => {

							RR.callback(expander, c, [true]);

						});

					}
					else {

						RR.styleApply([expander], ['display: inline-block', 'min-height: 0', 'height:'+ expander.offsetHeight +'px', 'opacity:1']);

						RR.animate([expander], ['opacity:0:800:linear', 'height:0px:1500:linear'], () => {

							RR.toggle([expander]);

							RR.styleApply([expander], ['overflow: hidden', 'height:0px']);

							RR.callback(expander, c, [null]);

						});

					}

				}

			});

		},

		// Toggle class ( [el] [class name] )
		toggleClass: (e, c) => {

			for(let i of RR.htmlObj(e)) {

				i.classList.toggle(c);

			}

		},

		// This helper removes class
		removeClass: (e, c) => {

			for(let i of RR.htmlObj(e)) {

				i.classList.remove(c);

			}

		},

		// This helper add class
		addClass: (e, c) => {

			for(let i of RR.htmlObj(e)) {

				i.classList.add(c);

			}

		},

		// This helper test for class value with given name are defined
		hasClass: (e, c) => {

			var f = null;

			for(let i of c.split(' ')) {

				if(RR.treeHacks(RR.htmlObj(e)).classList.contains(i)) {

					f = true;

				}

			}

			return f;

		},

		// Apply styles to element  
		styleApply: (e, s, c = null) => {

			var e = RR.htmlObj(e);

			if( e ) {

				for(let t of e) {

					for(let i of s) {

						let sets = RR.arguments(i, ':');

						if( RR.isset(sets[1]) ) {

							t.style[RR.normalizeStyleName(sets[0])] = sets[1];

						}
						else {

							t.style[RR.normalizeStyleName(sets[0])] = 'inherit';

						}

					}

				}

				RR.callback(e, c);

			}

		},

		// Get CSS properties value
		styleGet: (e, p) => {

			var p = RR.normalizeStyleName(p);

			var s = e.style[p] ? e.style[p] : getComputedStyle(e, null)[p];

			return RR.isU(s) ? '0' : s;

		},

		// Show elements
		show: (e, t) => {

			var e = RR.htmlObj(e);

			for(let s of e) {

				let	sh = s.savedHeight ? s.savedHeight : null; // return stored height
				let sd = s.savedDisplay ? s.savedDisplay : 'inherit';

				RR.styleApply([s], ['display:'+ sd]);

				if( sh ) {

					RR.animate([s], ['height:'+ sh +':'+ t]);

				}
				else {

					RR.styleApply([s], ['height:auto']);

				}

			}

		},

		// Hide elements
		hide: (e, t) => {

			var e = RR.htmlObj(e);

			for(let s of e) {

				s.savedHeight  = RR.styleGet(s, 'height'); // save states for show module
				s.savedDisplay = RR.styleGet(s, 'display');

			}

			RR.animate(e, ['height:0px:'+ t], () => {

				RR.styleApply([this], ['display:none']);

			});

		},

		// Animations for CSS properties
		animate: (e, g, c = null) => {

			var e = RR.htmlObj(e);

			// Execute animation queue
			let queueStack = [];

			for(let k of g) {

				queueStack.push( RR.arguments(k, ':')[2] );

			}

			// Get max value
			function ArrayMax(stack) {

				let max = stack[0];

				for(let i = 1; i < stack.length; i++) {

					if(RR.stripNum( stack[i] ) > max) {

						max = stack[i];

					}

				}

				return max;

			}

			// Higher time for callback
			const LQ = ArrayMax(queueStack);

			// Callback definitions
			let callbackProp;
			let callback = null;
			let callbackCounter = 0; 

			// Walk around selectors and properties
			for( let x of e ) {

				for(let i of g) {

					let p = RR.arguments(i, ':');
					let z = [...RR.shortToFull(x, p)];

					if(z[0][0] === 'transform') {

						p[1] = z[0][1] + '';

					}

					// Move queue
					if( (p[2] - 0) >= (RR.stripNum(LQ) - 0) && callbackCounter < 1 ) {

						callback = c;
						callbackCounter++;

					} 
					else {

						callback = null;

					}

					for(let l in z) {

						let prop = z[l][0];
						let dest = z[l][1];
						let unit = z[l][2];

						if( ['width', 'height', 'top', 'left', 'bottom', 'right'].includes(prop) ) {

							if( ['top', 'left', 'bottom', 'right'].includes(prop) ) {

								let pos = x.style.position;

								if( !['absolute', 'relative' ].includes( pos ) ) {

									x.style.position = 'relative';

								}

							}

							var from = RR.numberCSS(RR.styleGet(x, p[0]), p[0])[0];

							// Convert % to px
							if(unit === '%' && from !== 0) {

								dest *= from / 100;
								unit = 'px';

							}

						}
						else if( ['backgroundColor', 'borderBottomColor', 'borderLeftColor', 'borderRightColor', 'borderTopColor', 'color', 'outlineColor', 'textDecorationColor', 'columnRuleColor', 'textEmphasisColor', 'caretColor'].includes(prop) ) {

							if( /color/i.test(prop) ) {

								RR.colorMix(x, p[0], p[1], p[2], p[3], callback);

								if( callback ) {

									callback = null;

								}

							}

						}
						else if ( prop === 'transform' ) {

							// get default matrix defined as 2D
							let M2D = RR.arguments(RR.getValFromPropsBrackets('matrix', self.getComputedStyle(x, null)[prop] === 'none' ? 'matrix(1, 0, 0, 1, 0, 0)' : self.getComputedStyle(x, null)[prop])[1], ','); 

							// convert 2D matrix to 3D align
							let M3D = RR.arguments(RR.getValFromPropsBrackets('matrix3d', M2D.length <= 6 ? 'matrix3d('+ M2D[0] +', '+ M2D[1] +', 0, 0, '+ M2D[2] +', '+ M2D[3] +', 0, 0, 0, 0, 1, 0,'+ M2D[4] +','+ M2D[5] +', 0, 1)' : self.getComputedStyle(x, null)['transform'])[1], ',');

							// get current scale from matrix
							let scale3d = [M3D[0], M3D[5], M3D[10]];

							// get current rotate from matrix in degrees
							let pi = Math.PI;
							let sinB = parseFloat(M3D[8]);

							let b = Math.round(Math.asin(sinB) * 180 / pi);
							let cosB = Math.cos(b * pi / 180);

							let a = Math.round(Math.asin(-M3D[9] / cosB) * 180 / pi);
							let c = Math.round(Math.acos(M3D[0] / cosB) * 180 / pi);

							let angle3d = [a, b, isNaN(c) ? 0 : c ];

							// get translate
							let translate3d = [M3D[12] - 0, M3D[13] - 0, M3D[14] - 0];

							// get skew
							let skew3d = [Math.floor(M3D[4] / 0.0174532925),  Math.floor(M3D[1] / 0.0174532925)];

							// get perspective TODO: calculate it
							let perspective3d = -1 / (M3D[11] - 0); 

							// compare transforms to animate
							var transforms = [];

							function * compareTransformProp(p, s) {

								let exe = RR.getValFromPropsBrackets(p, s);

								if( exe ) {

									let start;

									function axisIndex(a, p) {

										let i; 

										switch(a.replace(p, '')) {

											case 'X': i = 0;

												break;

											case 'Y': i = 1;

												break;

											case 'Z': i = 2;

												break;

										}

										return i;

									}

									function propAxis(p) {

										let match = ['translate', 'skew', 'rotate', 'scale', 'perspective'];

										for( let i of match ) {

											if( p.includes(i) ) {

												let index = axisIndex(p, i);
												let s;

												switch( i ) {

													case 'translate':

														s = translate3d[index];

														break;

													case 'skew':

														s = skew3d[index];

														break;

													case 'rotate':

														s = angle3d[index];

														break;

													case 'scale':

														s = scale3d[index];

														break;

													case 'perspective':

														s = perspective3d;

														break;

												}

												return s;

											}

										}

									}

									yield !!p ? [p, [propAxis(p), RR.numberCSS(exe[1])[0], RR.numberCSS(exe[1])[1]]] : null;

								}

							}

							function packTransform(p, d) {

								let axis = ['X', 'Y', 'Z'];
								let c = [];

								for(let i in axis) {

									let prop;

									switch(p) {

										case 'scale':
										case 'translate': 
										case 'rotate': 

											prop = p + axis[i];

											break;

										case 'skew': 

											if( i <= 1 ) {

												prop = p + axis[i];

											}

											break;

										case 'perspective': 

											if( i <= 0 ) {

												prop = p;

											}

											break;
									}

									if(prop) {

										c.push(prop);

									}

								}

								for(let o of c) {

									transforms.push([...compareTransformProp(o, d)][0]);

								}

							}

							if( p[1].includes('rotate') ) {

								packTransform('rotate', p[1]);

							}

							if( p[1].includes('skew') ) {

								packTransform('skew', p[1]);

							}

							if( p[1].includes('translate') ) {

								packTransform('translate', p[1]);

							}

							if( p[1].includes('scale') ) {

								packTransform('scale', p[1]);

							}

							if( p[1].includes('perspective') ) {

								packTransform('perspective', p[1]);

							}

							if( transforms.length > 0 ) {

								RR.animateMatrix(x, transforms, p[2], p[3], callback);

								if( callback ) {

									callback = null;

								}

							}

						}

						// Other CSS values
						else {

							var from = RR.numberCSS(RR.styleGet(x, p[0]))[0];

							if(!from && from !== 0) {

								from = RR.numberCSS(RR.styleGet(x, z[l][3]))[0];

							}

						}

						// Perform animation for element with propertie
						if( prop !== 'transform' && prop !== 'color' ) {

							RR.animateMove(x, prop, [from, dest, unit], p[2], p[3], callback);

							if( callback ) {

								callback = null;

							}

						}

					}

				}

			}

		},

		// Module slide
		// [element] :: ( #parents fixed container -> .slide selector )
		slide: (e, t = 3000) => {

			var e = RR.htmlObj(e);
			let i = 0;

			// Make window flag to get timerId access on fetch
			self.rotate = void setInterval(

				() => {

					if(e) {

						e[i].style.zIndex = 0; 

						i++;

						i = (i - 0) === e.length ? 0 : i - 0;

						e[i].style.zIndex = 1;

					}

				}, 

			t); 

		},

		// Module tabs 
		// p - control selectors   ( like  [ul > li] )
		// e - switchable contents ( like [div] )
		tabs: (e, p, c) => {
			
			let t = p.split(' ')[0]; // get parents selector to prevent other tabs to be switched

			var e = RR.htmlObj(e);
			var p = RR.htmlObj(p +'[data-content]');

			RR.event(e, 'click', function(evt) {

				evt.preventDefault();

				if( evt.isTrusted ) {

					RR.attr(t +' .tabactive', { 'class': null, 'style': 'visibility:hidden' });
					RR.attr(t +' .activetab', { 'class': null });

					for (let i of p) {

						if (i.hasAttribute('data-content')) {

							if (RR.attr(i, 'data-content')[0] === RR.attr(this, 'data-link')[0]) {

								RR.attr(this, { 'class': 'activetab' });
								RR.attr(i, {'class': 'tabactive', 'style': 'visibility: visible'});

							}

						}

					}

				}

			});

		},

		// Move some units
		animateMove: (e, p, v, t, r, c) => {

			// v arg - [from, dest, unit);
			let s = performance.now();
			let m = (v[0] - v[1]) / t;

			let cnt = 0;

			void requestAnimationFrame(

				function frame(d) {

					// g - time gone; s - start time; m - speed;  z - delta
					let g = d - s;
					let z = v[0] - (m * g);

					// Time escape preventing
					if (g > t) {

						g = t;

					}

					// Apply FX's
					let f = RR.effects(r, g / t);

					if(p === 'scroll') {

						self.scrollTo(0, z * f);

					} 
					else {

						e.style[p] = (v[2]) ? Math.floor(z * f) + v[2] : z * f;		

					}

					// Animation time is over? If not perform next frame
					if (g < t) {

						requestAnimationFrame(frame);

					} 
					else {

						if(p === 'scroll') {

							RR.animate(c, ['opacity:1:700:easeInBack']);

						} 
						else {

							// Hard fix CSS to prevent escaping ranges
							e.style[p] = v[1] + v[2];

							if( c && cnt < 1) {

								c.call(e);

								cnt++;

							}

						}

					}

				}

			);

		},

		// Get values propertie from brackets
		getValFromPropsBrackets: (p, v) => (new RegExp(p +'\\(([^)]+)\\)').exec(v)),

		// Replaces values in CSS matrix
		setMatrixCss: (e, p, v) => {

			let c = e.style['transform'];

			if( !c.includes(p) ) {

				c += ' '+ p +'(0) ';

			}

			e.style['transform'] = c.replace(RR.getValFromPropsBrackets(p, c)[0], '').trim() +' '+ p +'('+ v +')'; 

		},

		// Animate transformable CSS matrix properties
		animateMatrix: (e, tr, t, fx, c) => {

			var cnt = 0;

			for(let i of tr) {

				if( i ) {

					//s = performance.now(); 	   // time now
					//m = (i[1][0] - i[1][1]) / t; // speed
					//x = i[1][0]; 				   // destination
					//h = i[1][1]; 				   // duration
					//y = i[1][2]; 				   // units
					//p = i[0];    				   // propertie

					((s, p, m, x, y, h) => {

						void requestAnimationFrame(

							function frame(d) {

								// g - time gone; s - start time; m - speed;  z - delta
								let g = d - s;
								let z = x - (m * g);

								// Time escape preventing
								if (g > t) {

									g = t;

								}

								// Apply FX's
								let f = RR.effects(fx, g / t);

								// Test for units are defined and set CSS value correct
								RR.setMatrixCss(e, p, z * f + (y ? y : ''));

								// Animation time is over? if not perform next frame
								if (g < t) {

									requestAnimationFrame(frame);

								} 
								else {

									// Fix endpoint to prevent escaping ranges
									RR.setMatrixCss(e, p, h + (y ? y : ''));

									if( c && cnt < 1 )  {

										c.call(e);

										cnt++;
									}

								}

							}

						);

					})(performance.now(), i[0], (i[1][0] - i[1][1]) / t, i[1][0], i[1][2], i[1][1]);

				}

			}

		},

		// FX's math
		effects: (fx, f = 1) => {

			switch(fx) {

				case 'easeIn': 

					f = Math.pow( f, 5 );

					break;

				case 'easeOut': 

					f = 1 - Math.pow( 1 - f, 5 );

					break;

				case 'easeOutStrong': 

					f = (f == 1) ? 1 : 1 - Math.pow(2, -10 * f);

					break;

				case 'easeInBack': 

					f = (f) * f * ((1.70158 + 1) * f - 1.70158);

					break;

				case 'easeOutBack': 

					f = (f = f - 1) * f * ((1.70158 + 1) * f + 1.70158) + 1;

					break;

				case 'easeOutQuad': 

					f = f < .5 ? 2 * f * f : -1 + (4 - 2 * f) * f;

					break;

				case 'easeOutCubic': 

					f = f * f * f;

					break;

				case 'easeInOutCubic': 

					f = f < .5 ? 4 * f * f * f : (f - 1) * (2 * f - 2) * (2 * f - 2) + 1;

					break;

				case 'easeInQuart': 

					f = f * f * f * f;

					break;

				case 'easeOutQuart': 

					f = 1 - (--f) * f * f * f;

					break;

				case 'easeInOutQuart': 

					f = f < .5 ? 8 * f * f * f * f : 1 - 8 * (--f) * f * f * f;

					break;

				case 'easeInQuint': 

					f = f * f * f * f * f;

					break;

				case 'easeOutQuint': 

					f = 1 + (--f) * f * f * f * f;

					break;

				case 'easeInOutQuint': 

					f = f < .5 ? 16 * f * f * f * f * f : 1 + 16 * (--f) * f * f * f * f;

					break;

				case 'elastic': 

					f = Math.pow(2, 10 * (f - 1)) * Math.cos(20 * Math.PI * 1.5 / 3 * f);

					break;

				case 'easeInElastic': 

					f = (.04 - .04 / f) * Math.sin(25 * f) + 1;

					break;

				case 'easeOutElastic':  

					f = .04 * f / (--f) * Math.sin(25 * f);

					break;

				case 'easeInOutElastic': 

					f = (f -= .5) < 0 ? (.01 + .01 / f) * Math.sin(50 * f) : (.02 - .01 / f) * Math.sin(50 * f) + 1;

					break;

				case 'easeInSin':  

					f = 1 + Math.sin(Math.PI / 2 * f - Math.PI / 2);

					break;

				case 'easeOutSin':  

					f = Math.sin(Math.PI / 2 * f);

					break;

				case 'easeInOutSin':  

					f = (1 + Math.sin(Math.PI * f - Math.PI / 2)) / 2;

					break;

				case 'easeInCirc':  

					f = -(Math.sqrt(1 - (f * f)) - 1);

					break;

				case 'easeOutCirc':  f = Math.sqrt(1 - Math.pow((f - 1), 2));

					break;

				case 'easeInOutCirc':  

					f = ((f /= .5) < 1) ? -.5 * (Math.sqrt(1 - f * f) - 1) : .5 * (Math.sqrt(1 - (f -= 2) * f) + 1);

					break;

				case 'easeInQuad':  f = f * f;

					break;

				case 'easeInExpo':  

					f = (f === 0) ? 0 : Math.pow(2, 10 * (f - 1));

					break;

				case 'easeOutExpo':  

					f = (f === 1) ? 1 : -Math.pow(2, -10 * f) + 1;

					break;

				case 'easeInOutExpo':  

					f = ((f /= .5) < 1) ? .5 * Math.pow(2, 10 * (f - 1)) : .5 * (-Math.pow(2, -10 * --f) + 2);

					break;

				case 'easeOutBounce':  

					if ((f) < (1 / 2.75)) {

						f = (7.5625 * f * f);

					} 
					else if (f < (2 / 2.75)) {

						f = (7.5625 * (f -= (1.5 / 2.75)) * f + .75);

					} 
					else if (f < (2.5/2.75)) {

						f = (7.5625 * (f -= (2.25 / 2.75)) * f + .9375);

					} 
					else {

						f = (7.5625 * (f -= (2.625 / 2.75)) * f + .984375);

					}

					break;

				case 'bouncePast': 

					if (f < (1 / 2.75)) {

						f = (7.5625 * f * f);

					} 
					else if (f < (2 / 2.75)) {

						f = 2 - (7.5625 * ( f -= (1.5 / 2.75)) * f + .75);

					} 
					else if (f < (2.5 / 2.75)) {

						f = 2 - (7.5625 * ( f -=(2.25 / 2.75)) * f + .9375);

					} 
					else {

						f = 2 - (7.5625 * ( f -=(2.625 / 2.75)) * f + .984375);

					}

					break;

				case 'swingTo': 

					f = (f -= 1) * f * ((1.70158 + 1) * f + 1.70158) + 1;

					break;

				case 'swingFrom': 

					f = f * f * ((1.70158 + 1) * f - 1.70158);

					break;

				case 'spring': 

					f = 1 - (Math.cos(f * 4.5 * Math.PI) * Math.exp(-f * 6));

					break;

				case 'blink': 

					f = Math.round(f * (5)) % 2;

					break;

				case 'pulse': 

					f = ( -Math.cos((f * ((5) - .5) * 2) * Math.PI) / 2) + .5;

					break;

				case 'wobble': 

					f = ( -Math.cos(f * Math.PI * (9 * f)) / 2) + .5;

					break;

				case 'sinusoidal': 

					f = ( -Math.cos(f * Math.PI) / 2) + .5;

					break;

				case 'flicker': 

					f = f + (Math.random() - .5) / 5; 
					f = RR.effects('sinusoidal', f < 0 ? 0 : f > 1 ? 1 : f);

					break;

				case 'mirror':

					if (f < .5) {

						f = RR.effects('sinusoidal', f * 2);

					}
					else {

						f = RR.effects('sinusoidal', 1 - (f - .5) * 2);				

					}

					break;

				case 'radical': 

					f = Math.sqrt(f);

					break;

				case 'harmony': 

					f = (1 + Math.sin((f - .5) * Math.PI)) / 2;

					break;  

				case 'back': 

					f = Math.pow(f, 2) * ((1.5 + 1) * f - 1.5);

					break;

				case 'expo': 

					f = Math.pow(2, 8 * (f - 1));

					break;

			}

			return f;

		},

		// Returns a color in rgba format
		getRGB: ( color ) => {

			if( color && color.length === 4 ) {

				return color;

			}

			let patterns = [

				/rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/, 
				/rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/, 
				/rgba?\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
				/rgba?\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d?(?:\.\d+)?)\s*)?\)/,
				/^hsla\((\d+),\s*([\d.]+)%,\s*([\d.]+)%,\s*(\d*(?:\.\d+)?)\)$/,
				/^hsl\((\d+),\s*([\d.]+)%,\s*([\d.]+)%\)$/,
				/#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/,
				/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/

			];

			function hsla2rgb(h, s, l) {

				function hue2rgb(p, q, t) {

					if(t < 0) {

						t += 1;

					}

					if(t > 1) {

						t -= 1;

					}

					if(t < 1 / 6) {

						return p + (q - p) * 6 * t;

					}

					if(t < 1 / 2) {

						return q;

					}

					if(t < 2 / 3) {

						return p + (q - p) * (2 / 3 - t) * 6;

					}

					return p;

				}

				const q = l < .5 ? l * (1 + s) : l + s - l * s;

				const p = 2 * l - q;

				return [parseInt(hue2rgb(p, q, h + 1 / 3) * 255), parseInt(hue2rgb(p, q, h) * 255), parseInt(hue2rgb(p, q, h - 1 / 3) * 255)];

			};

			let r;

			if(r = patterns[0].exec(color)) {

				return [parseInt(r[1]), parseInt(r[2]), parseInt(r[3]), 1];

			}

			if(r = patterns[1].exec(color)) {

				return [parseFloat(r[1]) * 2.55, parseFloat(r[2]) * 2.55, parseFloat(r[3]) * 2.55, 1];

			}

			if(r = patterns[2].exec(color)) {

				return [parseInt(r[1]), parseInt(r[2]), parseInt(r[3]), parseFloat(r[4])];

			}

			if(r = patterns[3].exec(color)) {

				return [parseFloat(r[1]) * 2.55, parseFloat(r[2]) * 2.55, parseFloat(r[3]) * 2.55, parseFloat(r[4])];

			}

			if(r = patterns[4].exec(color)) {

				return hsla2rgb( parseInt(r[1]) / 360, parseInt(r[2]) / 100, parseInt(r[3]) / 100 ).concat(parseFloat(r[4]));

			}

			if(r = patterns[5].exec(color)) {

				return hsla2rgb( parseInt(r[1]) / 360, parseInt(r[2]) / 100, parseInt(r[3]) / 100).concat(1);

			}

			if(r = patterns[6].exec(color)) {

				return [parseInt(r[1], 16), parseInt(r[2], 16), parseInt(r[3], 16), 1];

			}

			if(r = patterns[7].exec(color)) {

				return [parseInt(r[1] + r[1], 16), parseInt(r[2] + r[2], 16), parseInt(r[3] + r[3], 16), 1];

			}

			return [ 127, 127, 127, .5 ];

		},

		// Color animation helper
		colorMix: (e, p, v, t, r, callback) => {

			// v arg - color destination;
			// e arg - elem
			// p arg - propertie
			// t arg - duration
			// r arg - fx name

			// Delta interpolation for color animations
			function lerp(a, b, u) {

				return (1 - u) * a + u * b;

			}

			((s, v, c = [0, 0, 0, 0], cnt = 0) => {

				void requestAnimationFrame(

					function frame(d) {

						// g - time gone; s - start time
						let g = d - s;

						function colors(h, m) {

							return parseInt(

								lerp( h, m, g / t )

							);

						}

						let f = RR.effects(r, g / t);

						e.style.setProperty(p, 'rgba('+ colors(c[0], v[0]) * f +','+ colors(c[1], v[1]) * f +','+ colors(c[2], v[2]) * f +','+ parseFloat(lerp( v[3], c[3], g / t )) * f +')');

						// Is animation time over? if not do next frame
						if( d < t ) {

							requestAnimationFrame(frame);

						} 
						else {

							e.style.setProperty(p, 'rgba('+ v[0] +','+ v[1] +','+ v[2] +','+ v[3] +')');

							if( callback && cnt < 1 )  {

								callback.call(e); 

								cnt++;

							}

						}

					}

				);

			})(performance.now(), RR.getRGB(v), RR.getRGB(RR.styleGet(e, p)));

		},

		// Connect script
		externalJS: (s) => {

			RR.new('script', 'head', 'after', {

				attr: {

					defer: 'defer',
					async: 'async',
					src: s

				}

			});

		},

		// Attributes API helper
		attr: (e, x) => {

			var e = RR.isO(e) ? [e] : RR.sel(e); 

			if( e ) {

				let c = 0;

				for(let i of e) {

					if(RR.isO(x)) {

						for(let b in x) {

							if(x[b] === null) {

								i.removeAttribute(b);

							} 
							else {

								i.setAttribute(b, x[b]);

							}

						}

					}

					if(RR.isS(x)) {
						
						var q = RR.arguments(x, ',');
						var p = [];
						
						for(let w in q) {

							p[c++] = i.getAttribute(q[w]);

						}

					}

				}; 

				if(RR.isS(x)) {

					return p;

				}

			}

		},

		// Replace some element to another element
		replace: (e, w) => {

			var e = RR.htmlObj(e);

			for(let i of e) {

				i.parentElement.replaceChild( !RR.isO(w) ? RR.convertSTRToHTML(w)[0] : w, i);

			}

		},

		// Collect usefull HTML elements by CSS v.3 selectors syntax
		sel: (s) => {

			let t = RR.isO(s) ? s : document.querySelectorAll(s);

			if( t.length ) {

				return [...RR.filterHTML(t)];

			} 
			else {

				return null;

			}

		},

		// Create and insert new HTML elements 
		// contains nodes or text in document with attributes
		new: (e, where, how, p) => {

			let t = RR.treeHacks( RR.sel(where) );
			let n = RR.that.createElement(e);
			let h = how.split(':');

			if( p.attr ) {

				RR.attr(n, p.attr);

			}

			n.innerHTML = p.html ? p.html : '';

			switch( h[0] ) {

				case 'before':

					t.insertBefore(n, t.firstChild);

					break;

				case 'after':

					t.lastChild.outerHTML = t.lastChild.outerHTML + n.outerHTML;

					break;


				case 'fit-in':

					t.children[ h[1] - 0 ].outerHTML = n.outerHTML + t.children[ h[1] - 0 ].outerHTML;

					break;

				default: 

					RR.insert(t, n, p.html);

					break;

			}

		},

		// Insert in element html
		// or create new element with contents
		insert: (t, e, c) => {

			var t = RR.htmlObj(t);

			if( !c ) {

				for(let i in t) {

					RR.treeHacks(t[i]).innerHTML = e;

				}

			}

			if( c ) {

				t.insertBefore(e, null);
				e.innerHTML = c;

			}

		},

		// Remove elements from document
		rem: (e) => {

			for(let i of RR.htmlObj(e)) { 

				i.remove();

			}

		},

		// Wrap elements
		wrap: (e, w) => {

			var w = document.createElement(w);

			for(let i of RR.htmlObj(e)) {

				i.parentElement.insertBefore(w, i);
				w.appendChild(i);

			}

		},

		// Unwrap elements
		unwrap: (e) => {

			for(let i of RR.htmlObj(e)) {

				let parent = i.parentElement;

				while (i.firstChild) {

					parent.insertBefore(i.firstChild, i);

				}

				parent.removeChild(i);

			}

		},

		// Local storage API simplifier
		storage: (p, m) => {

			let args = [];

			switch( m ) {

				case 'set':

					if( RR.isS(p) ) {

						args.push(RR.arguments(p, '='));

					}

					if( RR.isO(p) && p.length > 0) {

						for(let i in p) {

							args[i] = RR.arguments(p[i], '=');

						}

					}

					for(let i in args) {

						localStorage.setItem(args[i][0].trim(), args[i][1].trim());

					};

				break;

				case 'get':

					if(RR.isS(p)) {

						return localStorage.getItem(p.trim());

					}

				break;

				case 'rem':

					if(RR.isS(p)) {

						localStorage.removeItem(p.trim());

					}

					if(RR.isO(p)) {

						for(let i in p) { 

							localStorage.removeItem(p[i].trim());

						}

					}

				break;

			}

		},

		// Some HTML collections returned by .class selector have array look
		// others like #id have not includings and looks like single element
		// this method helps to align it to plain
		treeHacks: (e) => (e[0]) ? e[0] : e,

		// Test for HTML objects are equal
		equality: (a, b) => a.offsetLeft === b.offsetLeft && a.offsetTop === b.offsetTop && a.outerHTML === b.outerHTML ? true : null,

		// Filter for HTML objects only in Node Lists and are useful
		filterHTML: function * f(n) {

			for( let i of n ) {

				if(
					i.nodeName !== '#comment' &&
					i.nodeName !== '#text' && 
					!RR.isUseless(i) &&
					!RR.isN(i) && 
					!RR.isU(i) && 
					!RR.isS(i) && 
					!RR.isF(i) &&
					!RR.isA(i)

				) {

					if( !!i ) {

						yield i;

					}

				}

			};

		},

		// Convert string contains HTML to DOM Object
		convertSTRToHTML: (html) => {

			let sandbox = RR.that.createElement('div');
			let shadows = [];

			sandbox.innerHTML = html;

			for(let i of sandbox.children) {

				if (i.tagName !== 'SCRIPT') {

					shadows.push(i);

				}

			}

			return [...RR.filterHTML(shadows)];

		},

		// Grep inner HTML inside some tag
		findElementFromHTMLString: (s, d) => RR.convertSTRToHTML(d)[0].querySelectorAll(s),

		// Get letters by index
		// 0 - first letter
		letter: (lt, i = i - 0) => !isNaN(i) ? lt[i] : null,

		// Parse modules API arguments
		arguments: (a, d) => {

			let args = a.split(d+ '');

			for(let i of args) {

				i = i.trim();

			}

			return args;

		},

		// This helper returns normalized 
		// for browser style name
		// example: border-left -> borderLeft
		normalizeStyleName: (s) => {

			var s = RR.arguments(s, '-');
			let r = '';

			for(let z in s) {

				if(z >= 1) {

					s[z] = RR.letter(s[z], 0).toUpperCase() + s[z].slice(1);

				}

				r += s[z];

			}

			return r;

		},

		// Get offset positions from style name
		normalizeStyleNameOffset: (e, p) => {

			let r = 'offset' + RR.letter(p, 0).toUpperCase() + p.slice(1);

			if(!RR.isU(e[r])) {

				return e[r];

			}

		},

		// Returns metrics and value of given CSS propertie
		numberCSS: (v) => {

			let u = [

				'Q', 
				'cap', 
				'ch', 
				'ic', 
				'lh', 
				'rlh', 
				'px', 
				'ex', 
				'em', 
				'%', 
				'in', 
				'cm', 
				'mm', 
				'pt', 
				'pc', 
				'deg', 
				'vmax', 
				'vmin', 
				'vh', 
				'vw', 
				'vi', 
				'vb', 
				'rem', 
				'ch', 
				'rad', 
				'grad', 
				'turn', 
				'dppx', 
				'x', 
				'dpcm', 
				'dpi', 
				'khz', 
				'hz', 
				's', 
				'ms'

			];

			let c = 0;

			for (let i of u) {

				if( v.includes(i) ) {

					return [v.replace(i, '') - 0, i];

				}
				else {

					if(c++ === 34) {

						return [v - 0, null];

					}

				}

			}

		},

		// CSS parameters shorthands helper
		shortToFull: function * f(e, p) {

			let isShort = null;

			let isTransformShort = null;

			let n = p[0] + '';
			let w = p[1];

			var p = RR.arguments( p[1].replace(/\(.*?\)/g, s => s.replace(/\s+/g,'')  ), ' ');
			
			let j = p;
			let q = 0;

			// collection of shorthands posible properties
			RR.shorts   = ['border-radius', 'border-width', 'padding', 'margin', 'border-color'];
			RR.shorts2  = ['skew', 'translate', 'scale', 'rotate'];
			RR.shorts3  = ['background-position'];

			RR.fourval1 = ['TopLeft', 'TopRight', 'BottomLeft', 'BottomRight'];
			RR.fourval2 = ['Top', 'Right', 'Bottom', 'Left'];
			
			RR.fourval3 = ['X', 'Y', 'Z'];
			RR.fourval4 = ['backgroundPositionX', 'backgroundPositionY'];

			if(n === 'transform') {

				let resultstring = '';

				for(let g of p) {

					for(let k in RR.shorts2) {

						var shorten = g.split('(')[0];

						if( RR.shorts2[k] === shorten ) {

							for(let r in RR.fourval3) {

								let valueUnits = RR.arguments( RR.getValFromPropsBrackets(shorten, g)[1], ',' );

								if( valueUnits[r] ) {

									resultstring += " "+ shorten + RR.fourval3[r] +'('+ valueUnits[r] +')'+' '+ w.replace(g, ''); 

								}

							}

						}

					}

				}

				yield ['transform', resultstring, 'NaN'];

			}

			for(let y of RR.shorts) {

				if(y === n) {

					isShort = true;

				}

			}

			if( n === 'background-position' ) {

				const stack = w.split(',');

				for( let xb in stack ) {

					var pair = stack[xb].trim().split(' ');

					console.log( pair );

					switch( pair.length ) {

						case 1:

							break;

						case 2:
						case 4:

							let count = 0, st, cu, nx, cv;

							for( let ix in pair ) {

								nx = pair[(ix - 0) + 1]; 

								if( !RR.isU( nx ) ) {

									st = RR.numberCSS(nx);
									cu = RR.numberCSS(pair[ix]);

									switch( pair.length ) {

										case 2:

											if( !['left', 'right', 'top', 'bottom'].includes(pair[ix]) ) {

												console.log( pair[ix] );

												cv = (( count < 1 ) ? 'left '+ cu[0] : 'top '+ cu[0]) + cu[1];

												count++;

											}

											break;

										case 4: 

											if( ['left', 'top'].includes(pair[ix]) )  {

												cv = pair[ix] +' '+ st[0] + st[1];

											}

											if( pair[ix] === 'right' ) {

												cv = 'left '+ -st[0] + st[1];

											}

											if( pair[ix] === 'bottom' ) {

												cv = 'top '+ -st[0] + st[1];

											}

											break;

									}

								}

							}

							break;

					}

				}

			}

			// Regrouping standart properties
			if( isShort ) {

				// autocomplite all values in shorthand CSS notation
				// 1 * 4 repeated value
				// (1-2) * 2 repeated value
				// (1-2-3) + 2 to 4

				switch( p.length - 0 ) {

					case 1: 

						p.push(p[0], p[0], p[0]);

						break;

					case 2: 

						p.push(p[0], p[1]);

						break;

					case 3: 

						p.push(p[1]);

						break;

				}

				// get computed values for all four longhand properties
				if(p.length === 4) {

					// expand full property definition from shorthand
					for(let y in RR.shorts) {

						if(RR.shorts[y] === n) {

							var xt = RR.arguments(n, '-');
							var fg, df;

							if( n == 'border-radius' ) {

								fg = RR.fourval1;
								df = null;

							}


							if( ['padding', 'margin', 'border-width'].includes(n) ) {

								fg = RR.fourval2;
								df = 1;

							}

							if( n == 'border-color' ) {

								df = null;
								fg = RR.fourval2;

							}

							for(let s in fg) {

								// style, destination, units							
								yield [ (!df) ? RR.normalizeStyleName(xt[0] +'-'+ fg[q] +'-'+ xt[1]) : RR.normalizeStyleName(xt[0] +'-'+ fg[q]), RR.numberCSS(p[q])[0], RR.numberCSS(p[q])[1] ];

								q++;

							}

						}

					}

				}

			}

			// Complite to normal values
			if( !isShort ) {

				yield [RR.normalizeStyleName(n), RR.numberCSS(j[0])[0], RR.numberCSS(j[0])[1]];

			}

		},

		// Callback future
		callback: (e, c, args) => {

			if(RR.isC(c)) {

				RR.isA(args) ? c.call(e, args) : c.call(e);

			}

		},

		// Some cryptography
		hash: (str, m, c = null) => {

			function hash (str) {

				TextEncoder = function TextEncoder() { 

				};

				TextEncoder.prototype.encode = (s) => {

					const e = new Uint8Array(s.length);

					for ( let i = 0; i < s.length; i++ ) {

						e[i] = s.charCodeAt(i);

					}

					return e;

				};

				switch( m ) {

					case 256:
					case 384:
					case 512:

						return crypto.subtle.digest('SHA-'+ m, new TextEncoder().encode(str));

						break;

				}

			}

			function encode64( f ) {

				return btoa(new Uint8Array(f).reduce((s, b) => s + String.fromCharCode(b), ''));

			}

			hash(str).then( h => {

				c.call( encode64(h), h );

			});

		},

		// Multilaguage support of BTOA
		utoa: (s) => btoa(encodeURIComponent(s)),

		// Multilaguage support of ATOB
		atou: (s) => decodeURIComponent(self.atob(s)),

		// Strip string to number
		stripNum: (v) => +v.replace(/\D+/g, '') - 0,

		// Check of object is type of html object
		htmlObj: (e) => RR.isO(e) ? e : R.sel(e),

		// Isset 
		isset: (v) => !RR.isU(v) && v !== null ? true : null,

		// Is useless :: make possible to prevent useless such as injecting the trash into backend POST data
		isUseless: (t) => ['item', 'keys', 'values', 'entries', 'forEach', undefined].includes( t ) ? true : null,

		// Is callback
		isC: (c) => c && RR.isF(c) ? true : null,

		// Is object
		isO: (v) => typeof(v) === 'object' ? true : null,

		// Is string
		isS: (v) => typeof(v) === 'string' ? true : null,

		// Is array
		isA: (v) => typeof(v) === 'array' ? true : null,

		// Is function
		isF: (v) => typeof(v) === 'function' ? true : null,

		// Is undefined
		isU: (v) => typeof(v) === 'undefined' ? true : null,

		// Is number
		isN: (v) => typeof(v) === 'number' ? true : null

	};

})();

// Make RevolveR Interface Instance
const R_CMF_i = [

	class RevolveR {

		constructor( nspace ) {

			'use strict';

			const xSytemCfg = '........'+
			'..........................'+
			'c2VsZ.i5yZWxhdG.lvbnMgPSBb'+
			'J3.ZvbHVtZTogMTU.lJywgJ25v'+
			'aXNpb.mVzczogOC.UnLCAnc2Vu'+
			'c2U6IDI.1JScsICd.jYXB0dXJl'+
			'OiBo.aWdoLW1vZ.GVyYXRlJywg'+
			'J3Jh.dGU6IG5vcm1h.bCcsICdu'+
			'.YXR1cmU6IGZ1bm55Jyw.gJ3Zp'+
			'c2lvb.jogaGlnaC1tb2RlcmF0.'+
			'ZScsICdjb25u.ZWN0aXZ.pdHk6'+
			'IG1vZG.VyYXR.lJywgJ29yaWVu'+
			'dGF0a..W9uOiBoZXRlcm8nLCAn'+
			'dG9udXM6I.GFib3.ZlJywgJ2Fn'+
			'ZTogMTItNTAnL.CAnZW.1wYXRo'+
			'eTogbG9.3Jywg.J2RlY2VuY3k6'+
			'IH.RydWUnXTs..............'+
			'=.........................';

			// Set execution context
			const cntext = self;

			// Some Design
			const a = '❤️';
			const z = '🚫';

			// Define loader
			const loader = 

				( typeof R_CMF_i[ 1 ] === 'undefined' || cntext.self !== cntext.top ) ?	
				( x = z +' Senses allready not there ...' ) => x : 
				( o = 1 ) => {

					eval(

						'self.'+ nspace +' = new Proxy( RR, {} ); '+ atob( 

							xSytemCfg.replace(/[\.]/g, '') 

						)

					);

					let steps = [];

					// [ Get browser info & autostart some window features ]
					steps.push('self.'+ nspace +'.browser;');

					// [ Set default postion for indicators ]
					steps.push('self.'+ nspace +'.screenPosition(null, null, null);');

					// [ Launch it ]
					steps.push('self.'+ nspace +'.launch = true;');

					// [ Block more than one instance ]
					steps.push('delete self.'+ nspace +'.launch;');

					steps.push('delete R_CMF_i[ o - 0 ]');

					// [ Execute step by step ]
					for( let i of steps ) {

						eval( "'use strict;'\n" + i );

					}

					// [ Console Message ]
					return a +' Senses here ... [ domain: '+ document.location.hostname +' ]';

				};

				// Initialize RevolveR interface
				console.log( 

					loader()

				);

		};

	},

	// TODO :: make test for domain is allowed to activate senses
	document.location.hostname

];
