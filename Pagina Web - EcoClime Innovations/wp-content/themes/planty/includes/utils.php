<?php
/**
 * Theme tags and utilities
 *
 * @package PLANTY
 * @since PLANTY 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit; }



/* Arrays manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_array_get_first' ) ) {
	/**
	 * Return a first key ( by default ) or a first value from an associative array.
	 *
	 * @param array $arr  An array to return a first element.
	 * @param bool  $key  Optional. If true - return a key, else - return a value of a first item of the array.
	 *                    Default is true ( return a key ).
	 *
	 * @return mixed      A key or value of the first item of the array.
	 */
	function planty_array_get_first( &$arr, $key = true ) {
		foreach ( $arr as $k => $v ) {
			break;
		}
		return $key ? $k : $v;
	}
}

if ( ! function_exists( 'planty_array_get_keys_by_value' ) ) {
	/**
	 * Return keys by value from an associative string (categories=1|author=0|date=0|counters=1...) or array.
	 * a characters '|' and '&' can be used as a delimiter in the string between pairs key=value.
	 *
	 * @param array $arr    An array to return keys with a specified value.
	 * @param mixed $value  Optional. A value to compare. Default is 1.
	 *
	 * @return array        Keys with a specified value.
	 */
	function planty_array_get_keys_by_value( $arr, $value = 1 ) {
		if ( ! is_array( $arr ) ) {
			parse_str( str_replace( '|', '&', $arr ), $arr );
		}
		return $value != null ? array_keys( $arr, $value ) : array_keys( $arr );
	}
}

if ( ! function_exists( 'planty_array_delete_by_value' ) ) {
	/**
	 * Delete items by value from an array (any type). All entries equal to value will be removed.
	 *
	 * @param array $arr    An array to return keys with a specified value.
	 * @param mixed $value  A value to delete.
	 *
	 * @return array        A processed array without items equals to a value.
	 */
	function planty_array_delete_by_value( $arr, $value ) {
		foreach( (array)$value as $v ) {
			do {
				$key = array_search( $v, $arr );
				if ( false !== $key ) {
					unset( $arr[ $key ] );
				}
			} while ( false !== $key );
		}
		return $arr;
	}
}

if ( ! function_exists( 'planty_array_from_list' ) ) {
	/**
	 * Convert a list to the associative array (use values as keys).
	 *
	 * @param array $arr    An array to convert. For example: array( 1, 2, 3 )
	 *
	 * @return array        A converted array. For example: array( 1 => 1, 2 => 2, 3 => 3 )
	 */
	function planty_array_from_list( $arr ) {
		$new = array();
		foreach ( $arr as $v ) {
			$new[ $v ] = $v;
		}
		return $new;
	}
}

if ( ! function_exists( 'planty_array_slice' ) ) {
	/**
	 * Return a part of the array from key = $from to key = $to ( elements $from and $to can be included or not ).
	 *
	 * @param array  $arr   An array to copy elements.
	 * @param string $from  A key of the start element. If key starts with the character '+'
	 *                      - this element must be included to result.
	 * @param string $to    Optional. A key of the end element. If key starts with the character '+'
	 *                      - this element must be included to result. If this argument is omitted or equal to the
	 *                      empty string - an array part starts from $from and to the end of the array will be returned.
	 *
	 * @return array        A part of the original array between keys $from and $to.
	 */
	function planty_array_slice( $arr, $from, $to='' ) {
		if ( is_array( $arr ) && count( $arr ) > 0 && ( ! empty( $from ) || ! empty( $to ) ) ) {
			$arr_new  = array();
			$copy     = empty( $from );
			$from_inc = false;
			$to_inc   = false;
			if ( substr( $from, 0, 1) == '+' ) {
				$from_inc = true;
				$from     = substr( $from, 1 );
			}
			if ( substr( $to, 0, 1) == '+' ) {
				$to_inc = true;
				$to     = substr( $to, 1 );
			}
			foreach ( $arr as $k => $v ) {
				if ( ! empty( $from ) && $k == $from ) {
					$copy = true;
					if ( ! $from_inc ) {
						continue;
					}
				}
				if ( ! empty( $to ) && $k == $to ) {
					if ( $copy && $to_inc ) {
						$arr_new[ $k ] = $v;
					}
					break;
				}
				if ( $copy ) {
					$arr_new[ $k ] = $v;
				}
			}
			$arr = $arr_new;
		}
		return $arr;
	}
}

if ( ! function_exists( 'planty_array_merge' ) ) {
	/**
	 * Merge arrays and lists (preserve a numeric indexes).
	 *
	 * For example:
	 *
	 * planty_array_merge( [ 1, 2, 3, 4 ], [ 'one', 'two' ] )  // result: [ 'one', 'two', 3, 4 ]
	 *
	 * planty_array_merge( [ 'a' => 'A', 'b' => 'B' ], [ 'one', 'two', 'a' => 'AA' ] )  // result: [ 'a' => 'AA', 'b' => 'B', 0 => 'one', 1 => 'two' ]
	 *
	 * @param array  $a1   A first array (or a list) to be merged.
	 * @param array  $a2   A second array (or a list) to be merged.
	 *
	 * @return array       A merged array.
	 */
	function planty_array_merge( $a1, $a2 ) {
		for ( $i = 1; $i < func_num_args(); $i++ ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) && count( $arg ) > 0 ) {
				foreach ( $arg as $k => $v ) {
					$a1[ $k ] = $v;
				}
			}
		}
		return $a1;
	}
}

if ( ! function_exists( 'planty_array_insert_after' ) ) {
	/**
	 * Inserts any number of scalars or arrays at the point in the haystack
	 * immediately after the search key ($needle) was found, or at the end if the needle is not found or not supplied.
	 * Modifies $haystack in place.
	 *
	 * @param array  &$haystack  The associative array to search. This will be modified by the function.
	 * @param string $needle     The key to search for.
	 * @param mixed  $stuff      One or more arrays or scalars to be inserted into $haystack.
	 *
	 * @return int               The index at which $needle was found.
	 */
	function planty_array_insert_after( &$haystack, $needle, $stuff ) {
		if ( ! is_array( $haystack ) ) {
			return -1;
		}

		$new_array = array();
		for ( $i = 2; $i < func_num_args(); ++$i ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) ) {
				if ( 2 == $i ) {
					$new_array = $arg;
				} else {
					$new_array = planty_array_merge( $new_array, $arg );
				}
			} else {
				$new_array[] = $arg;
			}
		}

		$i = 0;
		if ( is_array( $haystack ) && count( $haystack ) > 0 ) {
			foreach ( $haystack as $key => $value ) {
				$i++;
				if ( $key == $needle ) {
					break;
				}
			}
		}

		$haystack = is_int( $needle )
						? array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) )
						: planty_array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) );

		return $i;
	}
}

if ( ! function_exists( 'planty_array_insert_before' ) ) {
	/**
	 * Inserts any number of scalars or arrays at the point in the haystack
	 * immediately before the search key ($needle) was found, or at the end if the needle is not found or not supplied.
	 * Modifies $haystack in place.
	 *
	 * @param array  &$haystack  The associative array to search. This will be modified by the function.
	 * @param string $needle     The key to search for.
	 * @param mixed  $stuff      One or more arrays or scalars to be inserted into $haystack.
	 *
	 * @return int               The index at which $needle was found.
	 */
	function planty_array_insert_before( &$haystack, $needle, $stuff ) {
		if ( ! is_array( $haystack ) ) {
			return -1;
		}

		$new_array = array();
		for ( $i = 2; $i < func_num_args(); ++$i ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) ) {
				if ( 2 == $i ) {
					$new_array = $arg;
				} else {
					$new_array = planty_array_merge( $new_array, $arg );
				}
			} else {
				$new_array[] = $arg;
			}
		}

		$i = 0;
		if ( is_array( $haystack ) && count( $haystack ) > 0 ) {
			foreach ( $haystack as $key => $value ) {
				if ( $key === $needle ) {
					break;
				}
				$i++;
			}
		}

		$haystack = is_int( $needle )
						? array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) )
						: planty_array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) );

		return $i;
	}
}

if ( ! function_exists( 'planty_array_get_by_key' ) ) {
	/**
	 * Return a list (an array with numeric keys) item or subkey value from a list
	 *
	 * @param array  &$arr    The plain array to search.
	 * @param string $key     The key to search for in subarrays.
	 * @param mixed  $value   A value to search.
	 * @param string $key2    Optional. The key of the subarray to return. If empty (default) - a whole subarray is returned.
	 * @param mixed  $default Optional. The default value to return if a specified value is not found in subarrays.
	 *
	 * @return mixed          A found item or $default.
	 */
	function planty_array_get_by_key( &$arr, $key, $value, $key2 = '', $default = '' ) {
		$rez = $default;
		foreach ( $arr as $v ) {
			if ( isset( $v[ $key ] ) && $v[ $key ] == $value ) {
				$rez = ! empty( $key2 ) ? $v[ $key2 ] : $v;
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'planty_array_str_replace' ) ) {
	/**
	 * Replace a substing in the specified key in all levels of array
	 * 
	 * @param string $from  A string to search for.
	 * @param string $to    A string to replace with.
	 * @param string $key   A key to search for in subarrays.
	 * @param array  $arr   The plain array to search.
	 * 
	 * @return array        A processed array.
	 */
	function planty_array_str_replace( $from, $to, $key, $arr ) {
		foreach ( $arr as $k => $v ) {
			if ( is_array( $v ) ) {
				$arr[ $k ] = planty_array_str_replace( $from, $to, $key, $v );
			} else if ( $k == $key ) {
				$arr[ $k ] = str_replace( $from, $to, $v );
			}
		}
		return $arr;
	}
}



/* HTML & CSS
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_generate_id' ) ) {
	/**
	 * Generate a random value for the tag's attribute 'id'
	 *
	 * @param string $prefix  Optional. A prefix string for id. Default is empty.
	 *
	 * @return string         A generated id.
	 */
	function planty_generate_id( $prefix = '' ) {
		return $prefix . str_replace( '.', '', mt_rand() );
	}
}

if ( ! function_exists( 'planty_get_tag' ) ) {
	/**
	 * Return a first tag or shortcode entry from the html layout.
	 *
	 * @param string $text       A searched html layout.
	 * @param string $tag_start  A tag start string. For example: '<video' or '[embed'
	 * @param string $tag_end    Optional. A tag end symbol. If omitted - will be detected by a first char of the $tag_start.
	 *
	 * @return string            A found tag with all attributes. For example: '<video src="URL" controls="1" autoplay="1">'
	 */
	function planty_get_tag( $text, $tag_start, $tag_end = '' ) {
		$val       = '';
		$pos_start = strpos( $text, $tag_start );
		if ( false !== $pos_start ) {
			$pos_end = $tag_end ? strpos( $text, $tag_end, $pos_start ) : false;
			if ( false === $pos_end ) {
				$tag_end = substr( $tag_start, 0, 1 ) == '<' ? '>' : ']';
				$pos_end = strpos( $text, $tag_end, $pos_start );
			}
			$val = substr( $text, $pos_start, $pos_end + strlen( $tag_end ) - $pos_start );
		}
		return $val;
	}
}

if ( ! function_exists( 'planty_get_tag_attrib' ) ) {
	/**
	 * Return a value of the specified attribute of the first tag or shortcode entry from the html layout.
	 *
	 * @param string $text  A searched html layout.
	 * @param string $tag   A tag string. For example: '<video>' or '[embed]'
	 * @param string $attr  An attribute name. For example: 'src'
	 *
	 * @return string       A found tag attribute or empty string.
	 *                      For example: $text = '<video src="URL" controls="1" autoplay="1">';
	 *                                   $tag = '<video>';
	 *                                   $attr = 'src';
	 *                                   Return 'URL' - a value of the attribute 'src' of the tag '<video>'.
	 */
	function planty_get_tag_attrib( $text, $tag, $attr ) {
		$val       = '';
		$pos_start = strpos( $text, substr( $tag, 0, strlen( $tag ) - 1 ) );
		if ( false !== $pos_start ) {
			$pos_end  = strpos( $text, substr( $tag, -1, 1 ), $pos_start );
			$pos_attr = strpos( $text, ' ' . ( $attr ) . '=', $pos_start );
			if ( false !== $pos_attr && $pos_attr < $pos_end ) {
				$pos_attr += strlen( $attr ) + 3;
				$pos_quote = strpos( $text, substr( $text, $pos_attr - 1, 1 ), $pos_attr );
				$val       = substr( $text, $pos_attr, $pos_quote - $pos_attr );
			}
		}
		return $val;
	}
}

if ( ! function_exists( 'planty_get_css_position_from_values' ) ) {
	/**
	 * Return a string with a CSS rules of the element position for the attribute 'style'.
	 * Each value can be a numeric or a string with allowed CSS-units (px, ex, em, %, vw, vh, etc.).
	 * If units is omitted - a 'px' should be used.
	 *
	 * @param string|numeric $top     Optional. A top offset of the element. If empty or omitted - not include to the result.
	 * @param string|numeric $right   Optional. A right offset of the element. If empty or omitted - not include to the result.
	 * @param string|numeric $bottom  Optional. A bottom offset of the element. If empty or omitted - not include to the result.
	 * @param string|numeric $left    Optional. A left offset of the element. If empty or omitted - not include to the result.
	 * @param string|numeric $width   Optional. A width of the element. If empty or omitted - not include to the result.
	 * @param string|numeric $height  Optional. A height of the element. If empty or omitted - not include to the result.
	 *
	 * @return string                 A set of CSS rules.
	 */
	function planty_get_css_position_from_values( $top = '', $right = '', $bottom = '', $left = '', $width = '', $height = '' ) {
		if ( ! is_array( $top ) ) {
			$top = compact( 'top', 'right', 'bottom', 'left', 'width', 'height' );
		}
		$output = '';
		foreach ( $top as $k => $v ) {
			$imp = substr( $v, 0, 1 );
			if ( '!' == $imp ) {
				$v = substr( $v, 1 );
			}
			if ( '' != $v ) {
				$output .= ( 'width' == $k ? 'width' : ( 'height' == $k ? 'height' : 'margin-' . esc_attr( $k ) ) ) . ':' . esc_attr( planty_prepare_css_value( $v ) ) . ( '!' == $imp ? ' !important' : '' ) . ';';
			}
		}
		return $output;
	}
}

if ( ! function_exists( 'planty_prepare_css_value' ) ) {
	/**
	 * Add a measure unit 'px' to the value, if units are not specified and value is numeric.
	 *
	 * @param string|numeric $val     A value to process.
	 *
	 * @return string                 A processed value with a measure unit (ready to use in CSS rules).
	 */
	function planty_prepare_css_value( $val ) {
		if ( '' !== $val ) {
			$parts = explode( ' ', trim( $val ) );
			foreach( $parts as $k => $v ) {
				$ed = substr( $v, -1 );
				if ( '0' <= $ed && $ed <= '9' ) {
					$parts[ $k ] .= 'px';
				}
			}
			$val = join( ' ', $parts );
		}
		return $val;
	}
}

if ( ! function_exists( 'planty_parse_icons_classes' ) ) {
	/**
	 * Return an array with icon's class names parsed from the css-file (from fontello).
	 *
	 * @param string $css  A path to the css-file with icon classes.
	 *
	 * @return array       A plain array with parsed class names.
	 */
	function planty_parse_icons_classes( $css ) {
		$rez = array();
		if ( ! file_exists( $css ) ) {
			return $rez;
		}
		$file = planty_fga( $css );
		if ( ! is_array( $file ) || count( $file ) == 0 ) {
			return $rez;
		}
		foreach ( $file as $row ) {
			if ( substr( $row, 0, 1 ) != '.' ) {
				continue;
			}
			$name = '';
			for ( $i = 1; $i < strlen( $row ); $i++ ) {
				$ch = substr( $row, $i, 1 );
				if ( in_array( $ch, array( ':', '{', '.', ' ' ) ) ) {
					break;
				}
				$name .= $ch;
			}
			if ( '' != $name ) {
				$rez[] = $name;
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'planty_get_column_class' ) ) {
	/**
	 * Return a class of the single column. For example: 'column-1_3'
	 *
	 * @param int $num         A number of the current column in the row.
	 * @param int $all         Total columns number in the row.
	 * @param int $all_tablet  Optional. Total columns number in the row on tablets. Default is empty string.
	 * @param int $all_mobile  Optional. Total columns number in the row on mobile devices. Default is empty string.
	 *
	 * @return string          A class name(s) for the current column.
	 */
	function planty_get_column_class( $num, $all, $all_tablet = '', $all_mobile = '' ) {
		$column_class_tpl = 'column-$1_$2';
		$column_class = str_replace( array( '$1', '$2' ), array( $num, $all ), $column_class_tpl );
		if ( ! empty( $all_tablet ) ) {
			$column_class .= ' ' . str_replace( array( '$1', '$2' ), array( $num, $all_tablet ), $column_class_tpl ) . '-tablet';
		}
		if ( ! empty( $all_mobile ) ) {
			$column_class .= ' ' . str_replace( array( '$1', '$2' ), array( $num, $all_mobile ), $column_class_tpl ) . '-mobile';
		}
		return $column_class;
	}
}





/* GET, POST, COOKIE, SESSION manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_stripslashes' ) ) {
	/**
	 * Strip slashes from the value received from http (if Magic Quotes is on).
	 *
	 * @param string $val  A value to strip slashes.
	 *
	 * @return string      A processed string.
	 */
	function planty_stripslashes( $val ) {
		static $magic = 0;
		if ( 0 === $magic ) {
			$magic = version_compare( phpversion(), '5.4', '>=' )
					|| ( function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc() == 1 )
					|| ( function_exists( 'get_magic_quotes_runtime' ) && get_magic_quotes_runtime() == 1 );
		}
		if ( is_array( $val ) ) {
			foreach ( $val as $k => $v ) {
				$val[ $k ] = planty_stripslashes( $v );
			}
		} else {
			$val = $magic ? stripslashes( trim( $val ) ) : trim( $val );
		}
		return $val;
	}
}

if ( ! function_exists( 'planty_get_value_gp' ) ) {
	/**
	 * Return a value with the specified name from GET or POST query.
	 *
	 * @param string $name  A name to return a value from GET or POST arrays.
	 * @param mixed  $defa  Optional. A default value (if the specified name is not found).
	 *
	 * @return mixed        A value from GET or POST arrays.
	 */
	function planty_get_value_gp( $name, $defa = '' ) {
		if ( isset( $_GET[ $name ] ) ) {
			$rez = wp_unslash( $_GET[ $name ] );
		} elseif ( isset( $_POST[ $name ] ) ) {
			$rez = wp_unslash( $_POST[ $name ] );
		} else {
			$rez = $defa;
		}
		return $rez;
	}
}

if ( ! function_exists( 'planty_get_value_gpc' ) ) {
	/**
	 * Return a value with the specified name from GET | POST | COOKIE (GPC) arrays.
	 *
	 * @param string $name  A name to return a value from GPC arrays.
	 * @param mixed  $defa  Optional. A default value (if the specified name is not found).
	 *
	 * @return mixed        A value from GPC arrays.
	 */
	function planty_get_value_gpc( $name, $defa = '' ) {
		if ( isset( $_GET[ $name ] ) ) {
			$rez = wp_unslash( $_GET[ $name ] );
		} elseif ( isset( $_POST[ $name ] ) ) {
			$rez = wp_unslash( $_POST[ $name ] );
		} elseif ( isset( $_COOKIE[ $name ] ) ) {
			$rez = wp_unslash( $_COOKIE[ $name ] );
		} else {
			$rez = $defa;
		}
		return $rez;
	}
}

if ( ! function_exists( 'planty_get_value_gps' ) ) {
	/**
	 * Return a value with the specified name from GET | POST | SESSION (GPS) arrays.
	 *
	 * @param string $name  A name to return a value from GPS arrays.
	 * @param mixed  $defa  Optional. A default value (if the specified name is not found).
	 *
	 * @return mixed        A value from GPS arrays.
	 */
	function planty_get_value_gps( $name, $defa = '' ) {
		global $wp_session;
		if ( isset( $_GET[ $name ] ) ) {
			$rez = wp_unslash( $_GET[ $name ] );
		} elseif ( isset( $_POST[ $name ] ) ) {
			$rez = wp_unslash( $_POST[ $name ] );
		} elseif ( isset( $wp_session[ $name ] ) ) {
			$rez = wp_unslash( $wp_session[ $name ] );
		} else {
			$rez = $defa;
		}
		return $rez;
	}
}

if ( ! function_exists( 'planty_get_session_value' ) ) {
	/**
	 * Return a value with the specified name from the current session.
	 *
	 * @param string $name  A name to return a value from the session.
	 * @param mixed  $defa  Optional. A default value (if the specified name is not found).
	 *
	 * @return mixed        A value from the session.
	 */
	function planty_get_session_value( $name, $defa = '' ) {
		global $wp_session;
		return isset( $wp_session[ $name ] ) ? $wp_session[ $name ] : $defa;
	}
}

if ( ! function_exists( 'planty_set_session_value' ) ) {
	/**
	 * Save a value with the specified name to the current session.
	 *
	 * @param string $name   A name to save a value to the session.
	 * @param mixed  $value  A value to save a value to the session.
	 */
	function planty_set_session_value( $name, $value ) {
		global $wp_session;
		$wp_session[ $name ] = $value;
	}
}

if ( ! function_exists( 'planty_del_session_value' ) ) {
	/**
	 * Delete a saved variable from the current session.
	 *
	 * @param string $name   A name to to delete from the session.
	 */
	function planty_del_session_value( $name ) {
		global $wp_session;
		unset( $wp_session[ $name ] );
	}
}

if ( ! function_exists( 'planty_get_cookie' ) ) {
	/**
	 * Return a value with the specified name from the cookies.
	 *
	 * @param string $name  A name to return a value from cookies.
	 * @param mixed  $defa  Optional. A default value (if the specified name is not found).
	 *
	 * @return string       A value from cookies.
	 */
	function planty_get_cookie( $name, $defa = '' ) {
		return planty_stripslashes( isset( $_COOKIE[ $name ] ) ? $_COOKIE[ $name ] : $defa );
	}
}

// Set a cookie - wrapper for setcookie using WP constants.
if ( ! function_exists( 'planty_set_cookie' ) ) {
	/**
	 * Save a value with the specified name to cookies.
	 *
	 * @param string $name     A name to save a value to cookies.
	 * @param string $value    A value to save to cookies.
	 * @param int    $expire   Optional. A timestamp when cookie will be expired. Default is 0 - until current session end.
	 * @param bool   $secure   Optional. Send this cookie only for https sessions. Default is false.
	 * @param bool   $httponly Optional. Send this cookie only on http protocol (not available for scripts). Default is false.
	 */
	function planty_set_cookie( $name, $value, $expire = 0, $secure = false, $httponly = false  ) {
		if ( ! headers_sent() ) {
			if ( defined( 'PHP_VERSION_ID' ) && PHP_VERSION_ID >= 70300 ) {
				$rez = setcookie(
							$name,
							$value,
							apply_filters( 'planty_filter_cookie_options', array(
								'expires'  => $expire,
								'path'     => defined( 'COOKIEPATH' ) && ! empty( COOKIEPATH ) ? COOKIEPATH : '/',
								'domain'   => defined( 'COOKIE_DOMAIN' ) && ! empty( COOKIE_DOMAIN ) ? COOKIE_DOMAIN : '',	//planty_get_domain_from_url( get_home_url() ),
								'secure'   => $secure,
								'httponly' => $httponly,
								'samesite' => 'None'	// Strict | Lax | None
							) )
						);
			} else {
				$rez = setcookie(
							$name,
							$value,
							$expire,
							defined( 'COOKIEPATH' ) && ! empty( COOKIEPATH ) ? COOKIEPATH : '/',
							defined( 'COOKIE_DOMAIN' ) && ! empty( COOKIE_DOMAIN ) ? COOKIE_DOMAIN : '',	//planty_get_domain_from_url( get_home_url() ),
							$secure,
							$httponly
						);
			}
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( sprintf( __( '%1$s cookie cannot be set - headers already sent by %2$s on line %3$s', 'planty' ), $name, $file, $line ), E_USER_NOTICE ); // @codingStandardsIgnoreLine
		}
	}
}





/* Colors manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_hex2rgb' ) ) {
	/**
	 * Convert a string with a hex color in the format '#RRGGBB' to the array with its RGB components
	 * (as integers in the range 0 - 255).
	 *
	 * @param string $hex  A string with a hex color.
	 *
	 * @return int[]       An array with RGB components in the format: ['r' => red_value, 'g' => green_value, 'b' => blue_value].
	 */
	function planty_hex2rgb( $hex ) {
		$dec = hexdec( substr( $hex, 0, 1 ) == '#' ? substr( $hex, 1 ) : $hex );
		return array(
			'r' => $dec >> 16,
			'g' => ( $dec & 0x00FF00 ) >> 8,
			'b' => $dec & 0x0000FF,
		);
	}
}

if ( ! function_exists( 'planty_hex2rgba' ) ) {
	/**
	 * Convert a string with a hex color in the format '#RRGGBB' to the string with a CSS function rgba()
	 * and add a value for the alpha channel.
	 *
	 * @param string $hex    A string with a hex color.
	 * @param int    $alpha  An integer value of the alpha channel to add to the results array.
	 *
	 * @return string        A CSS string with a function: 'rgba(red_value, green_value, blue_value, alpha)'.
	 */
	function planty_hex2rgba( $hex, $alpha ) {
		$rgb = planty_hex2rgb( $hex );
		return 'rgba(' . intval( $rgb['r'] ) . ',' . intval( $rgb['g'] ) . ',' . intval( $rgb['b'] ) . ',' . floatval( $alpha ) . ')';
	}
}

if ( ! function_exists( 'planty_hex2hsb' ) ) {
	/**
	 * Convert a string with a hex color in the format '#RRGGBB' to the array with HSB components
	 * and to the each component specified values from 2-4 arguments.
	 *
	 * @param string $hex  A string with a hex color.
	 * @param int    $h    Optional. An integer value to add to the 'h' channel of the result. Default is 0.
	 * @param int    $s    Optional. An integer value to add to the 's' channel of the result. Default is 0.
	 * @param int    $b    Optional. An integer value to add to the 'b' channel of the result. Default is 0.
	 *
	 * @return int[]       An array with HSB components in the format: ['h' => hue_value, 's' => saturation_value, 'b' => brightness_value].
	 */
	function planty_hex2hsb( $hex, $h = 0, $s = 0, $b = 0 ) {
		$hsb      = planty_rgb2hsb( planty_hex2rgb( $hex ) );
		$hsb['h'] = min( 359, max( 0, $hsb['h'] + $h ) );
		$hsb['s'] = min( 100, max( 0, $hsb['s'] + $s ) );
		$hsb['b'] = min( 100, max( 0, $hsb['b'] + $b ) );
		return $hsb;
	}
}

if ( ! function_exists( 'planty_rgb2hsb' ) ) {
	/**
	 * Convert an array with a RGB components to the array with HSB components.
	 *
	 * @param int[] $rgb  An array with RGB components in the format: ['r' => red_value, 'g' => green_value, 'b' => blue_value].
	 *
	 * @return int[]      An array with HSB components in the format: ['h' => hue_value, 's' => saturation_value, 'b' => brightness_value].
	 */
	function planty_rgb2hsb( $rgb ) {
		$hsb      = array();
		$hsb['b'] = max( max( $rgb['r'], $rgb['g'] ), $rgb['b'] );
		$hsb['s'] = ( $hsb['b'] <= 0 ) ? 0 : round( 100 * ( $hsb['b'] - min( min( $rgb['r'], $rgb['g'] ), $rgb['b'] ) ) / $hsb['b'] );
		$hsb['b'] = round( ( $hsb['b'] / 255 ) * 100 );
		if ( ( $rgb['r'] == $rgb['g'] ) && ( $rgb['g'] == $rgb['b'] ) ) {
			$hsb['h'] = 0;
		} elseif ( $rgb['r'] >= $rgb['g'] && $rgb['g'] >= $rgb['b'] ) {
			$hsb['h'] = 60 * ( $rgb['g'] - $rgb['b'] ) / ( $rgb['r'] - $rgb['b'] );
		} elseif ( $rgb['g'] >= $rgb['r'] && $rgb['r'] >= $rgb['b'] ) {
			$hsb['h'] = 60 + 60 * ( $rgb['g'] - $rgb['r'] ) / ( $rgb['g'] - $rgb['b'] );
		} elseif ( $rgb['g'] >= $rgb['b'] && $rgb['b'] >= $rgb['r'] ) {
			$hsb['h'] = 120 + 60 * ( $rgb['b'] - $rgb['r'] ) / ( $rgb['g'] - $rgb['r'] );
		} elseif ( $rgb['b'] >= $rgb['g'] && $rgb['g'] >= $rgb['r'] ) {
			$hsb['h'] = 180 + 60 * ( $rgb['b'] - $rgb['g'] ) / ( $rgb['b'] - $rgb['r'] );
		} elseif ( $rgb['b'] >= $rgb['r'] && $rgb['r'] >= $rgb['g'] ) {
			$hsb['h'] = 240 + 60 * ( $rgb['r'] - $rgb['g'] ) / ( $rgb['b'] - $rgb['g'] );
		} elseif ( $rgb['r'] >= $rgb['b'] && $rgb['b'] >= $rgb['g'] ) {
			$hsb['h'] = 300 + 60 * ( $rgb['r'] - $rgb['b'] ) / ( $rgb['r'] - $rgb['g'] );
		} else {
			$hsb['h'] = 0;
		}
		$hsb['h'] = round( $hsb['h'] );
		return $hsb;
	}
}

if ( ! function_exists( 'planty_hsb2rgb' ) ) {
	/**
	 * Convert an array with a HSB components to the array with RGB components.
	 *
	 * @param int[] $hsb  An array with HSB components in the format: ['h' => hue_value, 's' => saturation_value, 'b' => brightness_value].
	 *
	 * @return int[]      An array with RGB components in the format: ['r' => red_value, 'g' => green_value, 'b' => blue_value].
	 */
	function planty_hsb2rgb( $hsb ) {
		$rgb = array();
		$h   = round( $hsb['h'] );
		$s   = round( $hsb['s'] * 255 / 100 );
		$v   = round( $hsb['b'] * 255 / 100 );
		if ( 0 == $s ) {
			$rgb['r'] = $v;
			$rgb['g'] = $v;
			$rgb['b'] = $v;
		} else {
			$t1 = $v;
			$t2 = ( 255 - $s ) * $v / 255;
			$t3 = ( $t1 - $t2 ) * ( $h % 60 ) / 60;
			if ( 360 == $h ) {
				$h = 0;
			}
			if ( $h < 60 ) {
				$rgb['r'] = $t1;
				$rgb['b'] = $t2;
				$rgb['g'] = $t2 + $t3;
			} elseif ( $h < 120 ) {
				$rgb['g'] = $t1;
				$rgb['b'] = $t2;
				$rgb['r'] = $t1 - $t3;
			} elseif ( $h < 180 ) {
				$rgb['g'] = $t1;
				$rgb['r'] = $t2;
				$rgb['b'] = $t2 + $t3;
			} elseif ( $h < 240 ) {
				$rgb['b'] = $t1;
				$rgb['r'] = $t2;
				$rgb['g'] = $t1 - $t3;
			} elseif ( $h < 300 ) {
				$rgb['b'] = $t1;
				$rgb['g'] = $t2;
				$rgb['r'] = $t2 + $t3;
			} elseif ( $h < 360 ) {
				$rgb['r'] = $t1;
				$rgb['g'] = $t2;
				$rgb['b'] = $t1 - $t3;
			} else {
				$rgb['r'] = 0;
				$rgb['g'] = 0;
				$rgb['b'] = 0; }
		}
		return array(
			'r' => round( $rgb['r'] ),
			'g' => round( $rgb['g'] ),
			'b' => round( $rgb['b'] ),
		);
	}
}

if ( ! function_exists( 'planty_rgb2hex' ) ) {
	/**
	 * Convert an array with a RGB components to the string with a hex color presentation in the format '#RRGGBB'.
	 *
	 * @param int[] $rgb  An array with RGB components in the format: ['r' => red_value, 'g' => green_value, 'b' => blue_value].
	 *
	 * @return string     A string with a hex color presentation in the format '#RRGGBB'.
	 */
	function planty_rgb2hex( $rgb ) {
		$hex = array(
			dechex( $rgb['r'] ),
			dechex( $rgb['g'] ),
			dechex( $rgb['b'] ),
		);
		return '#' . ( strlen( $hex[0] ) == 1 ? '0' : '' ) . ( $hex[0] ) . ( strlen( $hex[1] ) == 1 ? '0' : '' ) . ( $hex[1] ) . ( strlen( $hex[2] ) == 1 ? '0' : '' ) . ( $hex[2] );
	}
}

if ( ! function_exists( 'planty_hsb2hex' ) ) {
	/**
	 * Convert an array with a HSB components to the string with a hex color presentation in the format '#RRGGBB'.
	 *
	 * @param int[] $hsb  An array with HSB components in the format: ['h' => hue_value, 's' => saturation_value, 'b' => brightness_value].
	 *
	 * @return string     A string with a hex color presentation in the format '#RRGGBB'.
	 */
	function planty_hsb2hex( $hsb ) {
		return planty_rgb2hex( planty_hsb2rgb( $hsb ) );
	}
}






/* Date manipulations
----------------------------------------------------------------------------------------------------- */

//
if ( ! function_exists( 'planty_date_to_sql' ) ) {
	/**
	 * Convert a date from the format 'dd.mm.YYYY' to the SQL format 'YYYY-mm-dd'
	 *
	 * @param string $str  A date in the format 'dd.mm.YYYY'
	 *
	 * @return string      A date in the SQL-format 'dd.mm.YYYY'
	 */
	function planty_date_to_sql($str) {
		if (trim($str)=='') return '';
		$str = strtr(trim($str),'/\.,','----');
		if (trim($str)=='00-00-0000' || trim($str)=='00-00-00') return '';
		$pos = strpos($str,'-');
		if ($pos > 3) return $str;
		$d=trim(substr($str,0,$pos));
		$str=substr($str,$pos+1);
		$pos = strpos($str,'-');
		$m=trim(substr($str,0,$pos));
		$y=trim(substr($str,$pos+1));
		$y=($y<50?$y+2000:($y<1900?$y+1900:$y));
		return ''.($y).'-'.(strlen($m)<2?'0':'').($m).'-'.(strlen($d)<2?'0':'').($d);
	}
}






/* Numbers manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_format_price' ) ) {
	/**
	 * Format a number as the price. If a number has no decimals (is integer) - return a price without decimals part,
	 * else - return a rounded price to 2 decimals digits delimited with dot '.'
	 *
	 * @param mixed $price   A number or a string with a number presentation.
	 *
	 * @return mixed|string  A formatted price.
	 */
	function planty_format_price( $price ) {
		return is_numeric( $price )
					? ( $price != round( $price, 0 )
						? number_format( round( $price, 2 ), 2, '.', ' ' )
						: number_format( $price, 0, '.', ' ' )
						)
					: $price;
	}
}

if ( ! function_exists( 'planty_num2size' ) ) {
	/**
	 * Convert number to K-format if a number greater then 1000. For example: 10200 -> '10K'
	 *
	 * @param mixed $num     A number or a string with a number presentation.
	 *
	 * @return mixed|string  A string in a K-format or the original number (if less then 1000).
	 */
	function planty_num2size( $num ) {
		return $num > 1000 ? round( $num/1000, 0 ) . 'K' : $num;
	}
}

if ( ! function_exists( 'planty_size2num' ) ) {
	/**
	 * Try to convert size string with a suffix K(ilo) | M(ega) | G(iga) | T(era) | P(enta) to the integer:
	 *
	 * '10K' -> 10240 if an argument $full == true
	 *
	 * '10K' -> 10000 if an argument $full == false
	 *
	 * @param string $num   A string in a K-format.
	 * @param bool   $full  Optional. A base for convertation: true - 1024, false - 1000. Default is false (a base equal to 1000).
	 *
	 * @return int          A converted number.
	 */
	function planty_size2num( $size, $full = false ) {
		$suff = strtoupper( substr( $size, -1 ) );
		$pos  = strpos( 'KMGTP', $suff );
		if ( $pos !== false ) {
			$size = intval( substr( $size, 0, -1 ) ) * pow( $full ? 1024 : 1000, $pos + 1 );
		}
		return (int)$size;
	}
}

//
if ( ! function_exists( 'planty_parse_num' ) ) {
	/**
	 * Clear a string with a number presentation and leave only sign (+/-), digits and point (.) as delimiter.
	 * For example: '-123.45test' -> -123.45
	 *
	 * @param string $str  A string with a 'dirty' number.
	 *
	 * @return float       A clean number.
	 */
	function planty_parse_num( $str ) {
		return (float) filter_var( html_entity_decode( strip_tags( $str ) ), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	}
}






/* String manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_prepare_macros' ) ) {
	/**
	 * Replace all macros entries in the string:
	 *
	 * {{xxx}} will be replaced with <i>xxx</i>
	 *
	 * ((xxx)) will be replaced with <b>xxx</b>
	 *
	 * xxx||yyy will be replaced with xxx<br>yyy
	 *
	 * @param string $str  A string to replace all macros in.
	 *
	 * @return string      A processed string.
	 */
	function planty_prepare_macros( $str ) {
		return str_replace(
			array( '{{', '}}', '((', '))', '||' ),
			array( '<i>', '</i>', '<b>', '</b>', '<br>' ),
			$str
		);
	}
}

if ( ! function_exists( 'planty_remove_macros' ) ) {
	/**
	 * Remove all macros wrappers from the string.
	 * Delete all substrings '{{', '}}', '((', '))', '||'.
	 *
	 * @param string $str  A string to remove all macros in.
	 *
	 * @return string      A processed string.
	 */
	function planty_remove_macros( $str ) {
		return str_replace(
			array( '{{', '}}', '((', '))', '||' ),
			array( '', '', '', '', ' ' ),
			$str
		);
	}
}

if ( ! function_exists( 'planty_is_on' ) ) {
	/**
	 * Check if a value is equal to the 'ON' state:
	 *
	 * - if its an array - return true if count items > 0.
	 *
	 * - if its a number - return true if it is not equal to 0.
	 *
	 * - if its a bool - return true if it is equal to true.
	 *
	 * - if its a string - return true if it is not empty an enqual to '1' | 'true' | 'on' | 'yes' | 'show'.
	 *
	 * @param mixed $prm  A value to check.
	 *
	 * @return bool       Return true if a value is equal to the 'ON' state.
	 */
	function planty_is_on( $prm ) {
		return is_array( $prm )
					? count( $prm ) > 0
					: ( is_bool( $prm ) && $prm === true )
						|| ( is_numeric( $prm ) && $prm > 0 )
						|| in_array( strtolower( $prm ), array( '1', 'true', 'on', 'yes', 'show' )
						);
	}
}
if ( ! function_exists( 'planty_is_off' ) ) {
	/**
	 * Check if a value is equal to the 'OFF' state:
	 *
	 * - if its an array - return true if count items == 0.
	 *
	 * - if its a number - return true if it is equal to 0.
	 *
	 * - if its a bool - return true if it is equal to false.
	 *
	 * - if its a string - return true if it is not empty an enqual to '0' | 'false' | 'off' | 'no' | 'hide'.
	 *
	 * @param mixed $prm  A value to check.
	 *
	 * @return bool       Return true if a value is equal to the 'OFF' state.
	 */
	function planty_is_off( $prm ) {
		return is_array( $prm )
					? count( $prm ) == 0
					: empty( $prm )
						|| ( is_numeric( $prm ) && 0 === $prm )
						|| in_array( strtolower( $prm ), array( '0', 'false', 'off', 'no', 'none', 'hide' ) );
	}
}
if ( ! function_exists( 'planty_is_inherit' ) ) {
	/**
	 * Check if a value is equal to the 'INHERIT' state:
	 *
	 * - if its a string and enqual to 'inherit'.
	 *
	 * @param mixed $prm  A value to check.
	 *
	 * @return bool       Return true if a value is equal to the 'INHERIT' state.
	 */
	function planty_is_inherit( $prm ) {
		return ! is_array( $prm ) && in_array( strtolower( $prm ), array( 'inherit' ) );
	}
}

if ( ! function_exists( 'planty_strshort' ) ) {
	/**
	 * Return a string truncated on the edge of word to the specified max length (in symbols) and appended with '...'
	 * ( or any other characters specified in the third argument ).
	 *
	 * @param string $str        A string to truncate.
	 * @param int    $maxlength  A maximum length (in characters) for the result.
	 * @param string $add        Optional. A characters to append the result.
	 *
	 * @return string            A truncated string.
	 */
	function planty_strshort( $str, $maxlength, $add = '&hellip;' ) {
		if ( 0 >= $maxlength ) {
			return '';
		}
		$str = planty_strip_tags( $str );
		if ( strlen( $str ) <= $maxlength ) {
			return $str;
		}
		$str = substr( $str, 0, $maxlength - strlen( $add ) );
		$ch  = substr( $str, $maxlength - strlen( $add ), 1 );
		if ( ' ' != $ch ) {
			for ( $i = strlen( $str ) - 1; $i > 0; $i-- ) {
				if ( ' ' == substr( $str, $i, 1 ) ) {
					break;
				}
			}
			$str = trim( substr( $str, 0, $i ) );
		}
		if ( ! empty( $str ) && strpos( ',.:;-', substr( $str, -1 ) ) !== false ) {
			$str = substr( $str, 0, -1 );
		}
		return ( $str ) . ( $add );
	}
}

if ( ! function_exists( 'planty_strwords' ) ) {
	/**
	 * Return a string truncated on the edge of word to the specified max length (in words) and appended with '...'
	 * ( or any other characters specified in the third argument ).
	 *
	 * @param string $str        A string to truncate.
	 * @param int    $maxlength  A maximum length (in words) for the result.
	 * @param string $add        Optional. A characters to append the result.
	 *
	 * @return string            A truncated string.
	 */
	function planty_strwords( $str, $maxlength, $add = '&hellip;' ) {
		if ( $maxlength <= 0 ) {
			return '';
		}
		$words = explode( ' ', $str );
		if ( count( $words ) > $maxlength ) {
			$words = array_slice( $words, 0, $maxlength );
			$words[ count( $words ) - 1 ] .= $add;
		}
		return join(' ', $words	);
	}
}

if ( ! function_exists( 'planty_strip_tags' ) ) {
	/**
	 * Remove all non-text tags from html.
	 *
	 * @param  string $str  A string to process.
	 *
	 * @return string       A clean string without any html tags.
	 */
	function planty_strip_tags( $str ) {
		// remove comments and any content found in the the comment area (strip_tags only removes the actual tags).
		$str = preg_replace( '#<!--.*?-->#s', '', $str );
		// remove all script and style tags
		$str = preg_replace( '#<(script|style)\b[^>]*>(.*?)</(script|style)>#is', "", $str );
		// remove br tags (missed by strip_tags)
		$str = preg_replace( '#<br[^>]*?>#', ' ', $str );
		// put a space between list items, paragraphs and headings (strip_tags just removes the tags).
		$str = preg_replace( '#</(li|p|span|h1|h2|h3|h4|h5|h6)>#', ' </$1>', $str );
		// remove all remaining html-code
		$str = strip_tags( $str );
		return trim( $str );
	}
}

if ( ! function_exists( 'planty_excerpt' ) ) {
	/**
	 * Make an excerpt from the content with a html layout.
	 *
	 * @param string $str        A string with html layout.
	 * @param int    $maxlength  A maximum length for excerpt.
	 * @param string $add        Optional. A characters to append the result.
	 *
	 * @return string            A truncated string without html tags.
	 */
	function planty_excerpt( $str, $maxlength, $add = '&hellip;' ) {
		if ( $maxlength <= 0 ) {
			return '';
		}
		return planty_strwords( planty_strip_tags( $str ), $maxlength, $add );
	}
}

if ( ! function_exists( 'planty_unserialize_recover' ) ) {
	/**
	 * Recalculate string length counters in the serialized string.
	 * 
	 * @param string $str  A serialized string.
	 * 
	 * @return string      A processed string.
	 */
	function planty_unserialize_recover( $str ) {
		return preg_replace_callback(
			'!s:(\d+):"(.*?)";!s',
			function( $match ) {
				return ( strlen( $match[2] ) == $match[1] )
					? $match[0]
					: 's:' . strlen( $match[2] ) . ':"' . $match[2] . '";';
			},
			$str
		);
	}
}

if ( ! function_exists( 'planty_unserialize' ) ) {
	/**
	 * Try unserialize a string and process cases with CR and wrong string length counters.
	 *
	 * @param string $str  A serialized string.
	 *
	 * @return false|mixed Return an unserialized string or false if an unrecoverable error occurs.
	 */
	function planty_unserialize( $str ) {
		if ( ! empty( $str ) && is_serialized( $str ) ) {
			// If serialized data contain an unrecoverable object (a base class for this object is not exists) - skip this string
			if ( true || ! preg_match( '/O:[0-9]+:"([^"]*)":[0-9]+:{/', $str, $matches ) || empty( $matches[1] ) || class_exists( $matches[1] ) ) {
				try {
					// Attempt 1: try unserialize original string
					$data = @unserialize( $str );
					// Attempt 2: try unserialize original string without CR symbol '\r'
					if ( false === $data ) {
						$str2 = str_replace( "\r", "", $str );
						$data = @unserialize( $str2 );
					}
					// Attempt 3: try unserialize original string with modified character counters
					if ( false === $data ) {
						$data = @unserialize( planty_unserialize_recover( $str ) );
					}
					// Attempt 4: try unserialize original string without CR symbol '\r' with modified character counters
					if ( false === $data ) {
						$data = @unserialize( planty_unserialize_recover( $str2 ) );
					}
				} catch ( Exception $e ) {
					if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
						dcl( $e->getMessage() );
					}
					$data = false;
				}
				return $data;
			} else {
				return $str;
			}
		} else {
			return $str;
		}
	}
}

if ( ! function_exists( 'planty_str_replace' ) ) {
	/**
	 * Make a deep replacement with a support for arrays, objects and serialized strings.
	 *
	 * @param string|array $from  A string or array with strings to be replaced.
	 * @param string|array $to    A string or array with strings to replace on.
	 * @param mixed        $str   A string|array|object to search in.
	 *
	 * @return mixed              A processed string|array|object.
	 */
	function planty_str_replace( $from, $to, $str ) {
		if ( is_array( $str ) ) {
			foreach ( $str as $k => $v ) {
				$str[ $k ] = planty_str_replace( $from, $to, $v );
			}
		} elseif ( is_object( $str ) ) {
			if ( '__PHP_Incomplete_Class' !== get_class( $str ) ) {
				foreach ( $str as $k => $v ) {
					$str->{$k} = planty_str_replace( $from, $to, $v );
				}
			}
		} elseif ( is_string( $str ) ) {
			if ( is_serialized( $str ) ) {
				$str = serialize( planty_str_replace( $from, $to, planty_unserialize( $str ) ) );
			} else {
				$str = str_replace( $from, $to, $str );
			}
		}
		return $str;
	}
}

if ( ! function_exists( 'planty_str_replace_once' ) ) {
	/**
	 * Uses only the first encountered substitution from the list.
	 *
	 * @param string|array $from  A string or array with strings to be replaced.
	 * @param string|array $to    A string or array with strings to replace on.
	 * @param string $str         A string to search in.
	 *
	 * @return string             A processed string.
	 */
	function planty_str_replace_once( $from, $to, $str ) {
		$rez = '';
		if ( ! is_array( $from ) ) {
			$from = array( $from );
		}
		if ( ! is_array( $to ) ) {
			$to = array( $to );
		}
		for ( $i = 0; $i < strlen( $str ); $i++ ) {
			$found = false;
			for ( $j = 0; $j < count( $from ); $j++ ) {
				if ( substr( $str, $i, strlen( $from[ $j ] ) ) == $from[ $j ] ) {
					$rez  .= isset( $to[ $j ] ) ? $to[ $j ] : '';
					$found = true;
					$i    += strlen( $from[ $j ] ) - 1;
					break;
				}
			}
			if ( ! $found ) {
				$rez .= $str[ $i ];
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'planty_tags_count' ) ) {
	/**
	 * Return a high-level tags number.
	 *
	 * @param string $str  A string with a html layouts to search tags.
	 * @param string $tag  A tag name to search (without '<' and '>', only name).
	 * 
	 * @return int         A found entries number.
	 */
	function planty_tags_count( $str, $tag ) {
		$cnt = 0;
		if ( ! empty( $str ) && is_string( $str ) ) {
			$tag_start = '<' . $tag . ' ';
			$tag_end   = '</' . $tag . '>';
			$tag_start_len = strlen( $tag_start );
			$tag_end_len = strlen( $tag_end );
			$tag_in = 0;
			for ( $i = 0; $i < strlen( $str ); $i++ ) {
				if ( substr( $str, $i, $tag_start_len ) == $tag_start ) {
					$tag_in++;
					$i += $tag_start_len - 1;
					$cnt += 1 == $tag_in ? 1 : 0;
				} elseif ( substr( $str, $i, $tag_end_len ) == $tag_end ) {
					$tag_in--;
					$i += $tag_end_len - 1;
				}
			}
		}
		return $cnt;
	}
}



/* Media: images, galleries, audio, video
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_getimagesize' ) ) {
	/**
	 * Get a result of the getimagesize() for an image URL (only if an image is found in the folder 'uploads').
	 *
	 * @param string $url  An image URL
	 *
	 * @return array|false A result of the getimagesize() or false if an image is not found in the folder 'uploads'.
	 */
	function planty_getimagesize( $url, $echo = false ) {
		$img_size = false;
		if ( is_string( $url ) ) {
			$img_path = planty_is_url( $url ) ? planty_url_to_local_path( $url ) : $url;
			if ( ! empty( $img_path ) && file_exists( $img_path ) ) {
				$img_size = getimagesize( $img_path );
			}
			if ( $echo && $img_size !== false && ! empty( $img_size[3] ) ) {
				echo ' ' . trim( $img_size[3] );
			}
		}
		return $img_size;
	}
}

if ( ! function_exists( 'planty_clear_thumb_size' ) ) {
	/**
	 * Clear a suffix with a thumb size from the image URL.
	 *
	 * For example: 'image-name-200x125.jpg' -> 'image-name.jpg'
	 *
	 * @param string $url              An image URL (potentially with a suffix).
	 * @param bool   $remove_protocol  Optional. Remove a protocol from the image URL. Default is true.
	 *
	 * @return string                  A processed URL without a thumb size suffix and a protocol
	 *                                 (if a second argument is true).
	 */
	function planty_clear_thumb_size( $url, $remove_protocol = true ) {
		if ( empty( $url ) ) return '';
		$pi = pathinfo( $url );
		if ( $remove_protocol ) {
			$pi['dirname'] = planty_remove_protocol_from_url( $pi['dirname'], false );
		}
		$parts = explode( '-', $pi['filename'] );
		$suff  = explode( 'x', $parts[ count( $parts ) - 1 ] );
		if ( count( $suff ) == 2 && (int) $suff[0] > 0 && (int) $suff[1] > 0 ) {
			array_pop( $parts );
			$url = $pi['dirname'] . '/' . join( '-', $parts ) . '.' . $pi['extension'];
		}
		return $url;
	}
}

if ( ! function_exists( 'planty_add_thumb_size' ) ) {
	/**
	 * Add a suffix with a thumb size to the image URL.
	 *
	 * For example: 'image-name.jpg' -> 'image-name-200x125.jpg'
	 *
	 * @param string $url         An image URL.
	 * @param string $thumb_size  A registered thumb size name to add its dimensions.
	 * @param bool $check_exists  Optional. Check if a result image exists in the folder 'uploads'. Default is true.
	 *                            If a result image is not exists - an URL for the original image is returned.
	 *
	 * @return string             A processed URL with a thumb size suffix.
	 */
	function planty_add_thumb_size( $url, $thumb_size, $check_exists = true ) {

		if ( empty( $url ) ) return '';

		$pi = pathinfo( $url );

		// Remove image sizes from filename
		$parts = explode( '-', $pi['filename'] );
		$suff = explode( 'x', $parts[ count( $parts ) - 1 ] );
		if ( count( $suff ) == 2 && (int) $suff[0] > 0 && (int) $suff[1] > 0) {
			array_pop( $parts );
		}
		$url = ( ! empty( $pi['dirname'] ) ? $pi['dirname'] . '/' : '' ) . join( '-', $parts ) . ( ! empty( $pi['extension'] ) ? '.' . $pi['extension'] : '' );

		// Add new image sizes
		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) && in_array( $thumb_size, array_keys( $_wp_additional_image_sizes ) ) ) {
			if ( empty( $_wp_additional_image_sizes[ $thumb_size ]['height'] ) || empty( $_wp_additional_image_sizes[ $thumb_size ]['crop'] ) ) {
				$image_id = planty_attachment_url_to_postid( $url );
				if ( is_numeric( $image_id ) && (int) $image_id > 0 ) {
					$attach = wp_get_attachment_image_src( $image_id, $thumb_size );
					if ( ! empty( $attach[0] ) ) {
						$pi = pathinfo( $attach[0] );
						$pi['dirname'] = planty_remove_protocol_from_url( $pi['dirname'] );
						$parts = explode( '-', $pi['filename'] );
					}
				}
			} else {
				$parts[] = intval( $_wp_additional_image_sizes[ $thumb_size ]['width'] ) . 'x' . intval( $_wp_additional_image_sizes[ $thumb_size ]['height'] );
			}
		}
		$pi['filename'] = join( '-', $parts );
		$new_url = planty_remove_protocol_from_url( ( ! empty( $pi['dirname'] ) ? $pi['dirname'] . '/' : '' ) . $pi['filename'] . ( ! empty( $pi['extension'] ) ? '.' . $pi['extension'] : '' ) );

		// Check exists
		if ( $check_exists ) {
			$uploads_info = wp_upload_dir();
			$uploads_url = planty_remove_protocol_from_url( $uploads_info['baseurl'] );
			$uploads_dir = $uploads_info['basedir'];
			if ( strpos( $new_url, $uploads_url ) !== false ) {
				if ( ! file_exists( str_replace( $uploads_url, $uploads_dir, $new_url ) ) ) {
					$new_url = planty_remove_protocol_from_url( $url );
				}
			} else {
				$new_url = planty_remove_protocol_from_url( $url );
			}
		}
		return $new_url;
	}
}

if ( ! function_exists( 'planty_get_thumb_size' ) ) {
	/**
	 * Return a theme-specific image thumb size name with a prefix and a multiplier (if a visitor have a retina device).
	 *
	 * For example:
	 *
	 * 'masonry' -> 'planty-thumb-masonry' or 'planty-thumb-masonry-@retina'
	 *
	 * but 'full' -> 'full', 'woocommerce-product' -> 'woocommerce-product'. etc.
	 *
	 * @param  string $ts  A short name of the theme-specific thumb size.
	 *
	 * @return mixed       A full name with a theme-specific prefix and retina suffix (if need)
	 */
	function planty_get_thumb_size( $ts ) {
		$thumb_sizes = planty_storage_get( 'theme_thumbs' );
		if ( ! empty( $thumb_sizes[ "planty-thumb-{$ts}" ] ) ) {
			$ts = 'planty-thumb-' . $ts;
		}
		$retina = planty_get_retina_multiplier() > 1 ? '-@retina' : '';
		$is_external = apply_filters(
							'planty_filter_is_external_thumb_size',
							in_array( $ts,
										apply_filters(
											'planty_filter_external_thumb_sizes',
											array( 'full', 'post-thumbnail', 'thumbnail', 'large' )   // Don't add 'medium' to this array
										)
									)
							|| strpos( $ts, 'woocommerce' ) === 0
							|| strpos( $ts, 'yith' ) === 0
							|| strpos( $ts, 'course' ) === 0
							|| strpos( $ts, 'trx_demo' ) === 0,
							$ts
						);
		$is_internal = strpos( $ts, 'planty-thumb-' ) === 0 || strpos( $ts, 'trx_addons-thumb-' ) === 0;
		return apply_filters(
					'planty_filter_get_thumb_size',
					( $is_external || $is_internal ? '' : 'planty-thumb-' )
					. $ts
					. ( $is_internal ? $retina : '' )
				);
	}
}

if ( ! function_exists( 'planty_detect_thumb_size' ) ) {
	/**
	 * Detect a thumb size name from the specified file name. For example:
	 *
	 * 'image-name-300x300.jpg' -> 'medium',
	 *
	 * 'image-name-90x90.jpg' -> 'planty-thumb-tiny'
	 *
	 * @param string $image  An image URL to detect a thumb size name.
	 *
	 * @return string        Detected name of the registered thumb size.
	 */
	function planty_detect_thumb_size( $image ) {
		$ts = '';
		if ( preg_match( '/\-([0-9]{1,4}x[0-9]{1,4})\./', $image, $matches ) && ! empty( $matches[1] ) ) {
			$dim = array_map( 'intval', explode( 'x', $matches[1] ) );
			$ts = planty_get_closest_thumb_size( '', array( 'width' => $dim[0], 'height' => $dim[1] ) );
		}
		return $ts;
	}
}

if ( ! function_exists( 'planty_get_closest_thumb_size' ) ) {
	/**
	 * Return a closest thumb size by dimensions.
	 *
	 * @param string $old_size  A current thumb size (leave it unchanged if it dimensions fit).
	 * @param array  $dim       An array with keys 'width' and 'height' (both are optional) to search a closest thumb size to fit.
	 * @param string $prefix    Optional. A substring at the start of the name of thumbnails
	 *                          (if not equal - skip this size) to filter the result. Default is empty string.
	 *
	 * @return string           A closest thumb size name to fit specified dimensions.
	 */
	function planty_get_closest_thumb_size( $old_size, $dim, $prefix = '' ) {
		$closest = array( 'thumb' => '', 'width' => 0, 'height' => 0 );
		$biggest = array( 'thumb' => '', 'width' => 0, 'height' => 0 );
		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
			$tmp = $_wp_additional_image_sizes;
			if ( isset( $tmp[ $old_size ] ) ) {
				unset( $tmp[ $old_size ] );
				$tmp = array_merge( array( $old_size => $_wp_additional_image_sizes[ $old_size ] ), $tmp );
			}
			foreach ( $tmp as $thumb => $sizes ) {
				if ( ! empty( $prefix ) && substr( $thumb, 0, strlen( $prefix ) ) !== $prefix ) {
					continue;
				}
				$cur = array(
							'thumb' => $thumb,
							'width' => $sizes['width'],
							'height' => $sizes['height']
							);
				if (
					( empty( $dim['width'] ) || empty( $cur['width'] ) || $dim['width'] <= $cur['width'] )
					&&
					( empty( $dim['height'] ) || empty( $cur['height'] ) || $dim['height'] <= $cur['height'] )
					&&
					( empty( $closest['thumb'] )
						|| ( ! empty( $cur['width'] ) && ( empty( $closest['width'] ) || $cur['width'] < $closest['width'] ) )
						|| ( ! empty( $cur['height'] ) && ( empty( $closest['height'] ) || $cur['height'] < $closest['height'] ) )
					)
				) {
					$closest = $cur;
					if ( $thumb == $old_size ) {
						break;
					}
				}
				if (
					( empty( $dim['width'] ) || empty( $cur['width'] ) || $cur['width'] <= $dim['width'] )
					&&
					( empty( $dim['height'] ) || empty( $cur['height'] ) || $cur['height'] <= $dim['height'] )
					&&
					( empty( $biggest['thumb'] )
						|| ( ! empty( $biggest['width'] ) && $cur['width'] > $biggest['width'] )
						|| ( ! empty( $biggest['height'] ) && $cur['height'] > $biggest['height'] )
					)
				) {
					$biggest = $cur;
				}
			}
			if ( empty( $closest['thumb'] ) ) {
				$closest['thumb'] = 'full';	// Can return $biggest['thumb'] to get closest, but smaller size
			}
		}
		return $closest['thumb'];
	}
}

if ( ! function_exists( 'planty_get_attachment_url' ) ) {
	/**
	 * Return an image URL for the specified thumb size by attachment ID or URL.
	 *
	 * @param array|int|string $image_id  An attachment ID to get URL or the image URL to add a thumb size suffix.
	 * @param string           $size      Optional. A thumb size name to add a suffix to the URL.
	 *
	 * @return string
	 */
	function planty_get_attachment_url( $image_id, $size = 'full' ) {
		if ( is_array( $image_id ) ) {
			$image_id = ! empty( $image_id[ 'id' ] )
							? (int) $image_id[ 'id' ]
							: ( ! empty( $image_id[ 'url' ] )
									? $image_id[ 'url' ]
									: ''
								);
		}
		if ( is_numeric( $image_id ) && (int) $image_id > 0 ) {
			$attach   = wp_get_attachment_image_src( $image_id, $size );
			$image_id = empty( $attach[0] ) ? '' : $attach[0];
		} else {
			$image_id = planty_add_thumb_size( $image_id, $size );
		}
		return $image_id;
	}
}

if ( ! function_exists( 'planty_get_post_image' ) ) {
	/**
	 * Search the first tag <img> in the post content
	 * and return a value of the attribute 'src' or a whole tag (if a second parameter is false).
	 *
	 * @param string $post_text  Optional. A post content to search a tag <img>.
	 *                           If omitted or empty - a current post content is used.
	 * @param bool $src          Optional. If true - return only the value of the attribute 'src',
	 *                           else - return a whole tag. Default is true.
	 *
	 * @return string           Founded URL (or a whole tag) or an empty string if found nothing.
	 */
	function planty_get_post_image( $post_text = '', $src = true ) {
		global $post;
		$img = '';
		if ( empty( $post_text ) ) {
			$post_text = $post->post_content;
		}
		if ( preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"][^>]*>/is', $post_text, $matches ) ) {
			$img = $matches[ $src ? 1 : 0 ][0];
		}
		return $img;
	}
}

if ( ! function_exists( 'planty_get_post_audio' ) ) {
	/**
	 * Search the first tag <audio> or a shortcode [audio] in the post content
	 * and return a value of the attribute 'src' or a whole tag (if a second parameter is false).
	 *
	 * @param string $post_text  Optional. A post content to search a tag <audio>.
	 *                           If omitted or empty - a current post content is used.
	 * @param bool $src          Optional. If true - return only the value of the attribute 'src',
	 *                           else - return a whole tag. Default is true.
	 *
	 * @return string           Founded URL (or a whole tag) or an empty string if found nothing.
	 */
	function planty_get_post_audio( $post_text = '', $src = true ) {
		global $post;
		$img = planty_get_post_audio_list_first( ! $src );
		if ( is_array( $img ) ) {
			if ( ! empty( $img['url'] ) ) {
				$img = $img['url'];
			} else if ( ! empty( $img['embed'] ) ) {
				$img = planty_get_post_iframe( $img['embed'], true );
			} else {
				$img = '';
			}
		}
		if ( empty( $img ) ) {
			if ( empty( $post_text ) ) {
				$post_text = $post->post_content;
			}
			if ( $src ) {
				if ( preg_match_all( '/<audio.+src=[\'"]([^\'"]+)[\'"][^>]*>/is', $post_text, $matches ) ) {
					$img = $matches[1][0];
				} else if ( preg_match_all( '/<!\\-\\- wp:trx-addons\\/audio-item.+"url":"([^"]*)"/is', $post_text, $matches ) ) {
					$img = $matches[1][0];
				}
			} else {
				$img = planty_get_tag( $post_text, '<audio', '</audio>' );
				if ( empty( $img ) ) {
					$img = do_shortcode( planty_get_tag( $post_text, '[audio', '[/audio]' ) );
				}
				if ( empty( $img ) ) {
					$img = planty_get_tag_attrib( $post_text, '[trx_widget_audio]', 'url' );
					if ( empty( $img ) && preg_match_all( '/<!\\-\\- wp\\:trx-addons\\/audio-item.+"url"\\:"([^"]*)"/is', $post_text, $matches ) ) {
						$img = $matches[1][0];
					}
					if ( ! empty( $img ) ) {
						$img = '<audio src="' . esc_url( $img ) . '"></audio>';
					}
				}
			}
		}
		return $img;
	}
}

if ( ! function_exists( 'planty_get_post_audio_list' ) ) {
	/**
	 * Return a list of audios from the post options.
	 *
	 * @param array $args  Optional. An array with filtering arguments. Default is an empty array.
	 *
	 * @return array       An array with the audio entries in the format:
	 *                     [ [ 'url' => 'audio1_url or empty', 'embed' => 'audio1_embed_code or empty' ],
	 *                       [ 'url' => 'audio2_url or empty', 'embed' => 'audio2_embed_code or empty' ],
	 *                       ...
	 *                     ]
	 */
	function planty_get_post_audio_list( $args=array() ) {
		return function_exists( 'trx_addons_get_post_audio_list' ) ? trx_addons_get_post_audio_list( $args ) : array();
	}
}

if ( ! function_exists( 'planty_get_post_audio_list_first' ) ) {
	/**
	 * Return a first audio from the post options.
	 *
	 * @param bool  $src   Optional. If true - only the audio URL must be returned, else - an array with audio settings.
	 *                     Default is false.
	 * @param array $args  Optional. An array with filtering arguments. Default is an empty array.
	 *
	 * @return array       An array with the first audio entry in the format:
	 *                     [ 'url' => 'audio1_url or empty', 'embed' => 'audio1_embed_code or empty' ]
	 *                     or the URL only
	 */
	function planty_get_post_audio_list_first( $src = false, $args = array() ) {
		return function_exists( 'trx_addons_get_post_audio_list_first' ) ? trx_addons_get_post_audio_list_first( $src, $args ) : '';
	}
}

if ( ! function_exists( 'planty_get_post_video' ) ) {
	/**
	 * Search the first tag <video> or a shortcode [video] in the post content
	 * and return a value of the attribute 'src' or a whole tag (if a second parameter is false).
	 *
	 * @param string $post_text  Optional. A post content to search a tag <video>.
	 *                           If omitted or empty - a current post content is used.
	 * @param bool $src          Optional. If true - return only the value of the attribute 'src',
	 *                           else - return a whole tag. Default is true.
	 *
	 * @return string           Founded URL (or a whole tag) or an empty string if found nothing.
	 */
	function planty_get_post_video( $post_text = '', $src = true ) {
		global $post;
		$img = planty_get_post_video_list_first( ! $src );
		if ( is_array( $img ) ) {
			if ( ! empty( $img['video_url'] ) ) {
				$img = $img['video_url'];
			} else if ( ! empty( $img['video_embed'] ) ) {
				$img = planty_get_post_iframe( $img['video_embed'], true );
			} else {
				$img = '';
			}
		}
		if ( empty( $img ) ) {
			if ( empty( $post_text ) ) {
				$post_text = $post->post_content;
			}
			if ( $src ) {
				if ( preg_match_all( '/<video.+src=[\'"]([^\'"]+)[\'"][^>]*>/is', $post_text, $matches ) ) {
					$img = $matches[1][0];
				} else if ( preg_match_all( '/<!-- wp:trx-addons\\/video.+"link":"([^"]*)"/is', $post_text, $matches ) ) {
					$img = $matches[1][0];
				} else if ( preg_match_all( '/<!-- wp:core-embed\\/(youtube|vimeo|dailymotion|facebook).+"url":"([^"]*)"/is', $post_text, $matches ) ) {
					$img = $matches[2][0];
				} else if ( preg_match_all( '/<!-- wp:embed {"url":"([^"]*(youtube|vimeo|dailymotion|facebook)[^"]*)"/is', $post_text, $matches ) ) {
					$img = $matches[1][0];
				} else if ( preg_match_all( '/<iframe.+src=[\'"]([^\'"]+(youtube|vimeo|dailymotion|facebook)[^\'"]+)[\'"][^>]*>/is', $post_text, $matches ) ) {
					$img = $matches[1][0];
				}
			} else {
				$img = planty_get_tag( $post_text, '<video', '</video>' );
				if ( empty( $img ) ) {
					$sc = planty_get_tag( $post_text, '[video', '[/video]' );
					if ( empty( $sc ) ) {
						$sc = planty_get_tag( $post_text, '[trx_widget_video', '' );
					}
					if ( ! empty( $sc ) ) {
						$img = do_shortcode( $sc );
					}
					if ( empty( $img ) && preg_match_all( '/<!-- wp\\:trx-addons\\/video.+"link"\\:"([^"]*)"/is', $post_text, $matches ) ) {
						$img = planty_get_embed_video( $matches[1][0] );
					}
					if ( empty( $img ) && preg_match_all( '/<!-- wp:core-embed\\/(youtube|vimeo|dailymotion|facebook).+"url":"([^"]*)"/is', $post_text, $matches ) ) {
						$img = planty_get_embed_video( $matches[2][0] );	// , true
					}
					if ( empty( $img ) && preg_match_all( '/<!-- wp:embed {"url":"([^"]*(youtube|vimeo|dailymotion|facebook)[^"]*)"/is', $post_text, $matches ) ) {
						$img = planty_get_embed_video( $matches[1][0] );	// , true
					}
					if ( empty( $img ) && preg_match_all( '/(<iframe.+src=[\'"][^\'"]+(youtube|vimeo|dailymotion|facebook)[^\'"]+[\'"][^>]*>[^<]*<\\/iframe>)/is', $post_text, $matches ) ) {
						$img = $matches[1][0];
					}
				}
			}
		}
		return apply_filters( 'planty_filter_get_post_video', $img );
	}
}

if ( ! function_exists( 'planty_get_post_video_list' ) ) {
	/**
	 * Return a list of videos from the post options.
	 *
	 * @param array $args  Optional. An array with filtering arguments. Default is an empty array.
	 *
	 * @return array       An array with the video entries in the format:
	 *                     [ [ 'url' => 'video1_url or empty', 'embed' => 'video1_embed_code or empty' ],
	 *                       [ 'url' => 'video2_url or empty', 'embed' => 'video2_embed_code or empty' ],
	 *                       ...
	 *                     ]
	 */
	function planty_get_post_video_list( $args=array() ) {
		return function_exists( 'trx_addons_get_post_video_list' ) ? trx_addons_get_post_video_list( $args ) : array();
	}
}

if ( ! function_exists( 'planty_get_post_video_list_first' ) ) {
	/**
	 * Return a first video from the post options.
	 *
	 * @param bool  $src   Optional. If true - only the video URL must be returned, else - an array with video settings.
	 *                     Default is false.
	 * @param array $args  Optional. An array with filtering arguments. Default is an empty array.
	 *
	 * @return array       An array with the first video entry in the format:
	 *                     [ 'url' => 'video1_url or empty', 'embed' => 'video1_embed_code or empty' ]
	 *                     or the URL only
	 */
	function planty_get_post_video_list_first( $src = false, $args = array() ) {
		return function_exists( 'trx_addons_get_post_video_list_first' ) ? trx_addons_get_post_video_list_first( $src, $args ) : '';
	}
}

if ( ! function_exists( 'planty_get_post_iframe' ) ) {
	/**
	 * Search the first tag with the inner frame in the post content
	 * and return a value of the attribute 'src' or a whole tag (if a second parameter is false).
	 *
	 * @param string $post_text  Optional. A post content to search a tag.
	 *                           If omitted or empty - a current post content is used.
	 * @param bool $src          Optional. If true - return only the value of the attribute 'src',
	 *                           else - return a whole tag. Default is true.
	 *
	 * @return string           Founded URL (or a whole tag) or an empty string if found nothing.
	 */
	function planty_get_post_iframe( $post_text = '', $src = true ) {
		global $post;
		$img = '';
		$tag = planty_get_embed_video_tag_name();
		if ( empty( $post_text ) ) {
			$post_text = $post->post_content;
		}
		if ( $src ) {
			if ( preg_match_all( '/<' . esc_html( $tag ) . '.+src=[\'"]([^\'"]+)[\'"][^>]*>/is', $post_text, $matches ) ) {
				$img = $matches[1][0];
			}
		} else {
			$img = planty_get_tag( $post_text, '<' . esc_html( $tag ), '</' . esc_html( $tag ) . '>' );
		}
		return apply_filters( 'planty_filter_get_post_iframe', $img );
	}
}

if ( ! function_exists( 'planty_get_embed_video' ) ) {
	/**
	 * Return a html layout with the embed video tag.
	 *
	 * @param string $video         An URL of the video to embed.
	 * @param bool   $use_wp_embed  Optional. Use an object $wp_embed to get layout or build layout with a theme-specific algorithm.
	 *
	 * @return string               An embed layout from the video URL.
	 */
	function planty_get_embed_video( $video, $use_wp_embed = false, $args = array() ) {
		global $wp_embed;
		if ( $use_wp_embed && is_object( $wp_embed ) ) {
			$embed_video = do_shortcode( $wp_embed->run_shortcode( '[embed]' . trim( $video ) . '[/embed]' ) );
		} else if ( planty_is_from_uploads( $video ) ) {
			$embed_video  = '<video'
								. ' src="' . esc_url( $video ) . '"'
								. ( ! empty( $args['controls'] ) ? ' controls="controls"' : '' )
								. ( ! empty( $args['loop'] ) ? ' loop="loop"' : '' )
								. ( ! empty( $args['mute'] ) ? ' muted="muted"' : '' )
								. ( ! empty( $args['autoplay'] ) ? ' autoplay="autoplay"' : '' )
								. ( ! empty( $args['autoplay'] ) ? ' playsinline="playsinline"' : '' )
								. ' preload="metadata"'
							. '></video>';
		} else {
			// Video link from Youtube
			if ( strpos( $video, 'youtu.be/' ) !== false || strpos( $video, 'youtube.com/' ) !== false ) {
				$video = str_replace(
							array(
								'/shorts/',							// Youtube Shorts link
								'/watch?v=',						// Youtube watch link
								'/youtu.be/',						// Youtube share link
							),
							array(
								'/embed/',
								'/embed/',
								'/www.youtube.com/embed/',
							),
							$video
						);
				$video = planty_add_to_url( $video, planty_get_embed_video_parameters() );
			}
			// Video link from Vimeo
			if ( strpos( $video, 'player.vimeo.com' ) === false ) {
				$video = str_replace(
							array(
								'vimeo.com/',
							),
							array(
								'player.vimeo.com/video/',
							),
							$video
						);
			}
			// Video link from Dailymotion
			$video = str_replace(
						array(
							'dai.ly/',							// DailyMotion.com video link
							'dailymotion.com/video/',			// DailyMotion.com page link 
						),
						array(
							'dailymotion.com/embed/video/',
							'dailymotion.com/embed/video/',
						),
						$video
					);
			// Video link from Facebook
			$fb = strpos($video, 'facebook.com/');
			if ( $fb !== false ) {
				$video = substr( $video, 0, $fb ) . 'facebook.com/plugins/video.php?href=' . urlencode($video);
			}
			// Video from TikTok
			$video = preg_replace( '/https?:\/\/(www\.)?tiktok\.com\/@([^\/]+)\/video\/(\d+)/i', 'https://www.tiktok.com/embed/v2/$3', $video );
			$is_tiktok = strpos( $video, 'tiktok.com/embed/v2' ) !== false;

			// Make an embed tag
			$tag   = planty_get_embed_video_tag_name();
			// Calc video width and height (with ratio 16:9)
			$ratio  = apply_filters( 'planty_filter_video_ratio', $is_tiktok ? '9:21' : '16:9' );
			$parts  = explode( ':', $ratio );
			$width  = $is_tiktok ? apply_filters( 'planty_filter_video_width', 325, 'tiktok' ) : planty_get_content_width();
			$height = round( $width / $parts[0] * $parts[1] );
			$dim    = apply_filters( 'planty_filter_video_dimensions', array(
									'width' => $width,
									'height' => $height
									), $ratio );
			$embed_video  = '<' . esc_html( $tag )
								. ' src="' . esc_url( $video ) . '"'
								//. ' allow="autoplay"'
								. ' width="' . esc_attr( $dim['width'] ) . '"'
								. ' height="' . esc_attr( $dim['height'] ) . '"'
								//. ' frameborder="0"'
								. '>'
							. '</' . esc_html( $tag ) . '>';
		}
		return $embed_video;
	}
}

if ( ! function_exists( 'planty_get_embed_video_tag_name' ) ) {
	/**
	 * Return a name of the tag-container to embed video.
	 *
	 * @return string  A tag name to embed video.
	 */
	function planty_get_embed_video_tag_name() {
		return strrev( 'e'			// A container name for embed video
						. 'mar'
						. 'fi' );
	}
}

if ( ! function_exists( 'planty_get_embed_video_parameters' ) ) {
	/**
	 * Return an array with a query parameters for an embed video.
	 *
	 * @return array  An array with parameters for an embed video.
	 */
	function planty_get_embed_video_parameters() {
		return array(
				'feature'        => 'oembed',
				'wmode'          => 'transparent',
				'origin'         => esc_url( home_url() ),
				'widgetid'       => 1,
				'enablejsapi'    => 1,
				'disablekb'      => 1,
				'modestbranding' => 1,
				'iv_load_policy' => 3,
				'rel'            => 0,
				'showinfo'       => 0,
				'playsinline'    => 1,
		);
	}
}

if ( ! function_exists( 'planty_make_video_autoplay' ) ) {
	/**
	 * Add a feature 'autoplay' to the tag <video> or to the tag with an embeded video.
	 *
	 * @param string $video  A string with the html layout to search a video and add the 'autoplay' behaviour to it.
	 * @param bool $muted    Optional. Make a founded video muted (need for modern browser to enable autoplay when
	 *                       a current page is loaded).
	 *
	 * @return string        A processed html layout.
	 */
	function planty_make_video_autoplay( $video, $muted = false ) {
		if ( strpos( $video, '<video' ) !== false ) {
			if ( strpos( $video, 'autoplay' ) === false ) {
				$video = str_replace(
									'<video',
									'<video autoplay="autoplay" onloadeddata="' . ( $muted ? 'this.muted=true;' : '' ) . 'this.play();"'
										. ( $muted
												? ' muted="muted" loop="loop" playsinline="playsinline"'
												: ' controls="controls" loop="loop"'
											),
									$video
									);
				if ( $muted ) {
					$video = str_replace( 'controls="controls"', '', $video );
				}
			}
		} else {
			$tag = planty_get_embed_video_tag_name();
			$pos = strpos( $video, '<' . esc_html( $tag ) );
			if ( false !== $pos ) {
				if ( preg_match( '/(<' . esc_html( $tag ) . '[^>]*src=[\'"])([^\'"]+)([\'"][^>]*>)(.*)/is', $video, $matches ) ) {
					$video = ( $pos > 0 ? substr( $video, 0, $pos ) : '' );
					if ( ! empty( $matches[2] ) ) {
						// Add parameters to the 'src'
						$matches[2] = planty_add_to_url(
							$matches[2],
							array_merge(
								array(
									'autoplay' => $muted && ( strpos( $matches[2], 'youtube.com' ) !== false || strpos( $matches[2], 'youtu.be' ) !== false )
													? 0
													: 1
								),
								$muted
									? array_merge(
										planty_get_embed_video_parameters(),
										array(
											'muted' => 1,
											'controls' => 0,
											'background' => 1,
											'autohide' => 1,
											'playsinline' => 1,
											'loop' => 1,
											)
										)
									: array()
							)
						);
					}
					$video .= $matches[1] . $matches[2] . $matches[3] . $matches[4];
					if ( strpos( $video, 'autoplay"' ) === false && strpos( $video, 'autoplay;' ) === false ) {
						$video = strpos( $video, ' allow="' ) !== false
									? str_replace( ' allow="', ' allow="autoplay;', $video )
									: str_replace( '<' . esc_html( $tag ) . ' ', '<' . esc_html( $tag ) . ' allow="autoplay" ', $video );
					}
				}
			}
		}
		return $video;
	}
}

if ( ! function_exists( 'planty_is_from_uploads' ) ) {
	/**
	 * Check if a specified image URL from the folder 'uploads' of the current site.
	 *
	 * @param string $url  An URL of the image to check it.
	 *
	 * @return bool        Return true if the image found in the folder 'uploads'.
	 */
	function planty_is_from_uploads( $url ) {
		$url          = planty_remove_protocol_from_url( $url );
		$parts        = explode( '?', $url );
		$url          = $parts[0];
		$uploads_info = wp_upload_dir();
		$uploads_url  = planty_remove_protocol_from_url( $uploads_info['baseurl'] );
		$uploads_dir  = $uploads_info['basedir'];
		return strpos( $url, $uploads_url ) !== false && file_exists( str_replace( $uploads_url, $uploads_dir, $url ) );
	}
}

if ( ! function_exists( 'planty_is_youtube_url' ) ) {
	/**
	 * Check if a specified URL from the Youtube.
	 *
	 * @param string $url  An URL of the video to check it.
	 *
	 * @return bool        Return true if the URL is from Youtube.
	 */
	function planty_is_youtube_url( $url ) {
		return strpos( $url, 'youtu.be' ) !== false || strpos( $url, 'youtube.com' ) !== false;
	}
}

if ( ! function_exists( 'planty_is_vimeo_url' ) ) {
	/**
	 * Check if a specified URL from the Vimeo.
	 *
	 * @param string $url  An URL of the video to check it.
	 *
	 * @return bool        Return true if the URL is from Vimeo.
	 */
	function planty_is_vimeo_url( $url ) {
		return strpos( $url, 'vimeo.com' ) !== false;
	}
}



/* Init WP Filesystem before the theme inited
------------------------------------------------------------------- */

if ( ! function_exists( 'planty_init_filesystem' ) ) {
	add_action( 'after_setup_theme', 'planty_init_filesystem', 0 );
	/**
	 * Init the object $wp_filesystem before the theme inited.
	 *
	 * @param bool $force  Optional. Init anyway or only if the plugin "ThemeREX Addons" is not activated. Default is false.
	 *
	 * @return bool        Return true if an object $wp_filesystem is inited successfull.
	 */
	function planty_init_filesystem( $force = false ) {
		if ( ! $force && function_exists( 'trx_addons_init_filesystem' ) ) {
			return true;
		}
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once trailingslashit( ABSPATH ) . 'wp-admin/includes/file.php';
		}
		if ( is_admin() ) {
			$url   = admin_url();
			$creds = false;
			// First attempt to get credentials.
			if ( function_exists( 'request_filesystem_credentials' ) ) {
				$creds = request_filesystem_credentials( $url, '', false, false, array() );
				if ( false === $creds ) {
					// If we comes here - we don't have credentials
					// so the request for them is displaying no need for further processing
					return false;
				}
			}

			// Now we got some credentials - try to use them.
			if ( ! WP_Filesystem( $creds ) ) {
				// Incorrect connection data - ask for credentials again, now with error message.
				if ( function_exists( 'request_filesystem_credentials' ) ) {
					request_filesystem_credentials( $url, '', true, false );
				}
				return false;
			}

			return true; // Filesystem object successfully initiated.
		} else {
			WP_Filesystem();
		}
		return true;
	}
}

if ( ! function_exists( 'planty_fpc' ) ) {
	/**
	 * Save a data to the specified file. If flag is 0 or omitted and file exists - it will be overwritten.
	 *
	 * @param string $file  A path to the file.
	 * @param string $data  A data to save.
	 * @param int $flag     Saving options. Use the constant FILE_APPEND to append data to the file instead overwrite it.
	 *
	 * @return false|int    A saved bytes number or false if error occurs.
	 *
	 * @throws Exception    Throw an Exception if the object $wp_filesystem is not initialized yet.
	 */
	function planty_fpc( $file, $data, $flag = 0 ) {
		if ( ! empty( $file ) ) {
			if ( function_exists( 'trx_addons_fpc' ) ) {
				return trx_addons_fpc( $file, $data, $flag );
			} else {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );
					// Attention! WP_Filesystem can't append the content to the file!
					// That's why we have to read the contents of the file into a string,
					// add new content to this string and re-write it to the file if parameter $flag == FILE_APPEND!
					return $wp_filesystem->put_contents( $file, ( FILE_APPEND == $flag && $wp_filesystem->exists( $file ) ? $wp_filesystem->get_contents( $file ) : '' ) . $data, false );
				} else {
					if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
						// Translators: Add the file name to the message
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Put contents to the file "%s" failed', 'planty' ), $file ) );
					}
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'planty_fgc' ) ) {
	/**
	 * Load a data from the specified file.
	 *
	 * @param string $file  A path to the file.
	 *
	 * @return string       A data loaded from the file.
	 *
	 * @throws Exception    Throw an Exception if the object $wp_filesystem is not initialized yet.
	 */
	function planty_fgc( $file ) {
		if ( ! empty( $file ) ) {
			if ( function_exists( 'trx_addons_fgc' ) ) {
				return trx_addons_fgc( $file );
			} else {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$file = substr( $file, 0, 2 ) == '//' ? planty_add_protocol( $file ) : str_replace( ABSPATH, $wp_filesystem->abspath(), $file );
					return $wp_filesystem->get_contents( $file );
				} else {
					if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
						// Translators: Add the file name to the message
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Get contents from the file "%s" failed', 'planty' ), $file ) );
					}
				}
			}
		}
		return '';
	}
}

if ( ! function_exists( 'planty_fga' ) ) {
	/**
	 * Load a data from the specified file and return an array with a text rows.
	 *
	 * @param string $file  A path to the file.
	 *
	 * @return array        An array with a text rows.
	 *
	 * @throws Exception    Throw an Exception if the object $wp_filesystem is not initialized yet.
	 */
	function planty_fga( $file ) {
		if ( ! empty( $file ) ) {
			if ( function_exists( 'trx_addons_fga' ) ) {
				return trx_addons_fga( $file );
			} else {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$file = str_replace( ABSPATH, $wp_filesystem->abspath(), $file );
					return $wp_filesystem->get_contents_array( $file );
				} else {
					if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
						// Translators: Add the file name to the message
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Get rows from the file "%s" failed', 'planty' ), $file ) );
					}
				}
			}
		}
		return array();
	}
}


if ( ! function_exists( 'planty_mkdir' ) ) {
	/**
	 * Create a new directory (folder) with the specified path.
	 *
	 * @param string $path  A path to the directory.
	 *
	 * @return bool         Return true if a directory created successfull, else return false.
	 *
	 * @throws Exception    Throw an Exception if the object $wp_filesystem is not initialized yet or if error occurs.
	 */
	function planty_mkdir( $path ) {
		if ( ! empty( $path ) ) {
			if ( function_exists( 'trx_addons_mkdir' ) ) {
				return trx_addons_mkdir( $path );
			} else {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$path = str_replace( ABSPATH, $wp_filesystem->abspath(), $path );
					if ( ! $wp_filesystem->is_dir( $path ) ) {
						// On Theme Unit Test requirements wp_mkdir_p( $path ) is used instead $wp_filesystem->mkdir( $path, FS_CHMOD_DIR )
						if ( ! wp_mkdir_p( $path ) ) {
							if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
								// Translators: Add the file name to the message
								throw new Exception( sprintf( esc_html__( 'Create a folder "%s" failed', 'planty' ), $path ) );
							}
						} else {
							return true;
						}
					} else {
						return true;
					}
				} else {
					if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
						// Translators: Add the file name to the message
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Create a folder "%s" failed', 'planty' ), $path ) );
					}
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'planty_unlink' ) ) {
	/**
	 * Remove a specified file or directory.
	 * Parameters $recursive and $type are deprecated since the theme version v.2.3.0.
	 *
	 * @param string $path      Path to the file or directory to be deleted.
	 * @param bool   $recursive Deprecated.
	 * @param string $type      Deprecated.
	 *
	 * @return bool             Return true if a file or directory deleted successfully.
	 *
	 * @throws Exception        Throw an Exception if the object $wp_filesystem is not initialized yet or if error occurs.
	 */
	function planty_unlink( $path, $recursive = true, $type = 'd' ) {
		if ( ! empty( $path ) ) {
			if ( function_exists( 'trx_addons_unlink' ) ) {
				return trx_addons_unlink( $path );
			} else {
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					$path = str_replace( ABSPATH, $wp_filesystem->abspath(), $path );
					return $wp_filesystem->delete( $path, true, $wp_filesystem->is_file( $path ) ? 'f' : 'd' );
				} else {
					if ( planty_is_on( planty_get_theme_option( 'debug_mode' ) ) ) {
						// Translators: Add the file name to the message
						throw new Exception( sprintf( esc_html__( 'WP Filesystem is not initialized! Delete a file/folder "%s" failed', 'planty' ), $path ) );
					}
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'planty_copy' ) ) {
	/**
	 * Copy a file or a whole folder tree (with subfolders) to the new destination.
	 * If in the new destination exists a file or folder with same name - it will be deleted before.
	 *
	 * @param string $src Path to the source file or directory.
	 * @param string $dst Path to the destination.
	 *
	 * @return bool       Return true if no error occurs.
	 *
	 * @throws Exception  Throw an Exception if the object $wp_filesystem is not initialized yet or if error occurs.
	 */
	function planty_copy( $src, $dst ) {
		global $wp_filesystem;
		if ( ! empty( $src ) && ! empty( $dst ) ) {
			if ( function_exists( 'trx_addons_copy' ) ) {
				return trx_addons_copy( $src, $dst );
			} else if ( function_exists( 'copy_dir' ) && isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
				$src = planty_prepare_path( $src );
				$dst = planty_prepare_path( $dst );
				return copy_dir( $src, $dst );
			} else {
				planty_unlink( $dst );
				if ( is_dir( $src ) ) {
					if ( ! is_dir( $dst ) ) {
						planty_mkdir( $dst );
					}
					$files = scandir( $src, SCANDIR_SORT_NONE );
					foreach ( $files as $file ) {
						if ( $file != "." && $file != ".." ) {
							planty_copy( "$src/$file", "$dst/$file" );
						}
					}
					return true;
				} else if ( file_exists( $src ) ) {
					return copy( $src, $dst );
				}
			}
		}
		return false;
	}
}

if ( ! function_exists( 'planty_unzip_file' ) ) {
	/**
	 * Init a $wp_filesystem (if need) and unzip file.
	 * 
	 * @param string $zip   A path to the zip-archive.
	 * @param string $dest  A path to the folder to unzip files in.
	 */
	function planty_unzip_file( $zip, $dest ) {
		if ( function_exists( 'trx_addons_unzip_file' ) ) {
			return trx_addons_unzip_file( $zip, $dest );
		}
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) || ! is_object( $wp_filesystem ) ) {
			planty_init_filesystem( true );
		}
		return unzip_file( $zip, $dest );
	}
}

if ( ! function_exists( 'planty_retrieve_json' ) ) {
	/**
	 * Get JSON from the specified url and return a decoded object or null
	 *
	 * @param string $url  An URL to receive a JSON data.
	 *
	 * @return mixed       A decoded object or null.
	 */
	function planty_retrieve_json( $url ) {
		return function_exists( 'trx_addons_retrieve_json' ) ? trx_addons_retrieve_json( $url ) : '';
	}
}

if ( ! function_exists( 'planty_esc' ) ) {
	/**
	 * Remove unsafe characters from the file or folder path
	 *
	 * @param string $name  A file path to clean.
	 *
	 * @return string       A clear file path.
	 */
	function planty_esc( $name ) {
		return str_replace( array( '\\', '~', '$', ':', ';', '+', '>', '<', '|', '"', "'", '`', "\xFF", "\x0A", "\x0D", '*', '?', '^' ), '/', trim( $name ) );
	}
}

if ( ! function_exists( 'planty_prepare_path' ) ) {
	/**
	 * Replace all directory separators to the single type (equal to the constant DIRECTORY_SEPARATOR)
	 *
	 * @param string $path  A file path to process directory separators.
	 *
	 * @return string       A processed file path.
	 */
	function planty_prepare_path( $path ) {
		return str_replace( array( '\\', '/' ), defined( 'DIRECTORY_SEPARATOR' ) ? DIRECTORY_SEPARATOR : '/', $path );
	}
}

if ( ! function_exists( 'planty_check_min_file' ) ) {
	/**
	 * Return a path to the .min version of file (if exists and filetime .min > filetime original) instead the original path.
	 *
	 * @param string $file  A path to the original file.
	 * @param string $dir   Optional. A directory with .min files. If omitted or empty - a directory of the original file is used.
	 *
	 * @return string       Return a path to the .min file
	 */
	function planty_check_min_file( $file, $dir = '' ) {
		if ( empty( $dir ) ) {
			$dir = dirname( $file );
		}
		if ( substr( $file, -3 ) == '.js' ) {
			if ( substr( $file, -7 ) != '.min.js' && planty_is_off( planty_get_theme_option( 'debug_mode' ) ) ) {
				$dir      = trailingslashit( $dir );
				$file_min = substr( $file, 0, strlen( $file ) - 3 ) . '.min.js';
				if ( file_exists( $dir . $file_min ) && filemtime( $dir . $file ) <= filemtime( $dir . $file_min ) ) {
					$file = $file_min;
				}
			}
		} elseif ( substr( $file, -4 ) == '.css' ) {
			if ( substr( $file, -8 ) != '.min.css' && planty_is_off( planty_get_theme_option( 'debug_mode' ) ) ) {
				$dir      = trailingslashit( $dir );
				$file_min = substr( $file, 0, strlen( $file ) - 4 ) . '.min.css';
				if ( file_exists( $dir . $file_min ) && filemtime( $dir . $file ) <= filemtime( $dir . $file_min ) ) {
					$file = $file_min;
				}
			}
		}
		return $file;
	}
}

if ( ! function_exists( 'planty_get_file_dir' ) ) {
	/**
	 * Check if a file present in the child theme and return a path (url) to it,
	 * else return a path (url) to the file in the main theme dir.
	 *
	 * If a file is not exists (for example, a component is not included in the theme's light version) - return an empty string.
	 *
	 * @param string $file      A path to the file to check it in the child-theme directory.
	 * @param bool $return_url  Optional. If true - a file URL will be returned instead the file path. Default is false.
	 *
	 * @return string           A path or URL to the file in the child-theme directory (check it first)
	 *                          or in the main theme directory. If a file is not exists - return an empty string.
	 */
	function planty_get_file_dir( $file, $return_url = false ) {
		// Use new WordPress functions (if present)
		if ( function_exists( 'get_theme_file_path' ) && ! PLANTY_ALLOW_SKINS && ! planty_get_theme_setting( 'check_min_version', false ) ) {
			$dir = get_theme_file_path( $file );
			$dir = file_exists( $dir )
						? ( $return_url ? get_theme_file_uri( $file ) : $dir )
						: '';

		// Otherwise (on WordPress older then 4.7.0) or theme use .min versions of .js and .css or theme use skins
		} else {
			if ( '/' == $file[0] ) {
				$file = substr( $file, 1 );
			}
			$dir       = '';
			$theme_dir = apply_filters( 'planty_filter_get_theme_file_dir', '', $file, $return_url );
			if ( '' != $theme_dir ) {
				$dir = $theme_dir;
			} elseif ( PLANTY_CHILD_DIR != PLANTY_THEME_DIR && file_exists( PLANTY_CHILD_DIR . ( $file ) ) ) {
				$dir = ( $return_url ? PLANTY_CHILD_URL : PLANTY_CHILD_DIR ) . planty_check_min_file( $file, PLANTY_CHILD_DIR );
			} elseif ( file_exists( PLANTY_THEME_DIR . ( $file ) ) ) {
				$dir = ( $return_url ? PLANTY_THEME_URL : PLANTY_THEME_DIR ) . planty_check_min_file( $file, PLANTY_THEME_DIR );
			}
		}
		return $dir;
	}
}

if ( ! function_exists( 'planty_get_file_url' ) ) {
	/**
	 * Check if a file present in the child theme and return its URL,
	 * else return an URL to the file in the main theme dir.
	 *
	 * If a file is not exists (for example, a component is not included in the theme's light version) - return an empty string.
	 *
	 * @param string $file      A path to the file to check it in the child-theme directory.
	 *
	 * @return string           An URL to the file in the child-theme directory (check it first)
	 *                          or in the main theme directory. If a file is not exists - return an empty string.
	 */
	function planty_get_file_url( $file ) {
		return planty_get_file_dir( $file, true );
	}
}

if ( ! function_exists( 'planty_get_file_ext' ) ) {
	/**
	 * Return a file extension from the full name (path).
	 *
	 * @param string $file  A file name or path to get an extension part.
	 *
	 * @return string       A file extension - a part of the name after the last '.' (dot).
	 */
	function planty_get_file_ext( $file ) {
		$ext = pathinfo( $file, PATHINFO_EXTENSION );
		return empty( $ext ) ? '' : $ext;
	}
}

if ( ! function_exists( 'planty_get_file_name' ) ) {
	/**
	 * Return a file name from the full name (path).
	 *
	 * @param string $file       A file name or path to get a name part.
	 * @param bool $without_ext  Optional. If true - return a file name with an extension,
	 *                           else return only the file name without extension.
	 *
	 * @return string            A file name with or without the extension.
	 */
	function planty_get_file_name( $file, $without_ext = true ) {
		$parts = pathinfo( $file );
		return ! empty( $parts['filename'] ) && $without_ext ? $parts['filename'] : $parts['basename'];
	}
}

if ( ! function_exists( 'planty_get_folder_dir' ) ) {
	/**
	 * Check if a folder present in the child theme and return a path (url) to it,
	 * else return a path (url) to the folder in the main theme dir.
	 *
	 * If a folder is not exists (for example, a component is not included in the theme's light version) - return an empty string.
	 *
	 * @param string $file      A path to the folder to check it in the child-theme directory.
	 * @param bool $return_url  Optional. If true - a folder URL will be returned instead the folder path. Default is false.
	 *
	 * @return string           A path or URL to the folder in the child-theme directory (check it first)
	 *                          or in the main theme directory. If a folder is not exists - return an empty string.
	 */
	function planty_get_folder_dir( $folder, $return_url = false ) {
		if ( '/' == $folder[0] ) {
			$folder = substr( $folder, 1 );
		}
		$dir       = '';
		$theme_dir = apply_filters( 'planty_filter_get_theme_folder_dir', '', $folder, $return_url );
		if ( '' != $theme_dir ) {
			$dir = $theme_dir;
		} elseif ( PLANTY_CHILD_DIR != PLANTY_THEME_DIR && is_dir( PLANTY_CHILD_DIR . ( $folder ) ) ) {
			$dir = ( $return_url ? PLANTY_CHILD_URL : PLANTY_CHILD_DIR ) . ( $folder );
		} elseif ( is_dir( PLANTY_THEME_DIR . ( $folder ) ) ) {
			$dir = ( $return_url ? PLANTY_THEME_URL : PLANTY_THEME_DIR ) . ( $folder );
		}
		return apply_filters( 'planty_filter_get_folder_dir', $dir, $folder, $return_url );
	}
}

if ( ! function_exists( 'planty_get_folder_url' ) ) {
	/**
	 * Check if a folder present in the child theme and return an URL to it,
	 * else return an URL to the folder in the main theme dir.
	 *
	 * If a folder is not exists (for example, a component is not included in the theme's light version) - return an empty string.
	 *
	 * @param string $file      A path to the folder to check it in the child-theme directory.
	 *
	 * @return string           An URL to the folder in the child-theme directory (check it first)
	 *                          or in the main theme directory. If a folder is not exists - return an empty string.
	 */
	function planty_get_folder_url( $folder ) {
		return planty_get_folder_dir( $folder, true );
	}
}

if ( ! function_exists( 'planty_merge_js' ) ) {
	/**
	 * Merge all separate JS-files to the single file to increase a page upload speed.
	 *
	 * @param string $to   A path to the merged file.
	 * @param array $list  A list of the separate files to be merged.
	 */
	function planty_merge_js( $to, $list ) {
		$s = '';
		foreach ( $list as $f ) {
			$s .= planty_fgc( planty_get_file_dir( $f ) );
		}
		if ( '' != $s ) {
			$file_dir = planty_get_file_dir( $to );
			if ( empty( $file_dir ) && strpos( $to, '-full.js' ) !== false ) {
				$file_dir = planty_get_file_dir( str_replace( '-full.js', '.js', $to ) );
				if ( ! empty( $file_dir ) ) {
					$file_dir = str_replace( '.js', '-full.js', $file_dir );
				}
			}
			planty_fpc( $file_dir,
				'/* '
				. strip_tags( __( "ATTENTION! This file was generated automatically! Don't change it!!!", 'planty' ) )
				. "\n----------------------------------------------------------------------- */\n"
				. apply_filters( 'planty_filter_js_output', apply_filters( 'planty_filter_prepare_js', $s, true ), $to )
			);
		}
	}
}

if ( ! function_exists( 'planty_merge_css' ) ) {
	/**
	 * Merge all separate CSS-files to the single file to increase a page upload speed.
	 *
	 * @param string $to             A path to the merged file.
	 * @param array $list            A list of the separate files to be merged.
	 * @param bool $need_responsive  Optional. If true - process responsive files and collect all media-queries. Default is false.
	 */
	function planty_merge_css( $to, $list, $need_responsive = false ) {
		if ( $need_responsive ) {
			$responsive = apply_filters( 'planty_filter_responsive_sizes', planty_storage_get( 'responsive' ) );
		}
		$sizes  = array();
		$output = '';
		foreach ( $list as $f ) {
			$fdir = planty_get_file_dir( $f );
			if ( '' != $fdir ) {
				$css = planty_fgc( $fdir );
				if ( $need_responsive ) {
					$pos = 0;
					while( false !== $pos ) {
						$pos = strpos($css, '@media' );
						if ( false !== $pos ) {
							$pos += 7;
							$pos_lbrace = strpos( $css, '{', $pos );
							$cnt = 0;
							$in_comment = false;
							for ( $pos_rbrace = $pos_lbrace + 1; $pos_rbrace < strlen( $css ); $pos_rbrace++ ) {
								if ( $in_comment ) {
									if ( substr( $css, $pos_rbrace, 2 ) == '*/' ) {
										$pos_rbrace++;
										$in_comment = false;
									}
								} else if ( substr( $css, $pos_rbrace, 2 ) == '/*' ) {
									$pos_rbrace++;
									$in_comment = true;
								} else if ( substr( $css, $pos_rbrace, 1 ) == '{' ) {
									$cnt++;
								} elseif ( substr( $css, $pos_rbrace, 1 ) == '}' ) {
									if ( $cnt > 0 ) {
										$cnt--;
									} else {
										break;
									}
								}
							}
							$media = trim( substr( $css, $pos, $pos_lbrace - $pos ) );
							if ( empty( $sizes[ $media ] ) ) {
								$sizes[ $media ] = '';
							}
							$sizes[ $media ] .= "\n\n" . apply_filters( 'planty_filter_merge_css', substr( $css, $pos_lbrace + 1, $pos_rbrace - $pos_lbrace - 1 ) );
							$css = substr( $css, $pos_rbrace + 1);
						}
					}
				} else {
					$output .= "\n\n" . apply_filters( 'planty_filter_merge_css', $css );
				}
			}
		}
		if ( $need_responsive ) {
			foreach ( $responsive as $k => $v ) {
				$media = ( ! empty( $v['min'] ) ? "(min-width: {$v['min']}px)" : '' )
						. ( ! empty( $v['min'] ) && ! empty( $v['max'] ) ? ' and ' : '' )
						. ( ! empty( $v['max'] ) ? "(max-width: {$v['max']}px)" : '' );
				if ( ! empty( $sizes[ $media ] ) ) {
					$output .= "\n\n"
							// Translators: Add responsive size's name to the comment
							. strip_tags( sprintf( __( '/* SASS Suffix: --%s */', 'planty' ), $k ) )
							. "\n"
							. "@media {$media} {\n"
								. $sizes[ $media ]
							. "\n}\n";
					unset( $sizes[ $media ] );
				}
			}
			if ( count( $sizes ) > 0 ) {
				$output .= "\n\n"
						. strip_tags( __( '/* Unknown Suffixes: */', 'planty' ) );
				foreach ( $sizes as $k => $v ) {
					$output .= "\n\n"
							. "@media {$k} {\n"
								. $v
							. "\n}\n";
				}
			}
		}
		if ( '' != $output ) {
			$file_dir = planty_get_file_dir( $to );
			if ( empty( $file_dir ) && strpos( $to, '-full.css' ) !== false ) {
				$file_dir = planty_get_file_dir( str_replace( '-full.css', '.css', $to ) );
				if ( ! empty( $file_dir ) ) {
					$file_dir = str_replace( '.css', '-full.css', $file_dir );
				}
			}
			planty_fpc( $file_dir,
				'/* ' 
				. strip_tags( __("ATTENTION! This file was generated automatically! Don't change it!!!", 'planty') ) 
				. "\n----------------------------------------------------------------------- */\n"
				. apply_filters( 'planty_filter_css_output', apply_filters( 'planty_filter_prepare_css', $output, true ), $to )
			);
		}
	}
}

if ( ! function_exists( 'planty_filter_merge_css' ) ) {
	add_filter( 'planty_filter_merge_css', 'planty_filter_merge_css' );
	/**
	 * Replace all relative paths in the merged CSS-file to resolve url() attributes.
	 *
	 * Hooks:
	 *
	 * add_filter( 'planty_filter_merge_css', 'planty_filter_merge_css' );
	 *
	 * @param string $css  A merged CSS content to resolve paths.
	 *
	 * @return string      A processed CSS content
	 */
	function planty_filter_merge_css( $css ) {
		return str_replace( '../../../../', '../../../', $css);
	}
}

if ( ! function_exists( 'planty_merge_sass' ) ) {
	/**
	 * Deprecated. Merge all separate SASS files to the single SASS file.
	 *
	 * @param string $to               A path to the merged file.
	 * @param array  $list             An array with a separate SASS files.
	 * @param bool   $need_responsive  Optional. If true - process responsive files and collect all media-queries. Default is false.
	 * @param string $root             Optional. A relative path to the theme root folder from the merged file to resolve url() attributes.
	 */
	function planty_merge_sass( $to, $list, $need_responsive = false, $root = '../' ) {
		if ( $need_responsive ) {
			$responsive = apply_filters( 'planty_filter_responsive_sizes', planty_storage_get( 'responsive' ) );
		}
		$sass                = array(
			'import' => '',
			'sizes'  => array(),
		);
		$save                = false;
		$sass_special_symbol = '@';
		$sass_required       = "{$sass_special_symbol}required";
		$sass_include        = "{$sass_special_symbol}include";
		$sass_import         = "{$sass_special_symbol}import";
		foreach ( $list as $f ) {
			$add  = false;
			$fdir = planty_get_file_dir( $f );
			if ( '' != $fdir ) {
				if ( $need_responsive ) {
					$css = planty_fgc( $fdir );
					if ( strpos( $css, $sass_required ) !== false ) {
						$add = true;
					}
					foreach ( $responsive as $k => $v ) {
						if ( preg_match( "/([\d\w\-_]+\-\-{$k})\(/", $css, $matches ) ) {
							$sass['sizes'][ $k ] = ( ! empty( $sass['sizes'][ $k ] )
														? $sass['sizes'][ $k ] 
														: '' 
													)
													. "\t{$sass_include} {$matches[1]}();\n";
							$add                 = true;
						}
					}
				} else {
					$add = true;
				}
			}
			if ( $add ) {
				$sass['import'] .= apply_filters( 'planty_filter_sass_import', "{$sass_import} \"{$root}{$f}\";\n", $f );
				$save            = true;
			}
		}
		if ( $save ) {
			$output = '/* '
					. strip_tags( __( "ATTENTION! This file was generated automatically! Don't change it!!!", 'planty' ) )
					. "\n----------------------------------------------------------------------- */\n"
					. $sass['import'];
			if ( $need_responsive ) {
				foreach ( $responsive as $k => $v ) {
					if ( ! empty( $sass['sizes'][ $k ] ) ) {
						$output .= "\n\n"
								// Translators: Add responsive size's name to the comment
								. strip_tags( sprintf( __( '/* SASS Suffix: --%s */', 'planty' ), $k ) )
								. "\n"
								. '@media ' . ( ! empty( $v['min'] ) ? "(min-width: {$v['min']}px)" : '' )
											. ( ! empty( $v['min'] ) && ! empty( $v['max'] ) ? ' and ' : '' )
											. ( ! empty( $v['max'] ) ? "(max-width: {$v['max']}px)" : '' )
											. " {\n"
												. $sass['sizes'][ $k ]
											. "}\n";
					}
				}
			}
			planty_fpc( planty_get_file_dir( $to ), apply_filters( 'planty_filter_sass_output', $output, $to ) );
		}
	}
}





/* URL manipulations
----------------------------------------------------------------------------------------------------- */

if ( ! function_exists( 'planty_get_domain_from_url' ) ) {
	/**
	 * Return a domain from the URL.
	 *
	 * @param string $url  An URL to get a domain part.
	 *
	 * @return string      A domain part.
	 */
	function planty_get_domain_from_url( $url ) {
		$pos = strpos( $url, '//' );
		if ( $pos !== false ) {
			$url = substr( $url, $pos + 2 );
		}
		$pos = strpos( $url, '/' );
		if ( $pos !== false ) {
			$url = substr( $url, 0, $pos );
		}
		return $url;
	}
}

if ( ! function_exists( 'planty_add_protocol' ) ) {
	/**
	 * Add a site protocol to the URL.
	 *
	 * @param string $url  An URL to add a protocol.
	 *
	 * @return string      An URL with a site protocol.
	 */
	function planty_add_protocol( $url ) {
		return substr( $url, 0, 2 ) == '//' ? planty_get_protocol() . ':' . $url : $url;
	}
}

if ( ! function_exists( 'planty_remove_protocol_from_url' ) ) {
	/**
	 * Remove a protocol from the URL.
	 *
	 * @param string $url     An URL to remove a protocol.
	 * @param bool $complete  Optional. If true - remove 'protocol:' and '//', else remove a 'protocol:' only.
	 *
	 * @return string         A processed string.
	 */
	function planty_remove_protocol_from_url( $url, $complete = true ) {
		return preg_replace( '/(http[s]?:)?' . ( $complete ? '\\/\\/' : '' ) . '/', '', $url );
	}
}

if ( ! function_exists( 'planty_add_to_url' ) ) {
	/**
	 * Add parameters to the URL.
	 *
	 * @param string $url  An URL to add parameters.
	 * @param array  $prm  An associative array with parameters.
	 *
	 * @return string      An URL with parameters.
	 */
	function planty_add_to_url( $url, $prm ) {
		if ( is_array( $prm ) && count( $prm ) > 0 ) {
			$parts = explode( '?', $url );
			$params = array();
			if ( ! empty( $parts[1] ) ) {
				parse_str( $parts[1], $params );
			}
			$params = planty_array_merge( $params, $prm );
			$url = $parts[0];
			$separator = '?';
			foreach( $params as $k => $v ) {
				$url .= $separator . urlencode( $k ) . '=' . urlencode( $v );
				$separator = '&';
			}
		}
		return $url;
	}
}

if ( ! function_exists( 'planty_is_url' ) ) {
	/**
	 * Return true if a string is in the URL format.
	 *
	 * @param string $url  A string to check
	 *
	 * @return bool        true if a string is in the URL format.
	 */
	function planty_is_url( $url ) {
		return is_string( $url ) && ( strpos( $url, '//' ) === 0 || strpos( $url, '://' ) !== false );
	}
}

if ( ! function_exists( 'planty_is_external_url' ) ) {
	/**
	 * Check if the specified URL is an external URL (to any resource not from the current site).
	 *
	 * @param string $url  An URL to check.
	 *
	 * @return bool        true if the specified URL is not from the current site.
	 */
	function planty_is_external_url( $url ) {
		return planty_is_url( $url ) && strpos( $url, planty_remove_protocol_from_url( apply_filters( 'trx_addons_filter_home_url', get_home_url() ), true ) ) === false;
	}
}

if ( ! function_exists( 'planty_url_to_local_path' ) ) {
	/**
	 * Convert URL to the local path.
	 * 
	 * @param $url  An URL to be converted to the local path.
	 * 
	 * @return string  A local path from URL or an empty string (if an URL is external or a file is not exists).
	 */
	function planty_url_to_local_path( $url ) {
		$path = '';
		// Remove scheme from url
		$url = planty_remove_protocol_from_url( $url );
		// Get upload path & dir
		$upload_info = wp_upload_dir();
		// Where check file
		$locations = array(
			'uploads' => array(
				'dir' => $upload_info['basedir'],
				'url' => planty_remove_protocol_from_url( $upload_info['baseurl'] )
				),
			'child' => array(
				'dir' => PLANTY_CHILD_DIR,
				'url' => planty_remove_protocol_from_url( PLANTY_CHILD_URL )
				),
			'theme' => array(
				'dir' => PLANTY_THEME_DIR,
				'url' => planty_remove_protocol_from_url( PLANTY_THEME_URL )
				)
			);
		// Search a file in locations
		foreach( $locations as $key => $loc ) {
			// Check if $url is in location
			if ( false === strpos( $url, $loc['url'] ) ) continue;
			// Get a path from the URL
			$path = str_replace( $loc['url'], $loc['dir'], $url );
			// Check if a file exists
			if ( file_exists( $path ) ) {
				break;
			}
			$path = '';
		}
		return $path;
	}
}