<?php 
defined('ABSPATH') or die('You\'re not supposed to be here.');

class Moltin_Shortcode_CartTotalItems extends Moltin_Shortcode_Base {

	public function __construct() {
		$cart = moltin_cart_fetch();

		$this->code 	= 'cart_total_items';
		$this->atts 	= array('items' => count($cart['contents']));

		$this->register();
	}

}

new Moltin_Shortcode_CartTotalItems();