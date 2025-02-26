/**
 * Javascript utilities
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

/* global jQuery, PLANTY_STORAGE */

/* Global variables manipulations
---------------------------------------------------------------- */

(function(){
	"use strict";

	var $window   = jQuery( window ),
		$document = jQuery( document ),
		$adminbar = jQuery( '#wpadminbar' ),
		$body     = jQuery( 'body' );

	// Global variables storage
	if (typeof PLANTY_STORAGE == 'undefined') {
		window.PLANTY_STORAGE = {};
	}

	// Get global variable
	window.planty_storage_get = function(var_name) {
		return planty_isset( PLANTY_STORAGE[var_name] ) ? PLANTY_STORAGE[var_name] : '';
	};

	// Set global variable
	window.planty_storage_set = function(var_name, value) {
		PLANTY_STORAGE[var_name] = value;
	};

	// Inc/Dec global variable with specified value
	window.planty_storage_inc = function(var_name) {
		var value                  = arguments[1] === undefined ? 1 : arguments[1];
		PLANTY_STORAGE[var_name] += value;
	};

	// Concatenate global variable with specified value
	window.planty_storage_concat = function(var_name, value) {
		PLANTY_STORAGE[var_name] += '' + value;
	};

	// Get global array element
	window.planty_storage_get_array = function(var_name, key) {
		return planty_isset( PLANTY_STORAGE[var_name][key] ) ? PLANTY_STORAGE[var_name][key] : '';
	};

	// Set global array element
	window.planty_storage_set_array = function(var_name, key, value) {
		if ( ! planty_isset( PLANTY_STORAGE[var_name] )) {
			PLANTY_STORAGE[var_name] = {};
		}
		PLANTY_STORAGE[var_name][key] = value;
	};

	// Inc/Dec global array element with specified value
	window.planty_storage_inc_array = function(var_name, key) {
		var value                       = arguments[2] === undefined ? 1 : arguments[2];
		PLANTY_STORAGE[var_name][key] += value;
	};

	// Concatenate global array element with specified value
	window.planty_storage_concat_array = function(var_name, key, value) {
		PLANTY_STORAGE[var_name][key] += '' + value;
	};

	/* PHP-style functions
	---------------------------------------------------------------- */
	window.planty_isset = function(obj) {
		return typeof(obj) != 'undefined';
	};

	window.planty_empty = function(obj) {
		return typeof(obj) == 'undefined' || (typeof(obj) == 'object' && obj === null) || (typeof(obj) == 'array' && obj.length === 0) || (typeof(obj) == 'string' && planty_alltrim( obj ) === '') || obj === 0;
	};

	window.planty_is_array = function(obj)  {
		return typeof(obj) == 'array';
	};

	window.planty_is_object = function(obj)  {
		return typeof(obj) == 'object';
	};

	window.planty_clone_object = function(obj) {
		if (obj === null || typeof(obj) != 'object') {
			return obj;
		}
		var temp = {};
		for (var key in obj) {
			temp[key] = planty_clone_object( obj[key] );
		}
		return temp;
	};

	window.planty_merge_objects = function(obj1, obj2)  {
		for (var i in obj2) {
			obj1[i] = obj2[i];
		}
		return obj1;
	};

	window.planty_array_merge = function(a1, a2) {
		for (var i in a2) {
			a1[i] = a2[i];
		}
		return a1;
	};

	window.planty_array_first_key = function(arr) {
		var rez = null;
		for (var i in arr) {
			rez = i;
			break;
		}
		return rez;
	};

	window.planty_array_first_value = function(arr) {
		var rez = null;
		for (var i in arr) {
			rez = arr[i];
			break;
		}
		return rez;
	};

	// Generates a storable representation of a value
	window.planty_serialize = function(mixed_val) {
		var obj_to_array = arguments.length == 1 || argument[1] === true;

		switch (typeof(mixed_val)) {

			case "number":
				if (isNaN( mixed_val ) || ! isFinite( mixed_val )) {
					return false;
				} else {
					return (Math.floor( mixed_val ) == mixed_val ? "i" : "d") + ":" + mixed_val + ";";
				}

			case "string":
				return "s:" + mixed_val.length + ":\"" + mixed_val + "\";";

			case "boolean":
				return "b:" + (mixed_val ? "1" : "0") + ";";

			case "object":
				if (mixed_val == null) {
					return "N;";
				} else if (mixed_val instanceof Array) {
					var idxobj = { idx: -1 };
					var map    = [];
					for (var i = 0; i < mixed_val.length; i++) {
						idxobj.idx++;
						var ser = planty_serialize( mixed_val[i] );
						if (ser) {
							map.push( planty_serialize( idxobj.idx ) + ser );
						}
					}
					return "a:" + mixed_val.length + ":{" + map.join( "" ) + "}";
				} else {
					var class_name = planty_get_class( mixed_val );
					if (class_name == undefined) {
						return false;
					}
					var props = new Array();
					for (var prop in mixed_val) {
						var ser = planty_serialize( mixed_val[prop] );
						if (ser) {
							props.push( planty_serialize( prop ) + ser );
						}
					}
					if (obj_to_array) {
						return "a:" + props.length + ":{" + props.join( "" ) + "}";
					} else {
						return "O:" + class_name.length + ":\"" + class_name + "\":" + props.length + ":{" + props.join( "" ) + "}";
					}
				}

			case "undefined":
				return "N;";
		}
		return false;
	};

	// Encode / Decode Unicode string to / from single-byte characters
	( function( $ ) {
		var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
		a256 = '',
		r64 = [256],
		r256 = [256],
		i = 0;

		var UTF8 = {

			/**
			 * Encode multi-byte Unicode string into utf-8 multiple single-byte characters
			 * (BMP / basic multilingual plane only)
			 *
			 * Chars in range U+0080 - U+07FF are encoded in 2 chars, U+0800 - U+FFFF in 3 chars
			 *
			 * @param {String} strUni Unicode string to be encoded as UTF-8
			 * @returns {String} encoded string
			 */
			encode: function(strUni) {
				// use regular expressions & String.replace callback function for better efficiency than procedural approaches
				var strUtf = strUni
								.replace(
									/[\u0080-\u07ff]/g, // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
									function(c) {
										var cc = c.charCodeAt(0);
										return String.fromCharCode(0xc0 | cc >> 6, 0x80 | cc & 0x3f);
									}
								)
								.replace(
									/[\u0800-\uffff]/g, // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
									function(c) {
										var cc = c.charCodeAt(0);
										return String.fromCharCode(0xe0 | cc >> 12, 0x80 | cc >> 6 & 0x3F, 0x80 | cc & 0x3f);
									}
								);
				return strUtf;
			},

			/**
			* Decode utf-8 encoded string back into multi-byte Unicode characters
			*
			* @param {String} strUtf UTF-8 string to be decoded back to Unicode
			* @returns {String} decoded string
			*/
			decode: function(strUtf) {
				// note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
				var strUni = strUtf
								.replace(
									/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, // 3-byte chars
									function(c) { // (note parentheses for precence)
										var cc = ((c.charCodeAt(0) & 0x0f) << 12) | ((c.charCodeAt(1) & 0x3f) << 6) | (c.charCodeAt(2) & 0x3f);
										return String.fromCharCode(cc);
									}
								)
								.replace(
									/[\u00c0-\u00df][\u0080-\u00bf]/g, // 2-byte chars
									function(c) { // (note parentheses for precence)
										var cc = (c.charCodeAt(0) & 0x1f) << 6 | c.charCodeAt(1) & 0x3f;
										return String.fromCharCode(cc);
									}
								);
				return strUni;
			}
		};

		while( i < 256 ) {
			var c = String.fromCharCode(i);
			a256 += c;
			r256[i] = i;
			r64[i] = b64.indexOf(c);
			++i;
		}

		function code(s, discard, alpha, beta, w1, w2) {
			s = String(s);
			var buffer = 0,
				i = 0,
				length = s.length,
				result = '',
				bitsInBuffer = 0;

			while (i < length) {
				var c = s.charCodeAt(i);
				c = c < 256 ? alpha[c] : -1;

				buffer = (buffer << w1) + c;
				bitsInBuffer += w1;

				while (bitsInBuffer >= w2) {
					bitsInBuffer -= w2;
					var tmp = buffer >> bitsInBuffer;
					result += beta.charAt(tmp);
					buffer ^= tmp << bitsInBuffer;
				}
				++i;
			}
			if ( ! discard && bitsInBuffer > 0) {
				result += beta.charAt(buffer << (w2 - bitsInBuffer));
			}
			return result;
		}

		var Plugin = $.planty_encoder = function(dir, input, encode) {
			return input ? Plugin[dir](input, encode) : dir ? null : this;
		};

		Plugin.btoa = Plugin.encode = function(plain, utf8encode) {
			plain = Plugin.raw === false || Plugin.utf8encode || utf8encode
						? UTF8.encode(plain)
						: plain;
			plain = code(plain, false, r256, b64, 8, 6);
			return plain + '===='.slice((plain.length % 4) || 4);
		};

		Plugin.atob = Plugin.decode = function(coded, utf8decode) {
			coded = String(coded).split('=');
			var i = coded.length;
			do {
				--i;
				coded[i] = code(coded[i], true, r64, a256, 6, 8);
			} while (i > 0);
			coded = coded.join('');
			return Plugin.raw === false || Plugin.utf8decode || utf8decode
					? UTF8.decode(coded)
					: coded;
		};
	}(jQuery) );

	// Returns the name of the class of an object
	window.planty_get_class = function(obj) {
		if (obj instanceof Object && ! (obj instanceof Array) && ! (obj instanceof Function) && obj.constructor) {
			var arr = obj.constructor.toString().match( /function\s*(\w+)/ );
			if (arr && arr.length == 2) {
				return arr[1];
			}
		}
		return false;
	};


	/* Timing functions
	---------------------------------------------------------------- */
	window.planty_debounce = function(func, wait) {
		var timeout;
		return function () {
			var context = this, args = arguments;
			var later = function later() {
				timeout = null;
				func.apply(context, args);
			};
			var callNow = !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) {
				func.apply(context, args);
			}
		};
	};

	window.planty_throttle = function(func, wait, debounce) {
		var timeout;
		return function () {
			var context = this, args = arguments;
			var throttler = function () {
				timeout = null;
				func.apply(context, args);
			};
			if (debounce) clearTimeout(timeout);
			if (debounce || !timeout) timeout = setTimeout(throttler, wait);
		};
	};


	/* Mutation observers
	---------------------------------------------------------------- */
	var planty_observers = {};

	// Create mutations observer
	window.planty_create_observer = function( id, obj, callback, args ) {
		if ( typeof window.trx_addons_create_observer != 'undefined' ) {
			return trx_addons_create_observer( id, obj, callback, args );
		} else if ( typeof window.MutationObserver !== 'undefined' && obj.length > 0 ) {
			if ( typeof planty_observers[ id ] == 'undefined' ) {
				var defa = {
						attributes: false,
						childList: true,
						subtree: true
					};
				if ( args ) {
					defa = planty_object_merge( defa, args );
				}
				planty_observers[ id ] = {
					observer: new MutationObserver( callback ),
					obj: obj.get(0)
				};
				planty_observers[ id ].observer.observe( planty_observers[ id ].obj, defa );
			}
			return true;
		}
		return false;
	};

	// Remove mutations observer
	window.planty_remove_observer = function( id ) {
		if ( typeof window.trx_addons_remove_observer != 'undefined' ) {
			return trx_addons_remove_observer( id );
		} else if ( typeof window.MutationObserver !== 'undefined' ) {
			if ( typeof planty_observers[ id ] !== 'undefined' ) {
				planty_observers[ id ].observer.disconnect(
					planty_observers[ id ].obj
				);
				delete planty_observers[ id ];
			}
			return true;
		}
		return false;
	};


	/* Wordpress-style functions
	---------------------------------------------------------------- */

	var filters = {};

	// Add filter's handler
	window.planty_add_filter = function( filter, callback, priority ) {
		if ( typeof window.trx_addons_add_filter != 'undefined' ) {
			trx_addons_add_filter( filter, callback, priority );
		} else if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' ) {
			wp.hooks.addFilter( filter, 'planty', callback, priority == undefined ? 10 : priority );
		} else {
			if ( ! filters[filter] ) filters[filter] = {};
			if ( ! filters[filter][priority] ) filters[filter][priority] = [];
			filters[filter][priority].push( callback );
		}
	};

	// Apply filter's handlers
	window.planty_apply_filters = function( filter, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 ) {
		if ( typeof window.trx_addons_apply_filters != 'undefined' ) {
			arg1 = trx_addons_apply_filters( filter, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 );
		} else if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' && typeof wp.hooks.applyFilters != 'undefined' ) {
			arg1 = wp.hooks.applyFilters( filter, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 );
		} else if ( typeof filters[filter] == 'object' ) {
			var keys = Object.keys(filters[filter]).sort();
			for (var i=0; i < keys.length; i++ ) {
				for (var j=0; j < filters[filter][keys[i]].length; j++ ) {
					if ( typeof filters[filter][keys[i]][j] == 'function' ) {
						arg1 = filters[filter][keys[i]][j](arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9);
					}
				}
			}
		}
		return arg1;
	};

	// Add action's handler
	window.planty_add_action = function( action, callback, priority ) {
		if ( typeof window.trx_addons_add_action != 'undefined' ) {
			trx_addons_add_action( action, callback, priority );
		} else if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' ) {
			wp.hooks.addAction( action, 'planty', callback, priority == undefined ? 10 : priority );
		} else {
			planty_add_filter( action, callback, priority );
		}
	};

	// Do action's handlers
	window.planty_do_action = function( action, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 ) {
		if ( typeof window.trx_addons_do_action != 'undefined' ) {
			trx_addons_do_action( action, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 );
		} else if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' && typeof wp.hooks.doActions != 'undefined' ) {
			wp.hooks.doActions( action, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 );
		} else {
			planty_apply_filters( action, arg1, arg2, arg3, arg4, arg5, arg6, arg7, arg8, arg9 );
		}
	};


	/* String functions
	---------------------------------------------------------------- */

	window.planty_in_list = function(str, list) {
		var delim  = arguments[2] !== undefined ? arguments[2] : '|';
		var icase  = arguments[3] !== undefined ? arguments[3] : true;
		var retval = false;
		if (icase) {
			if (typeof(str) == 'string') {
				str = str.toLowerCase();
			}
			list = list.toLowerCase();
		}
		var parts = list.split( delim );
		for (var i = 0; i < parts.length; i++) {
			if (parts[i] == str) {
				retval = true;
				break;
			}
		}
		return retval;
	};

	window.planty_alltrim = function(str) {
		var dir      = arguments[1] !== undefined ? arguments[1] : 'a';
		var rez      = '';
		var i, start = 0, end = str.length - 1;
		if (dir == 'a' || dir == 'l') {
			for (i = 0; i < str.length; i++) {
				if (str.substr( i, 1 ) != ' ') {
					start = i;
					break;
				}
			}
		}
		if (dir == 'a' || dir == 'r') {
			for (i = str.length - 1; i >= 0; i--) {
				if (str.substr( i, 1 ) != ' ') {
					end = i;
					break;
				}
			}
		}
		return str.substring( start, end + 1 );
	};

	window.planty_ltrim = function(str) {
		return planty_alltrim( str, 'l' );
	};

	window.planty_rtrim = function(str) {
		return planty_alltrim( str, 'r' );
	};

	window.planty_padl = function(str, len) {
		var ch  = arguments[2] !== undefined ? arguments[2] : ' ';
		var rez = str.substr( 0, len );
		if (rez.length < len) {
			for (var i = 0; i < len - str.length; i++) {
				rez += ch;
			}
		}
		return rez;
	};

	window.planty_padr = function(str, len) {
		var ch  = arguments[2] !== undefined ? arguments[2] : ' ';
		var rez = str.substr( 0, len );
		if (rez.length < len) {
			for (var i = 0; i < len - str.length; i++) {
				rez = ch + rez;
			}
		}
		return rez;
	};

	window.planty_padc = function(str, len) {
		var ch  = arguments[2] !== undefined ? arguments[2] : ' ';
		var rez = str.substr( 0, len );
		if (rez.length < len) {
			for (var i = 0; i < Math.floor( (len - str.length) / 2 ); i++) {
				rez = ch + rez + ch;
			}
		}
		return rez + (rez.length < len ? ch : '');
	};

	window.planty_replicate = function(str, num) {
		var rez = '';
		for (var i = 0; i < num; i++) {
			rez += str;
		}
		return rez;
	};

	window.planty_prepare_macros = function(str) {
		return str
			.replace( /\{\{/g, "<i>" )
			.replace( /\}\}/g, "</i>" )
			.replace( /\(\(/g, "<b>" )
			.replace( /\)\)/g, "</b>" )
			.replace( /\|\|/g, "<br>" );
	};


	/* Numbers functions
	---------------------------------------------------------------- */

	// Round number to specified precision.
	// For example: num=1.12345, prec=2,  rounded=1.12
	//              num=12345,   prec=-2, rounded=12300
	window.planty_round_number = function(num) {
		var precision = arguments[1] !== undefined ? arguments[1] : 0;
		var p         = Math.pow( 10, precision );
		return Math.round( num * p ) / p;
	};

	// Clear number from any characters and append it with 0 to desired precision
	// For example: num=test1.12dd, prec=3, cleared=1.120
	window.planty_clear_number = function(num) {
		var precision = arguments[1] !== undefined ? arguments[1] : 0;
		var defa      = arguments[2] !== undefined ? arguments[2] : 0;
		var res       = '';
		var decimals  = -1;
		num           = "" + num;
		if (num == "") {
			num = "" + defa;
		}
		for (var i = 0; i < num.length; i++) {
			if (decimals == 0) {
				break;
			} else if (decimals > 0) {
				decimals--;
			}
			var ch = num.substr( i,1 );
			if (ch == '.') {
				if (precision > 0) {
					res += ch;
				}
				decimals = precision;
			} else if ((ch >= 0 && ch <= 9) || (ch == '-' && i == 0)) {
				res += ch;
			}
		}
		if (precision > 0 && decimals != 0) {
			if (decimals == -1) {
				res     += '.';
				decimals = precision;
			}
			for (i = decimals; i > 0; i--) {
				res += '0';
			}
		}
		//if (isNaN(res)) res = clearNumber(defa, precision, defa);
		return res;
	};

	// Convert number from decimal to hex
	window.planty_dec2hex = function(n) {
		return Number( n ).toString( 16 );
	};

	// Convert number from hex to decimal
	window.planty_hex2dec = function(hex) {
		return parseInt( hex,16 );
	};


	/* Array manipulations
	---------------------------------------------------------------- */

	window.planty_in_array = function(val, thearray)  {
		var rez = false;
		for (var i = 0; i < thearray.length - 1; i++) {
			if (thearray[i] == val) {
				rez = true;
				break;
			}
		}
		return rez;
	};

	window.planty_sort_array = function(thearray)  {
		var caseSensitive = arguments[1] !== undefined ? arguments[1] : false;
		var tmp           = '';
		for (var x = 0; x < thearray.length - 1; x++) {
			for (var y = (x + 1); y < thearray.length; y++) {
				if (caseSensitive) {
					if (thearray[x] > thearray[y]) {
						tmp         = thearray[x];
						thearray[x] = thearray[y];
						thearray[y] = tmp;
					}
				} else {
					if (thearray[x].toLowerCase() > thearray[y].toLowerCase()) {
						tmp         = thearray[x];
						thearray[x] = thearray[y];
						thearray[y] = tmp;
					}
				}
			}
		}
		return thearray;
	};

	/* Date manipulations
	---------------------------------------------------------------- */

	// Return array[Year, Month, Day, Hours, Minutes, Seconds]
	// from string: Year[-/.]Month[-/.]Day[T ]Hours:Minutes:Seconds
	window.planty_parse_date = function(dt) {
		dt      = dt.replace( /\//g, '-' ).replace( /\./g, '-' ).replace( /T/g, ' ' ).split( '+' )[0];
		var dt2 = dt.split( ' ' );
		var d   = dt2[0].split( '-' );
		var t   = dt2[1].split( ':' );
		d.push( t[0], t[1], t[2] );
		return d;
	};

	// Return difference string between two dates
	window.planty_get_date_difference = function(dt1) {
		var dt2        = arguments[1] !== undefined ? arguments[1] : '';
		var short_date = arguments[2] !== undefined ? arguments[2] : true;
		var sec        = arguments[3] !== undefined ? arguments[3] : false;
		var a1         = planty_parse_date( dt1 );
		dt1            = Date.UTC( a1[0], a1[1], a1[2], a1[3], a1[4], a1[5] );
		if (dt2 == '') {
			dt2    = new Date();
			var a2 = [dt2.getFullYear(), dt2.getMonth() + 1, dt2.getDate(), dt2.getHours(), dt2.getMinutes(), dt2.getSeconds()];
		} else {
			var a2 = planty_parse_date( dt2 );
		}
		dt2         = Date.UTC( a2[0], a2[1], a2[2], a2[3], a2[4], a2[5] );
		var diff    = Math.round( (dt2 - dt1) / 1000 );
		var days    = Math.floor( diff / (24 * 3600) );
		diff       -= days * 24 * 3600;
		var hours   = Math.floor( diff / 3600 );
		diff       -= hours * 3600;
		var minutes = Math.floor( diff / 60 );
		diff       -= minutes * 60;
		var rez     = '';
		if (days > 0) {
			rez += (rez !== '' ? ' ' : '') + days + ' day' + (days > 1 ? 's' : '');
		}
		if (( ! short_date || rez == '') && hours > 0) {
			rez += (rez !== '' ? ' ' : '') + hours + ' hour' + (hours > 1 ? 's' : '');
		}
		if (( ! short_date || rez == '') && minutes > 0) {
			rez += (rez !== '' ? ' ' : '') + minutes + ' minute' + (minutes > 1 ? 's' : '');
		}
		if (sec || rez == '') {
			rez += rez !== '' || sec ? (' ' + diff + ' second' + (diff > 1 ? 's' : '')) : 'less then minute';
		}
		return rez;
	};

	/* Colors functions
	---------------------------------------------------------------- */

	window.planty_hex2rgb = function(hex) {
		hex = parseInt( ((hex.indexOf( '#' ) > -1) ? hex.substring( 1 ) : hex), 16 );
		return {r: hex >> 16, g: (hex & 0x00FF00) >> 8, b: (hex & 0x0000FF)};
	};

	window.planty_hex2rgba = function(hex, alpha) {
		var rgb = planty_hex2rgb( hex );
		return 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + alpha + ')';
	};

	window.planty_rgb2hex = function(color) {
		var aRGB;
		color = color.replace( /\s/g,"" ).toLowerCase();
		if (color == 'rgba(0,0,0,0)' || color == 'rgba(0%,0%,0%,0%)') {
			color = 'transparent';
		}
		if (color.indexOf( 'rgba(' ) == 0) {
			aRGB = color.match( /^rgba\((\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?)\)$/i );
		} else {
			aRGB = color.match( /^rgb\((\d{1,3}[%]?),(\d{1,3}[%]?),(\d{1,3}[%]?)\)$/i );
		}

		if (aRGB) {
			color = '';
			for (var i = 1; i <= 3; i++) {
				color += Math.round( (aRGB[i][aRGB[i].length - 1] == "%" ? 2.55 : 1) * parseInt( aRGB[i] ) ).toString( 16 ).replace( /^(.)$/,'0$1' );
			}
		} else {
			color = color.replace( /^#?([\da-f])([\da-f])([\da-f])$/i, '$1$1$2$2$3$3' );
		}
		return (color.substr( 0,1 ) != '#' ? '#' : '') + color;
	};

	window.planty_components2hex = function(r,g,b) {
		return '#' +
			Number( r ).toString( 16 ).toUpperCase().replace( /^(.)$/,'0$1' ) +
			Number( g ).toString( 16 ).toUpperCase().replace( /^(.)$/,'0$1' ) +
			Number( b ).toString( 16 ).toUpperCase().replace( /^(.)$/,'0$1' );
	};

	window.planty_rgb2components = function(color) {
		color       = planty_rgb2hex( color );
		var matches = color.match( /^#?([\dabcdef]{2})([\dabcdef]{2})([\dabcdef]{2})$/i );
		if ( ! matches) {
			return false;
		}
		for (var i = 1, rgb = new Array( 3 ); i <= 3; i++) {
			rgb[i - 1] = parseInt( matches[i],16 );
		}
		return rgb;
	};

	window.planty_hex2hsb = function(hex) {
		var h   = arguments[1] !== undefined ? arguments[1] : 0;
		var s   = arguments[2] !== undefined ? arguments[2] : 0;
		var b   = arguments[3] !== undefined ? arguments[3] : 0;
		var hsb = planty_rgb2hsb( planty_hex2rgb( hex ) );
		hsb.h   = Math.min( 359, Math.max( 0, hsb.h + h ) );
		hsb.s   = Math.min( 100, Math.max( 0, hsb.s + s ) );
		hsb.b   = Math.min( 100, Math.max( 0, hsb.b + b ) );
		return hsb;
	};

	window.planty_hsb2hex = function(hsb) {
		var rgb = planty_hsb2rgb( hsb );
		return planty_components2hex( rgb.r, rgb.g, rgb.b );
	};

	window.planty_rgb2hsb = function(rgb) {
		var hsb = {};
		hsb.b   = Math.max( Math.max( rgb.r,rgb.g ),rgb.b );
		hsb.s   = (hsb.b <= 0) ? 0 : Math.round( 100 * (hsb.b - Math.min( Math.min( rgb.r,rgb.g ),rgb.b )) / hsb.b );
		hsb.b   = Math.round( (hsb.b / 255) * 100 );
		if ((rgb.r == rgb.g) && (rgb.g == rgb.b)) {
			hsb.h = 0;
		} else if (rgb.r >= rgb.g && rgb.g >= rgb.b) {
			hsb.h = 60 * (rgb.g - rgb.b) / (rgb.r - rgb.b);
		} else if (rgb.g >= rgb.r && rgb.r >= rgb.b) {
			hsb.h = 60 + 60 * (rgb.g - rgb.r) / (rgb.g - rgb.b);
		} else if (rgb.g >= rgb.b && rgb.b >= rgb.r) {
			hsb.h = 120 + 60 * (rgb.b - rgb.r) / (rgb.g - rgb.r);
		} else if (rgb.b >= rgb.g && rgb.g >= rgb.r) {
			hsb.h = 180 + 60 * (rgb.b - rgb.g) / (rgb.b - rgb.r);
		} else if (rgb.b >= rgb.r && rgb.r >= rgb.g) {
			hsb.h = 240 + 60 * (rgb.r - rgb.g) / (rgb.b - rgb.g);
		} else if (rgb.r >= rgb.b && rgb.b >= rgb.g) {
			hsb.h = 300 + 60 * (rgb.r - rgb.b) / (rgb.r - rgb.g);
		} else {
			hsb.h = 0;
		}
		hsb.h = Math.round( hsb.h );
		return hsb;
	};

	window.planty_hsb2rgb = function(hsb) {
		var rgb = {};
		var h   = Math.round( hsb.h );
		var s   = Math.round( hsb.s * 255 / 100 );
		var v   = Math.round( hsb.b * 255 / 100 );
		if (s == 0) {
			rgb.r = rgb.g = rgb.b = v;
		} else {
			var t1 = v;
			var t2 = (255 - s) * v / 255;
			var t3 = (t1 - t2) * (h % 60) / 60;
			if (h == 360) {
				h = 0;
			}
			if (h < 60) {
				rgb.r = t1;	rgb.b = t2;   rgb.g = t2 + t3; } else if (h < 120) {
				rgb.g = t1; rgb.b = t2;	rgb.r = t1 - t3; } else if (h < 180) {
					rgb.g = t1; rgb.r = t2;	rgb.b = t2 + t3; } else if (h < 240) {
					rgb.b = t1; rgb.r = t2;	rgb.g = t1 - t3; } else if (h < 300) {
							rgb.b = t1; rgb.g = t2;	rgb.r = t2 + t3; } else if (h < 360) {
							rgb.r = t1; rgb.g = t2;	rgb.b = t1 - t3; } else {
								rgb.r = 0;  rgb.g = 0;	rgb.b = 0;	 }
		}
		return { r:Math.round( rgb.r ), g:Math.round( rgb.g ), b:Math.round( rgb.b ) };
	};

	window.planty_color_picker = function(){
		var id         = arguments[0] !== undefined ? arguments[0] : "iColorPicker" + Math.round( Math.random() * 1000 );
		var colors     = arguments[1] !== undefined ? arguments[1] :
		'#f00,#ff0,#0f0,#0ff,#00f,#f0f,#fff,#ebebeb,#e1e1e1,#d7d7d7,#cccccc,#c2c2c2,#b7b7b7,#acacac,#a0a0a0,#959595,'
		+ '#ee1d24,#fff100,#00a650,#00aeef,#2f3192,#ed008c,#898989,#7d7d7d,#707070,#626262,#555,#464646,#363636,#262626,#111,#000,'
		+ '#f7977a,#fbad82,#fdc68c,#fff799,#c6df9c,#a4d49d,#81ca9d,#7bcdc9,#6ccff7,#7ca6d8,#8293ca,#8881be,#a286bd,#bc8cbf,#f49bc1,#f5999d,'
		+ '#f16c4d,#f68e54,#fbaf5a,#fff467,#acd372,#7dc473,#39b778,#16bcb4,#00bff3,#438ccb,#5573b7,#5e5ca7,#855fa8,#a763a9,#ef6ea8,#f16d7e,'
		+ '#ee1d24,#f16522,#f7941d,#fff100,#8fc63d,#37b44a,#00a650,#00a99e,#00aeef,#0072bc,#0054a5,#2f3192,#652c91,#91278f,#ed008c,#ee105a,'
		+ '#9d0a0f,#a1410d,#a36209,#aba000,#588528,#197b30,#007236,#00736a,#0076a4,#004a80,#003370,#1d1363,#450e61,#62055f,#9e005c,#9d0039,'
		+ '#790000,#7b3000,#7c4900,#827a00,#3e6617,#045f20,#005824,#005951,#005b7e,#003562,#002056,#0c004b,#30004a,#4b0048,#7a0045,#7a0026';
		var colorsList = colors.split( ',' );
		var tbl        = '<table class="colorPickerTable"><thead>';
		for (var i = 0; i < colorsList.length; i++) {
			if (i % 16 == 0) {
				tbl += (i > 0 ? '</tr>' : '') + '<tr>';
			}
			tbl += '<td style="background-color:' + colorsList[i] + '">&nbsp;</td>';
		}
		tbl += '</tr></thead><tbody>'
			+ '<tr style="height:60px;">'
			+ '<td colspan="8" id="' + id + '_colorPreview" style="vertical-align:middle;text-align:center;border:1px solid #000;background:#fff;">'
			+ '<input style="width:55px;color:#000;border:1px solid rgb(0, 0, 0);padding:5px;background-color:#fff;font:11px Arial, Helvetica, sans-serif;" maxlength="7" />'
			+ '<a href="#" id="' + id + '_moreColors" class="iColorPicker_moreColors"></a>'
			+ '</td>'
			+ '<td colspan="8" id="' + id + '_colorOriginal" style="vertical-align:middle;text-align:center;border:1px solid #000;background:#fff;">'
			+ '<input style="width:55px;color:#000;border:1px solid rgb(0, 0, 0);padding:5px;background-color:#fff;font:11px Arial, Helvetica, sans-serif;" readonly="readonly" />'
			+ '</td>'
			+ '</tr></tbody></table>';

		jQuery( document.createElement( "div" ) )
			.attr( "id", id )
			.css( 'display','none' )
			.html( tbl )
			.appendTo( "body" )
			.addClass( "iColorPickerTable" )
			.on( 'mouseover', 'thead td', function(){
					var aaa = planty_rgb2hex( jQuery( this ).css( 'background-color' ) );
					jQuery( '#' + id + '_colorPreview' ).css( 'background',aaa );
					jQuery( '#' + id + '_colorPreview input' ).val( aaa );
				}
			)
			.on( 'keypress', '#' + id + '_colorPreview input', function(key){
					var aaa = jQuery( this ).val();
					if (aaa.length < 7 && ((key.which >= 48 && key.which <= 57) || (key.which >= 97 && key.which <= 102) || (key.which === 35 || aaa.length === 0))) {
						aaa += String.fromCharCode( key.which );
					} else if (key.which == 8 && aaa.length > 0) {
						aaa = aaa.substring( 0, aaa.length - 1 );
					} else if (key.which === 13 && (aaa.length === 4 || aaa.length === 7)) {
						var fld  = jQuery( '#' + id ).data( 'field' );
						var func = jQuery( '#' + id ).data( 'func' );
						if (func !== null && func != 'undefined') {
							func( fld, aaa );
						} else {
							fld.val( aaa ).css( 'backgroundColor', aaa ).trigger( 'change' );
						}
						jQuery( '#' + id + '_Bg' ).fadeOut( 500 );
						jQuery( '#' + id ).fadeOut( 500 );

					} else {
						key.preventDefault();
						return false;
					}
					if (aaa.substr( 0,1 ) === '#' && (aaa.length === 4 || aaa.length === 7)) {
						jQuery( '#' + id + '_colorPreview' ).css( 'background',aaa );
					}
					return true;
				}
			)
			.on( 'click', 'thead td', function(e){
					var fld  = jQuery( '#' + id ).data( 'field' );
					var func = jQuery( '#' + id ).data( 'func' );
					var aaa  = planty_rgb2hex( jQuery( this ).css( 'background-color' ) );
					if (func !== null && func != 'undefined') {
						func( fld, aaa );
					} else {
						fld.val( aaa ).css( 'backgroundColor', aaa ).trigger( 'change' );
					}
					jQuery( '#' + id + '_Bg' ).fadeOut( 500 );
					jQuery( '#' + id ).fadeOut( 500 );
					e.preventDefault();
					return false;
				}
			)
			.on( 'click', 'tbody .iColorPicker_moreColors', function(e){
					var thead = jQuery( this ).parents( 'table' ).find( 'thead' );
					var out   = '';
					if (thead.hasClass( 'more_colors' )) {
						for (var i = 0; i < colorsList.length; i++) {
							if (i % 16 == 0) {
								out += (i > 0 ? '</tr>' : '') + '<tr>';
							}
							out += '<td style="background-color:' + colorsList[i] + '">&nbsp;</td>';
						}
						thead.removeClass( 'more_colors' ).empty().html( out + '</tr>' );
						jQuery( '#' + id + '_colorPreview' ).attr( 'colspan', 8 );
						jQuery( '#' + id + '_colorOriginal' ).attr( 'colspan', 8 );
					} else {
						var rgb = [0,0,0], i = 0, j = -1;	// Set j=-1 or j=0 - show 2 different colors layouts
						while (rgb[0] < 0xF || rgb[1] < 0xF || rgb[2] < 0xF) {
							if (i % 18 === 0) {
								out += (i > 0 ? '</tr>' : '') + '<tr>';
							}
							i++;
							out    += '<td style="background-color:' + planty_components2hex( rgb[0] * 16 + rgb[0],rgb[1] * 16 + rgb[1],rgb[2] * 16 + rgb[2] ) + '">&nbsp;</td>';
							rgb[2] += 3;
							if (rgb[2] > 0xF) {
								rgb[1] += 3;
								if (rgb[1] > (j === 0 ? 6 : 0xF)) {
									rgb[0] += 3;
									if (rgb[0] > 0xF) {
										if (j === 0) {
											j      = 1;
											rgb[0] = 0;
											rgb[1] = 9;
											rgb[2] = 0;
										} else {
											break;
										}
									} else {
										rgb[1] = (j < 1 ? 0 : 9);
										rgb[2] = 0;
									}
								} else {
									rgb[2] = 0;
								}
							}
						}
						thead.addClass( 'more_colors' ).empty().html( out + '<td  style="background-color:#ffffff" colspan="8">&nbsp;</td></tr>' );
						jQuery( '#' + id + '_colorPreview' ).attr( 'colspan', 9 );
						jQuery( '#' + id + '_colorOriginal' ).attr( 'colspan', 9 );
					}
					jQuery( '#' + id + ' table.colorPickerTable thead td' )
					.css(
						{
							'width':'12px',
							'height':'14px',
							'border':'1px solid #000',
							'cursor':'pointer'
						}
					);
					e.preventDefault();
					return false;
				}
			);
		jQuery( document.createElement( "div" ) )
			.attr( "id", id + "_Bg" )
			.on( 'click', function(e) {
					jQuery( "#" + id + "_Bg" ).fadeOut( 500 );
					jQuery( "#" + id ).fadeOut( 500 );
					e.preventDefault();
					return false;
				}
			)
			.appendTo( "body" );
		jQuery( '#' + id + ' table.colorPickerTable thead td' )
			.css(
				{
					'width':'12px',
					'height':'14px',
					'border':'1px solid #000',
					'cursor':'pointer'
				}
			);
		jQuery( '#' + id + ' table.colorPickerTable' )
			.css( {'border-collapse':'collapse'} );
		jQuery( '#' + id )
			.css(
				{
					'border':'1px solid #ccc',
					'background':'#333',
					'padding':'5px',
					'color':'#fff'
				}
			);
		jQuery( '#' + id + '_colorPreview' )
			.css( {'height':'50px'} );
		return id;
	};

	window.planty_color_picker_show = function(id, fld, func) {
		if (id === null || id === '') {
			id = jQuery( '.iColorPickerTable' ).attr( 'id' );
		}
		var eICP = fld.offset();
		var w    = jQuery( '#' + id ).width();
		var h    = jQuery( '#' + id ).height();
		var l    = eICP.left + w < $window.width() - 10 ? eICP.left : $window.width() - 10 - w;
		var t    = eICP.top + fld.outerHeight() + h < $window.scrollTop() + $window.height() - 10 ? eICP.top + fld.outerHeight() : eICP.top - h - 13;
		jQuery( "#" + id )
			.data( {field: fld, func: func} )
			.css(
				{
					'top':t + "px",
					'left':l + "px",
					'position':'absolute',
					'z-index':999999
				}
			)
			.fadeIn( 500 );
		jQuery( "#" + id + "_Bg" )
			.css(
				{
					'position':'fixed',
					'z-index':999998,
					'top':0,
					'left':0,
					'width':'100%',
					'height':'100%'
				}
			)
			.fadeIn( 500 );
		var def = fld.val().substr( 0, 1 ) == '#' ? fld.val() : planty_rgb2hex( fld.css( 'backgroundColor' ) );
		jQuery( '#' + id + '_colorPreview input,#' + id + '_colorOriginal input' ).val( def );
		jQuery( '#' + id + '_colorPreview,#' + id + '_colorOriginal' ).css( 'background',def );
	};

	/* CSS functions
	---------------------------------------------------------------- */

	// Return font-family string from the font parameters
	window.planty_get_load_fonts_family_string = function( name, family ) {
		var parts = [ name ];
		if ( planty_alltrim( family ) != '' ) {
			parts = parts.concat( family.split( ',' ) );
		}
		for( var i = 0; i < parts.length; i++ ) {
			parts[ i ] = planty_alltrim( parts[ i ] );
			if ( parts[ i ].indexOf( '"' ) < 0 && parts[ i ].indexOf( ' ' ) >= 0 ) {
				parts[ i ] = '"' + parts[ i ] + '"';
			}
		}
		return parts.join(',');
	};

	// Return class by prefix from classes string
	window.planty_get_class_by_prefix = function(classes, prefix) {
		var rez = '';
		if ( classes ) {
			classes = classes.split(' ');
			for (var i=0; i < classes.length; i++) {
				if (classes[i].indexOf(prefix) >= 0) {
					rez = classes[i].replace(/[\s]+/g, '');			// Remove \t\r\n and spaces from the new class
					break;
				}
			}
		}
		return rez;
	};

	// Replace class by prefix with new value
	window.planty_chg_class_by_prefix = function(classes, prefix, new_value) {
		var chg = false;
		if ( ! classes ) classes = '';
		classes = classes.replace(/[\s]+/g, ' ').split(' ');	// Replace groups \t\r\n and spaces with the single space
		new_value = new_value.replace(/[\s]+/g, '');			// Remove \t\r\n and spaces from the new class
		if ( typeof prefix == 'string' ) {
			prefix = [prefix];
		}
		for (var i=0; i < classes.length; i++) {
			for (var j = 0; j < prefix.length; j++ ) {
				if (classes[i].indexOf( prefix[j] ) >= 0) {
					classes[i] = new_value;
					chg = true;
					break;
				}
			}
			if ( chg ) break;
		}
		if ( ! chg && new_value ) {
			if (classes.length == 1 && classes[0] === '')
				classes[0] = new_value;
			else
				classes.push( new_value );
		}
		return classes.join(' ');
	};

	// Get inline styles and add to the page styles
	window.planty_import_inline_styles = function( response ) {
		var selectors = planty_apply_filters( 'planty_filter_spa_inline_css_selectors', [
							'planty-inline-styles-inline-css',
							'trx_addons-inline-styles-inline-css',
							'woocommerce-inline-inline-css',
//							'wpgdprc-front-css-inline-css',
							'elementor-frontend-inline-css'
						] );
		var p1, p2, inline_css, $inline_css_tag;
		for ( var i = 0; i < selectors.length; i++ ) {
			p1 = response.indexOf( selectors[i] );
			if ( p1 > 0 ) {
				p1 = response.indexOf( '>', p1 ) + 1;
				p2 = response.indexOf( '</style>', p1 ),
				inline_css = response.substring( p1, p2 ),
				$inline_css_tag = jQuery( '#' + selectors[i] );
				if ( $inline_css_tag.length === 0 ) {
					$body.append( '<style id="' + selectors[i] + '" type="text/css">' + inline_css + '</style>' );
				} else {
					$inline_css_tag.append( inline_css );
				}
			}
		}
	};


	// Get tags 'link' from response and add its to the 'head'
	window.planty_import_tags_link = function( response ) {
		// Get links to css from response
		var links = response.match( /<link[^>]*rel=['"]stylesheet['"][^>]*id=['"]([^'"]+)['"][^>]*>/g );
		if ( links ) {
			// Prepend its to the HEAD (if not loaded in the current page)
			var $head = jQuery( 'head' );
			for ( var i = 0; i < links.length; i++ ) {
				var matches = links[i].match( /<link[^>]*rel=['"]stylesheet['"][^>]*id=['"]([^'"]+)['"][^>]*>/ );
				if ( matches && matches.length && matches.length > 1 && matches[1] ) {
					if ( jQuery( '#' + matches[1].replace('.', '\\.') ).length === 0 ) {
						$head.prepend( links[i] );
					}
				}
			}
		}
	};


	/* Cookies manipulations
	---------------------------------------------------------------- */

	window.planty_get_cookie = function(name) {
		var defa  = arguments[1] !== undefined ? arguments[1] : null;
		var start = document.cookie.indexOf( name + '=' );
		var len   = start + name.length + 1;
		if (( ! start) && (name != document.cookie.substring( 0, name.length ))) {
			return defa;
		}
		if (start == -1) {
			return defa;
		}
		var end = document.cookie.indexOf( ';', len );
		if (end == -1) {
			end = document.cookie.length;
		}
		return unescape( document.cookie.substring( len, end ) );
	};

	window.planty_set_cookie = function(name, value) {
		var expires  = arguments[2] !== undefined ? arguments[2] : 0;  // In ms ( sec * 1000 ). If 0 (by default) - store the value for the current session only
		var path     = arguments[3] !== undefined ? arguments[3] : '/';
		var domain   = arguments[4] !== undefined ? arguments[4] : '';
		var secure   = arguments[5] !== undefined ? arguments[5] : '';
		var samesite = arguments[6] !== undefined ? arguments[6] : 'strict';  // strict | lax | none
		var today    = new Date();
		today.setTime( today.getTime() );
		var expires_date = new Date( today.getTime() + (expires * 1) );
		document.cookie  = encodeURIComponent(name) + '='
				+ encodeURIComponent( value )
				+ (expires ? ';expires=' + expires_date.toGMTString() : '')
				+ (path ? ';path=' + path : '')
				+ (domain ? ';domain=' + domain : '')
				+ (secure ? ';secure' : '')
				+ (samesite  ? ';samesite=' + samesite : '');
	};

	window.planty_del_cookie = function(name) {
		var path     = arguments[1] !== undefined ? arguments[1] : '/';
		var domain   = arguments[2] !== undefined ? arguments[2] : '';
		var secure   = arguments[3] !== undefined ? arguments[3] : '';
		var samesite = arguments[4] !== undefined ? arguments[4] : 'strict';  // strict | lax | none
		if ( planty_get_cookie( name ) ) {
			document.cookie = name + '='
				+ ';expires=Thu, 01-Jan-1970 00:00:01 GMT'
				+ (path ? ';path=' + path : '')
				+ (domain ? ';domain=' + domain : '')
				+ (secure ? ';secure' : '')
				+ (samesite  ? ';samesite=' + samesite : '');
		}
	};

	/* Local storage manipulations
	---------------------------------------------------------------- */

	window.planty_is_local_storage_exists = function() {
		try {
			return 'localStorage' in window && window['localStorage'] !== null;
		} catch (e) {
			return false;
		}		
	};
	
	window.planty_get_storage = function(name) {
		var defa = arguments[1] !== undefined ? arguments[1] : null;
		var val = null;
		if (planty_is_local_storage_exists()) {
			val = window['localStorage'].getItem(name);
			if (val === null) val = defa;
		} else {
			val = planty_get_cookie(name, defa);
		}
		return val;
	};
	
	window.planty_set_storage = function(name, value) {
		if (planty_is_local_storage_exists())
			window['localStorage'].setItem(name, value);
		else 
			planty_set_cookie(name, value, 365 * 24 * 60 * 60 * 1000);   // 1 year
	};
	
	window.planty_del_storage = function(name) {
		if (planty_is_local_storage_exists())
			window['localStorage'].removeItem(name);
		else 
			planty_del_cookie(name);
	};
	
	window.planty_clear_storage = function() {
		if (planty_is_local_storage_exists())
			window['localStorage'].clear();
	};

	/* ListBox and ComboBox manipulations
	---------------------------------------------------------------- */

	window.planty_clear_listbox = function(box) {
		for (var i = box.options.length - 1; i >= 0; i--) {
			box.options[i] = null;
		}
	};

	window.planty_add_listbox_item = function(box, val, text) {
		var item   = new Option();
		item.value = val;
		item.text  = text;
		box.options.add( item );
	};

	window.planty_del_listbox_item_by_value = function(box, val) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].value == val) {
				box.options[i] = null;
				break;
			}
		}
	};

	window.planty_del_listbox_item_by_text = function(box, txt) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].text == txt) {
				box.options[i] = null;
				break;
			}
		}
	};

	window.planty_find_listbox_item_by_value = function(box, val) {
		var idx = -1;
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].value == val) {
				idx = i;
				break;
			}
		}
		return idx;
	};

	window.planty_find_listbox_item_by_text = function(box, txt) {
		var idx = -1;
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].text == txt) {
				idx = i;
				break;
			}
		}
		return idx;
	};

	window.planty_select_listbox_item_by_value = function(box, val) {
		for (var i = 0; i < box.options.length; i++) {
			box.options[i].selected = (val == box.options[i].value);
		}
	};

	window.planty_select_listbox_item_by_text = function(box, txt) {
		for (var i = 0; i < box.options.length; i++) {
			box.options[i].selected = (txt == box.options[i].text);
		}
	};

	window.planty_get_listbox_values = function(box) {
		var delim = arguments[1] !== undefined ? arguments[1] : ',';
		var str   = '';
		for (var i = 0; i < box.options.length; i++) {
			str += (str ? delim : '') + box.options[i].value;
		}
		return str;
	};

	window.planty_get_listbox_texts = function(box) {
		var delim = arguments[1] !== undefined ? arguments[1] : ',';
		var str   = '';
		for (var i = 0; i < box.options.length; i++) {
			str += (str ? delim : '') + box.options[i].text;
		}
		return str;
	};

	window.planty_sort_listbox = function(box)  {
		var temp_opts = new Array(),
			temp      = new Option(),
			i, x, y;
		for (i = 0; i < box.options.length; i++) {
			temp_opts[i] = box.options[i].clone();
		}
		for (x = 0; x < temp_opts.length - 1; x++) {
			for (y = (x + 1); y < temp_opts.length; y++) {
				if (temp_opts[x].text > temp_opts[y].text) {
					temp         = temp_opts[x];
					temp_opts[x] = temp_opts[y];
					temp_opts[y] = temp;
				}
			}
		}
		for (i = 0; i < box.options.length; i++) {
			box.options[i] = temp_opts[i].clone();
		}
	};

	window.planty_get_listbox_selected_index = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return i;
			}
		}
		return -1;
	};

	window.planty_get_listbox_selected_value = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i].value;
			}
		}
		return null;
	};

	window.planty_get_listbox_selected_text = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i].text;
			}
		}
		return null;
	};

	window.planty_get_listbox_selected_option = function(box) {
		for (var i = 0; i < box.options.length; i++) {
			if (box.options[i].selected) {
				return box.options[i];
			}
		}
		return null;
	};

	/* Radio buttons manipulations
	---------------------------------------------------------------- */

	window.planty_get_radio_value = function(radioGroupObj) {
		for (var i = 0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked) {
				return radioGroupObj[i].value;
			}
		}
		return null;
	};

	window.planty_set_radio_checked_by_num = function(radioGroupObj, num) {
		for (var i = 0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked && i != num) {
				radioGroupObj[i].checked = false;
			} else if (i == num) {
				radioGroupObj[i].checked = true;
			}
		}
	};

	window.planty_set_radio_checked_by_value = function(radioGroupObj, val) {
		for (var i = 0; i < radioGroupObj.length; i++) {
			if (radioGroupObj[i].checked && radioGroupObj[i].value != val) {
				radioGroupObj[i].checked = false;
			} else if (radioGroupObj[i].value == val) {
				radioGroupObj[i].checked = true;
			}
		}
	};

	/* Form manipulations
	---------------------------------------------------------------- */

	/*
	// Usage example:
	var error = planty_form_validate(jQuery(form_selector), {		// -------- Options ---------
		error_message_show: true,									// Display or not error message
		error_message_time: 5000,									// Time to display error message
		error_message_class: 'message_box message_box_error',		// Class, appended to error message block
		error_message_text: 'Global error text',					// Global error message text (if don't write message in checked field)
		error_fields_class: 'error_field',							// Class, appended to error fields
		exit_after_first_error: false,								// Cancel validation and exit after first error
		rules: [
			{
				field: 'author',																// Checking field name
				min_length: { value: 1,	 message: 'The author name can\'t be empty' },			// Min character count (0 - don't check), message - if error occurs
				max_length: { value: 60, message: 'Too long author name'}						// Max character count (0 - don't check), message - if error occurs
			},
			{
				field: 'email',
				min_length: { value: 7,	 message: 'Too short (or empty) email address' },
				max_length: { value: 60, message: 'Too long email address'},
				mask: { value: '^([a-zA-Z0-9_\\-]+\\.)*[a-zA-Z0-9_\\-]+@[a-zA-Z0-9_\\-]+(\\.[a-zA-Z0-9_\\-]+)*\\.[a-zA-Z]{2,6}$', message: 'Invalid email address'}
			},
			{
				field: 'comment',
				min_length: { value: 1,	 message: 'The comment text can\'t be empty' },
				max_length: { value: 200, message: 'Too long comment'}
			},
			{
				field: 'pwd1',
				min_length: { value: 5,	 message: 'The password can\'t be less then 5 characters' },
				max_length: { value: 20, message: 'Too long password'}
			},
			{
				field: 'pwd2',
				equal_to: { value: 'pwd1',	 message: 'The passwords in both fields must be equals' }
			}
		]
	});
	*/

	window.planty_form_validate = function(form, opt) {
		var error_msg = '';
		form.find( ":input" ).each(
			function() {
				if (error_msg !== '' && opt.exit_after_first_error) {
					return;
				}
				for (var i = 0; i < opt.rules.length; i++) {
					if (jQuery( this ).attr( "name" ) == opt.rules[i].field) {
						var val   = jQuery( this ).val();
						var error = false;
						if (typeof(opt.rules[i].min_length) == 'object') {
							if (opt.rules[i].min_length.value > 0 && val.length < opt.rules[i].min_length.value) {
								if (error_msg == '') {
									jQuery( this ).get( 0 ).focus();
								}
								error_msg += '<p class="error_item">' + (typeof(opt.rules[i].min_length.message) != 'undefined' ? opt.rules[i].min_length.message : opt.error_message_text ) + '</p>';
								error      = true;
							}
						}
						if (( ! error || ! opt.exit_after_first_error) && typeof(opt.rules[i].max_length) == 'object') {
							if (opt.rules[i].max_length.value > 0 && val.length > opt.rules[i].max_length.value) {
								if (error_msg == '') {
									jQuery( this ).get( 0 ).focus();
								}
								error_msg += '<p class="error_item">' + (typeof(opt.rules[i].max_length.message) != 'undefined' ? opt.rules[i].max_length.message : opt.error_message_text ) + '</p>';
								error      = true;
							}
						}
						if (( ! error || ! opt.exit_after_first_error) && typeof(opt.rules[i].mask) == 'object') {
							if (opt.rules[i].mask.value !== '') {
								var regexp = new RegExp( opt.rules[i].mask.value );
								if ( ! regexp.test( val )) {
									if (error_msg == '') {
										jQuery( this ).get( 0 ).focus();
									}
									error_msg += '<p class="error_item">' + (typeof(opt.rules[i].mask.message) != 'undefined' ? opt.rules[i].mask.message : opt.error_message_text ) + '</p>';
									error      = true;
								}
							}
						}
						if (( ! error || ! opt.exit_after_first_error) && typeof(opt.rules[i].state) == 'object') {
							if (opt.rules[i].state.value == 'checked' && ! jQuery( this ).get( 0 ).checked) {
								if (error_msg == '') {
									jQuery( this ).get( 0 ).focus();
								}
								error_msg += '<p class="error_item">' + (typeof(opt.rules[i].state.message) != 'undefined' ? opt.rules[i].state.message : opt.error_message_text ) + '</p>';
								error      = true;
							}
						}
						if (( ! error || ! opt.exit_after_first_error) && typeof(opt.rules[i].equal_to) == 'object') {
							if (opt.rules[i].equal_to.value !== '' && val != jQuery( jQuery( this ).get( 0 ).form[opt.rules[i].equal_to.value] ).val()) {
								if (error_msg == '') {
									jQuery( this ).get( 0 ).focus();
								}
								error_msg += '<p class="error_item">' + (typeof(opt.rules[i].equal_to.message) != 'undefined' ? opt.rules[i].equal_to.message : opt.error_message_text ) + '</p>';
								error      = true;
							}
						}
						if (opt.error_fields_class !== '') {
							jQuery( this ).toggleClass( opt.error_fields_class, error );
						}
					}
				}
			}
		);
		if (error_msg !== '' && opt.error_message_show) {
			var error_message_box = form.find( ".result" );
			if (error_message_box.length == 0) {
				error_message_box = form.parent().find( ".result" );
			}
			if (error_message_box.length == 0) {
				form.append( '<div class="result"></div>' );
				error_message_box = form.find( ".result" );
			}
			if (opt.error_message_class) {
				error_message_box.toggleClass( opt.error_message_class, true );
			}
			error_message_box.html( error_msg ).fadeIn();
			setTimeout( function() { error_message_box.fadeOut(); }, opt.error_message_time );
		}
		return error_msg !== '';
	};

	/* Document manipulations
	---------------------------------------------------------------- */

	// Animated scroll to selected id
	window.trx_addons_document_animate_to_busy = false;
	window.planty_document_animate_to = function(id) {
		var split_animation = true;
		var speed    = arguments.length > 1 ? arguments[1] : -1;
		var callback = arguments.length > 2 ? arguments[2] : undefined;
		var oft = ! isNaN( id ) ? Number( id ) : 0,
			oft2 = -1;
		var obj = null;
		if (isNaN( id )) {
			if ( typeof id == 'object' ) {
				if ( id.length > 0 ) {
					obj = id;
				} else {
					return;
				}
			} else {
				if ( id.substring(0, 1) != '#' && id.substring(0, 1) != '.' ) {
					id = '#' + id;
				}
				obj = jQuery(id).eq(0);
				if ( obj.length === 0 ) {
					return;
				}
			}
			oft = split_animation ? obj.offset().top : Math.max( 0, obj.offset().top - planty_fixed_rows_height() );
			if ( split_animation ) {
				oft2 = Math.max( 0, oft - planty_fixed_rows_height() );
			}
		}
		if (speed < 0) {
			speed = Math.min( 1000, Math.max( 300, Math.round( Math.abs( ( oft2 < 0 ? oft : oft2 ) - $window.scrollTop() ) / $window.height() * 300 ) ) );
		}
		if (oft2 >= 0) {
			setTimeout( function() {
				if (isNaN( id )) {
					oft = obj.offset().top;
				}
				oft2 = Math.max( 0, oft - planty_fixed_rows_height() );
				jQuery( 'body,html' ).stop( true ).animate( {scrollTop: oft2}, Math.floor( speed / 2 ), 'linear', function() {
					_window_scroll_top = $window.scrollTop();
					window.trx_addons_document_animate_to_busy = false;
					if ( callback ) callback( id, speed );
				} );
			}, Math.floor( speed / 2 ) );
		} else {
			oft2 = oft;
		}
		if ( speed > 0 ) {
			jQuery( 'body,html' ).stop( true ).animate( { scrollTop: oft2 }, speed, 'linear', function() {
				_window_scroll_top = $window.scrollTop();
				window.trx_addons_document_animate_to_busy = false;
				if ( callback ) callback( id, speed );
			} );
		} else {
			jQuery( 'body,html' ).stop( true ).scrollTop( oft2 );
			_window_scroll_top = $window.scrollTop();
			window.trx_addons_document_animate_to_busy = false;
			if ( callback ) callback( id, speed );
		}
	};

	// Detect window width, height and scroll
	var _window_width = $window.width(),
		_window_height = $window.height(),
		_window_scroll_top = $window.scrollTop(),
		_window_scroll_left = $window.scrollLeft();
	$window.on( 'resize', function() {
		_window_width = $window.width();
		_window_height = $window.height();
		_window_scroll_top = $window.scrollTop();
		_window_scroll_left = $window.scrollLeft();	
	} );
	$window.on( 'scroll', function() {
		_window_scroll_top = $window.scrollTop();
		_window_scroll_left = $window.scrollLeft();	
	} );
	window.planty_window_width = function( val ) {
		if ( val ) _window_width = val;
		return _window_width;
	};
	window.planty_window_height = function( val ) {
		if ( val ) _window_height = val;
		return _window_height;
	};
	window.planty_window_scroll_top = function() {
		return _window_scroll_top;
	};
	window.planty_window_scroll_left = function() {
		return _window_scroll_left;
	};

	// Detect document height
	var	_document_height;
	var _document_height_first_run = true;
	var _update_document_height = function( e ) {
		if ( typeof window.trx_addons_document_height == 'undefined' ) {
			if ( _document_height_first_run && e && e.namespace == 'init_hidden_elements' ) {
				_document_height_first_run = false;
				return; 
			}
			_document_height = $document.height();
		}
	};
	$document.ready( _update_document_height );
	$document.on( 'action.init_hidden_elements action.got_ajax_response',    // Maybe need for ' action.sc_layouts_row_fixed_on action.sc_layouts_row_fixed_off'
				_update_document_height
				);
	$window.on( 'resize', _update_document_height );
	window.planty_document_height = function() {
		return typeof window.trx_addons_document_height != 'undefined' ? trx_addons_document_height() : _document_height;
	};

	// Detect adminbar height (if present and fixed)
	var _adminbar_height = 0;
	var _update_adminbar_height = function() {
		if ( typeof window.trx_addons_adminbar_height == 'undefined' ) {
			_adminbar_height = planty_adminbar_height_calc();
			document.querySelector('html').style.setProperty( '--fixed-rows-height', _adminbar_height + 'px' );
		}
	};
	$document.ready( _update_adminbar_height );
	$window.on( 'resize', _update_adminbar_height );
	window.planty_adminbar_height_calc = function() {
		return planty_apply_filters( 'planty_filter_adminbar_height',
										$adminbar.length === 0
											|| $adminbar.css( 'display' ) == 'none'
											|| $adminbar.css( 'position' ) == 'absolute'
												? 0
												: $adminbar.height()
									);
	};
	window.planty_adminbar_height = function() {
		return typeof window.trx_addons_adminbar_height != 'undefined' ? trx_addons_adminbar_height() : _adminbar_height;
	};

	// Detect fixed rows height
	window.planty_fixed_rows_height = function() {
		var with_admin_bar  = arguments.length > 0 ? arguments[0] : true,
			with_fixed_rows = arguments.length > 1 ? arguments[1] : true;
		return typeof trx_addons_fixed_rows_height != 'undefined'
					? trx_addons_fixed_rows_height( with_admin_bar, with_fixed_rows )
					: ( with_admin_bar ? planty_adminbar_height() : 0 );
	};

	// Change browser address without reload page
	window.planty_document_set_location = function( curLoc, state ) {
		try {
			history.pushState( state ? state : { url: curLoc }, null, curLoc );
			return;
		} catch ( e ) {}
		location.href = curLoc;
	};

	// Add/Change arguments to the url address
	window.planty_add_to_url = function(loc, prm) {
		var ignore_empty = arguments[2] !== undefined ? arguments[2] : true;
		var q            = loc.indexOf( '?' );
		var attr         = {};
		if (q > 0) {
			var qq    = loc.substr( q + 1 ).split( '&' );
			var parts = '';
			for (var i = 0; i < qq.length; i++) {
				var parts      = qq[i].split( '=' );
				attr[parts[0]] = parts.length > 1 ? parts[1] : '';
			}
		}
		for (var p in prm) {
			attr[p] = prm[p];
		}
		loc   = (q > 0 ? loc.substr( 0, q ) : loc) + '?';
		var i = 0;
		for (p in attr) {
			if (ignore_empty && attr[p] == '') {
				continue;
			}
			loc += (i++ > 0 ? '&' : '') + p + '=' + attr[p];
		}
		return loc;
	};

	// Check if url is page-inner (local) link
	window.planty_is_local_link = function(url) {
		var rez = url !== undefined;
		if (rez) {
			var url_pos = url.indexOf( '#' );
			if (url_pos == 0 && url.length == 1) {
				rez = false;
			} else {
				if (url_pos < 0) {
					url_pos = url.length;
				}
				var loc     = window.location.href;
				var loc_pos = loc.indexOf( '#' );
				if (loc_pos > 0) {
					loc = loc.substring( 0, loc_pos );
				}
				rez = url_pos == 0;
				if ( ! rez) {
					rez = loc == url.substring( 0, url_pos );
				}
			}
		}
		return rez;
	};

	// Check if the specified path is url
	window.planty_is_url = function( url ) {
		return url.indexOf( '//' ) === 0 || url.indexOf( '://' ) > 0;
	};


	/* Browsers detection
	---------------------------------------------------------------- */

	window.planty_browser_is_mobile = function() {
		var check = false;
		(function(a){if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test( a ) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test( a.substr( 0,4 ) )) {
				check = true}
		})( navigator.userAgent || navigator.vendor || window.opera );
		return check;
	};

	window.planty_browser_is_ios = function( with_mac ) {
		return navigator.userAgent.match( /iPad|iPhone|iPod/i ) != null || ( with_mac && navigator.userAgent.match( /Macintosh/i ) != null );
	};

	window.planty_is_retina = function() {
		var mediaQuery = '(-webkit-min-device-pixel-ratio: 1.5), (min--moz-device-pixel-ratio: 1.5), (-o-min-device-pixel-ratio: 3/2), (min-resolution: 1.5dppx)';
		return (window.devicePixelRatio > 1) || (window.matchMedia && window.matchMedia( mediaQuery ).matches);
	};

	/* File functions
	---------------------------------------------------------------- */

	window.planty_get_file_name = function(path) {
		path    = path.replace( /\\/g, '/' );
		var pos = path.lastIndexOf( '/' );
		if (pos >= 0) {
			path = path.substr( pos + 1 );
		}
		return path;
	};

	window.planty_get_file_ext = function(path) {
		var pos = path.lastIndexOf( '.' );
		path    = pos >= 0 ? path.substr( pos + 1 ) : '';
		return path;
	};

	/* Image functions
	---------------------------------------------------------------- */

	// Return true, if all images in the specified container are loaded
	window.planty_is_images_loaded = function(cont) {
		var complete = true;
		cont.find( 'img' ).each(
			function() {
				// If any of previous images is not loaded - skip rest
				if ( ! complete) {
					return;
				}
				var img = jQuery( this ).get( 0 );
				if (typeof img.complete == 'boolean') {
					// See if "complete" property is available
					complete = img.complete;
				} else if (typeof img.naturalWidth == 'number' && typeof img.naturalHeight == 'number') {
					// See if "naturalWidth" and "naturalHeight" properties are available
					complete = ! (this.naturalWidth == 0 && this.naturalHeight == 0);
				}
			}
		);
		return complete;
	};

	// Call function when all images in the specified container are loaded
	window.planty_when_images_loaded = function(cont, callback, max_delay) {
		if (max_delay === undefined) {
			max_delay = 3000;
		}
		if (max_delay <= 0 || planty_is_images_loaded( cont )) {
			callback();
		} else {
			setTimeout(
				function(){
					planty_when_images_loaded( cont, callback, max_delay - 100 );
				}, 100
			);
		}
	};



	/* Dev shorthands
	---------------------------------------------------------------- */

	window.dcl === undefined && ( window.dcl = function(s) { console.log(s); } );
	window.dcs === undefined && ( window.dcs = function() { console.trace(); } );
	window.dcts === undefined && ( window.dcts = function( name ) { console.time( name ? name : 'timer' ); } );
	window.dctl === undefined && ( window.dctl = function( name ) { console.timeLog( name ? name : 'timer' ); } );
	window.dcte === undefined && ( window.dcte = function( name ) { console.timeEnd( name ? name : 'timer' ); } );

})();
