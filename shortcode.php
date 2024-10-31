<?php
require_once(dirname(__FILE__).'/common.php');
$__ponticlaro = array();

class PonticlaroMediaSettingsShortCode extends PonticlaroMediaCommon {
	function PonticlaroMediaSettingsShortCode() {
        add_shortcode('media', array(&$this, 'getShortCodeHTML'));
	}

	function getShortCodeHTML($attribs=false, $content=false) {
    	global $post, $__ponticlaro;
    	$defaultAttribs = array(
    		'id'      => null,
			'width'   => null,
			'height'  => null,
			'size'    => null, // thumbnail, medium, large
    		'rel'     => null,
    		'title'   => null,
    		'class'   => '',
    		'alt'     => null,
    		'link'    => null,
    		'url'     => null,
    	);
    	if (!is_array($attribs)) $attribs = array();
    	
    	extract(shortcode_atts($defaultAttribs, $attribs));
		$extraAttribs = array_diff_key($attribs, $defaultAttribs);

		$post_id = $post->ID;

		$error = '';
		
		if (!$id && $url) {
			if ($url == 'true') {
				if (!isset($__ponticlaro[$post_id])) $url = 0;
				else $url = $__ponticlaro[$post_id]+1;
			} else {
				$url--;
			}
			$medias = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'orderby' => 'menu_order ASC, ID', 'order' => 'DESC') );
			$media = array_pop(array_slice($medias, (int) $url, 1));
			$id = $media->ID;
			return wp_get_attachment_url($id);
		} elseif (!$id) {
			if (!isset($__ponticlaro[$post_id])) $__ponticlaro[$post_id] = 0;
			else $__ponticlaro[$post_id]++;

			$medias = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'orderby' => 'menu_order ASC, ID', 'order' => 'DESC') );
			if (is_array($medias)) $media = array_pop(array_slice($medias, $__ponticlaro[$post_id], 1));
			$id = $media->ID;
			
		} else {
			$media =& get_post( $id );
		}

		$url = wp_get_attachment_url($id);



		$htmlAttribs  = '';		
		$htmlAttribs .= 'id="" ';
		$htmlAttribs .= 'rel="" ';
		$htmlAttribs .= 'title="" ';
		$htmlAttribs .= 'class="" ';
		$htmlAttribs .= 'alt="" ';

		if (wp_attachment_is_image($id)) {
			//$html = get_attachment_icon($id, true);
			if ($width || $height) {
				if (!$width) $width = $height;
				if (!$height) $height = $width;
				$size = array($width, $height);
			}
			$html = wp_get_attachment_image($id, $size);
			$html = $this->make_attribute($html, 'class', 'image');


			if ($link) {
				$html = '<a href="'.$url.'">'.$html.'</a>';
			}
		} else {
			$html = '<a href="'.$url.'">'.($content ? $content : $media->post_title).'</a>';
		}
		$html = $this->make_attribute($html, 'class', preg_replace('@/@', '-', $media->post_mime_type), true);
		$options = get_option('ponticlaro_media_options');
		
		if ($options['allmedia']) {
			$html = $this->image_send_to_editor($html, $id);
		} else {
			$o = array(
				'title' => true,
			);
			$a = array(
				'title' => 'custom',
			);
			$v = array(
				'title' => $title ? $title : $media->post_title,
			);
			foreach (array('rel', 'class', 'alt') as $k) {
				if ($$k) {
					$o[$k] = true;
					$a[$k] = 'custom';
					$v[$k] = $$k;
				}
			}
			$html = $this->image_send_to_editor($html, $id, $o, $a, $v);
		}

		foreach ($extraAttribs as $attrib => $val) {
			$html = $this->make_attribute($html, $attrib, $val);
		}
		return $html;
	}
	function image_send_to_editor($html, $id, $o=array(), $a=array(), $v=array()) {
		return parent::image_send_to_editor($html, $id, $o, $a, $v);
	}
	

}

?>