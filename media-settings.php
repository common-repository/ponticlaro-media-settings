<?php
require_once(dirname(__FILE__).'/common.php');
class PonticlaroMediaSettings extends PonticlaroMediaCommon {
	var $imageAttribs;
	
	function PonticlaroMediaSettings() {
		add_action('admin_head', array(&$this, 'settings_js'), 100);

		add_action('admin_menu', array(&$this, 'addhooks'));
		load_plugin_textdomain('ponticlaro-media-settings', 'wp-content/plugins/ponticlaro-media-settings/languages', 'ponticlaro-media-settings/languages');

		add_action('_admin_menu', array(&$this, 'menu'));
		add_shortcode('media', array(&$this, 'getShortCodeHTML'));
	}
	function getShortCodeHTML($attribs=false, $content=false) {
		return '';
	}
	function menu() {
		global $submenu;
	}
	function settings_js() {
		wp_enqueue_script('jquery-ui-tabs');

		wp_enqueue_script('ponticlaro-media-settings', '/wp-content/plugins/ponticlaro-media-settings/settings.js', array('common', 'jquery'));
		wp_print_scripts();
	}
	
	function action_links($action_links, $plugin_file, $plugin_data, $context) {
		$action_links[] = '<a href="options-general.php?page=ponticlaro-media-settings/media-settings.php" class="settings">' . __('Settings', 'ponticlaro-media') . '</a>';

		return $action_links;
	}
	
	function settings() {
		$attribs = array('alt', 'title', 'class', 'height', 'width', 'rel');

		if ($_POST['action'] == 'save') {
			switch($_POST['ponticlaro_image_path']) {
				case 'relative':
					$image_path = '/';
				break;
				case 'custom':
					$custom = $_POST['ponticlaro_image_domain'];
					if (strlen($custom) > 7) {
						if (!preg_match('@.*/$@', $custom)) {
							$custom .= '/';
						}
						$image_path = $custom;
					} else {
						$image_path = '';
					}
					
				break;
				default:
					$image_path = '';
				break;
			}
			if (is_array($_POST['ponticlaro_image_val_custom'])) foreach ($_POST['ponticlaro_image_val_custom'] as $k => $v) {
				$_POST['ponticlaro_image_val_custom'][$k] = trim($v);
			}
			update_option('ponticlaro_image_attribs', $_POST['ponticlaro_image_attribs']);
			update_option('ponticlaro_image_vals', $_POST['ponticlaro_image_vals']);
			update_option('ponticlaro_image_val_custom', $_POST['ponticlaro_image_val_custom']);
			
			update_option('ponticlaro_media_options', $_POST['ponticlaro_media_options']);
			
			update_option('ponticlaro_image_path', $image_path);
			if (strlen($image_path) > 2) update_option('ponticlaro_image_path_saved', $image_path);
		}
	
		$image_path = get_option('ponticlaro_image_path');
		$image_path_saved = get_option('ponticlaro_image_path_saved');
		
		$media_options 		= get_option('ponticlaro_media_options');
		$image_attribs  	= get_option('ponticlaro_image_attribs');
		$image_vals     	= get_option('ponticlaro_image_vals');
		$image_val_custom 	= get_option('ponticlaro_image_val_custom');

		include(dirname(__FILE__).'/settings.html');
	}
	
	function image_send_to_editor($html, $id) {
		return parent::image_send_to_editor($html, $id);
	}
	
	function addhooks() {
		add_options_page(__('Ponticlaro Media', 'ponticlaro-media'), __('Ponticlaro Media', 'ponticlaro-media'), 10, __FILE__, array(&$this, 'settings'));
		add_filter('image_send_to_editor', array(&$this, 'image_send_to_editor'), 10, 2);
		add_filter('media_send_to_editor', array(&$this, 'image_send_to_editor'), 10, 2);
		add_filter('plugin_action_links_ponticlaro-media-settings/plugin.php', array(&$this, 'action_links'), 10, 4);
		$media_options = get_option('ponticlaro_media_options');
		if ($media_options['shortcode']) {
			setcookie('insertshortcode', '1', time() + 3600, '/');
		} else {
			setcookie('insertshortcode', '0', time() - 3600, '/');
		}
	}
}
?>