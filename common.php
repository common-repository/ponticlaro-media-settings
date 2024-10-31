<?php
class PonticlaroMediaCommon {
	
	function image_send_to_editor($html, $id, $override=array(), $override_action=array(), $override_vals=array()) {
		$attribs = array('alt', 'title', 'class', 'height', 'width', 'rel');

		$image_path 	= get_option('ponticlaro_image_path');
		
		$image_attribs  = array_merge(is_array($arg = get_option('ponticlaro_image_attribs')) ? $arg : array(), $override);
		$image_vals     = array_merge(is_array($arg = get_option('ponticlaro_image_vals')) ? $arg : array(), $override_action);
		$image_val_custom = array_merge(is_array($arg = get_option('ponticlaro_image_val_custom')) ? $arg : array(), $override_vals);

		if (!is_feed() && ($image_path == '/')) {
			$html = preg_replace(array('@<([img|a])(.*)([src|href])=("|\')http://([^/]+)/@iU'), array('<${1}${2}${3}=${4}/'), $html);
		} elseif (strlen($image_path) > 7) {
			$html = preg_replace(array('@<([img|a])(.*)([src|href])=("|\')http://([^/]+)/@iU'), array('<${1}${2}${3}=${4}'.$image_path), $html);
		}
		foreach ($attribs as $attr) if ($image_attribs[$attr]) {
			if ($image_vals[$attr] == 'custom') {
				if (in_array($attr, array('alt', 'title'))) {
					$html = $this->make_attribute($html, $attr, $image_val_custom[$attr]);
				} else {
					$html = $this->make_attribute($html, $attr, $image_val_custom[$attr], true);
				}
			} elseif ($image_vals[$attr] == 'remove') {
				$html = preg_replace('@<(img|a)(.*)'.$attr.'="(.*)"@U', '<${1}${2}', $html);
			}
		}
		$html = preg_replace('/  /', ' ', $html);
		
		if (preg_match('@/wp-admin/@', $_SERVER['REQUEST_URI']) && $_COOKIE['insertshortcode']) {
			if (wp_attachment_is_image($id)) {
				$html = preg_replace('@<img(.*)src="[^"]*" ([^>]*)/>@', '[media id='.$id.' ${1}${2}]', $html);
			} else {
				$html = preg_replace('@<a(.*)href=\'[^\']*\'([^>]*)>(.*)</a>@', '[media id='.$id.' ${1}${2}]${3}[/media]', $html);
			}
		}
		$html = preg_replace('@( [a-zA-Z]*="")@', '', $html);
		
		return $html;
	}
	
	function make_attribute($html, $attr, $value, $append=false) {
		if (!preg_match('@<(img|a)(.*)'.$attr.'@U', $html)) {
			$html = preg_replace('@<(img|a) @U', '<${1} '.$attr.'="" ', $html);
		}
		if ($append) {
			$html = preg_replace('@<(img|a)(.*)'.$attr.'="(.*)"@U', '<${1}${2}'.$attr.'="${3} '.$value.'"', $html);
		} else {
			$html = preg_replace('@<(img|a)(.*)'.$attr.'="(.*)"@U', '<${1}${2}'.$attr.'="'.$value.'"', $html);
		}
		
		// make sure src is first
		$html = preg_replace('@<img(.*)( src="[^"]*")([^>]*)/>@', '<img${2} ${1}${3}/>', $html);
		
		// clean up spaces between attributes	
		$html = preg_replace('@  @', ' ', $html);

		return $html;
	}
}

/**
 * Replace array_diff_key()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.array_diff_key
 * @author      Tom Buskens <ortega@php.net>
 * @version     $Revision$
 * @since       PHP 5.0.2
 * @require     PHP 4.0.0 (user_error)
 */
function php_compat_array_diff_key()
{
    $args = func_get_args();
    if (count($args) < 2) {
        user_error('Wrong parameter count for array_diff_key()', E_USER_WARNING);
        return;
    }

    // Check arrays
    $array_count = count($args);
    for ($i = 0; $i !== $array_count; $i++) {
        if (!is_array($args[$i])) {
            user_error('array_diff_key() Argument #' .
                ($i + 1) . ' is not an array', E_USER_WARNING);
            return;
        }
    }

    $result = $args[0];
    if (function_exists('array_key_exists')) {
        // Optimize for >= PHP 4.1.0
        foreach ($args[0] as $key => $value) {
            for ($i = 1; $i !== $array_count; $i++) {
                if (array_key_exists($key,$args[$i])) {
                    unset($result[$key]);
                    break;
                }
            }
        }
    } else {
        foreach ($args[0] as $key1 => $value1) {
            for ($i = 1; $i !== $array_count; $i++) {
                foreach ($args[$i] as $key2 => $value2) {
                    if ((string) $key1 === (string) $key2) {
                        unset($result[$key2]);
                        break 2;
                    }
                }
            }
        }
    }
    return $result; 
}


// Define
if (!function_exists('array_diff_key')) {
    function array_diff_key()
    {
        $args = func_get_args();
        return call_user_func_array('php_compat_array_diff_key', $args);
    }
}

