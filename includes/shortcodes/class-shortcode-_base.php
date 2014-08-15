<?php 
/**
 * @Author	Anonymous
 * @link http://www.redrokk.com
 * @Package Wordpress
 * @SubPackage RedRokk Library
 * @copyright  Copyright (C) 2011+ Redrokk Interactive Media
 * 
 * @version 0.1
 */

defined('ABSPATH') or die('You\'re not supposed to be here.');

class Moltin_Shortcode_Base {

	/**
	 * the shortcode name (only when it matches the callback name; see discussion of attributes below)
	 * 
	 * @var string
	 */
	var $code;

	/**
	 * an associative array of attributes
	 * 
	 * @var array
	 */
	var $atts = array();
	
	public function __construct() {
		//
	}

	public function register() {
		add_shortcode( $this->code, array(&$this, 'parse') );
	}

	public function parse( $attributes, $content = null ) {
	    $atts 		= shortcode_atts( $this->atts, $attributes );

	    require_once(plugin_dir_path(__FILE__) . 'views/html-shortcode-' . $this->code . '.php');
	}
}