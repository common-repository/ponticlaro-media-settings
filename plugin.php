<?php
/*
Plugin Name: Ponticlaro Media Settings
Plugin URI: http://ponticlaro.com/wordpress/media-settings/
Author: Ponticlaro
Author URI: http://ponticlaro.com/
Description: Keep your media insert code consistent site-wide.
Version: 1.4

*/

if (preg_match('@/wp-admin/@', $_SERVER['REQUEST_URI'])) {
	require_once(dirname(__FILE__).'/media-settings.php');
	$PonticlaroMediaSettings = new PonticlaroMediaSettings();
} else {
	require_once(dirname(__FILE__).'/shortcode.php');
	$PonticlaroMediaSettingsShortCode = new PonticlaroMediaSettingsShortCode();
}
?>