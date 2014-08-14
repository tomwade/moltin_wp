<?php

/** Core Moltin functions ******************************************************/

if(!function_exists('moltin')) {

	function moltin() {
		global $moltin;

		if(!$moltin) {
			$moltin = new Moltin;
			$moltin->authenticate();
		}

		return $moltin;
	}

}

if(!function_exists('moltin_call')) {

	function moltin_call($method, $action, $args = null) {
		$m = moltin();

		return $m->{$method}($action, $args);
	}

}


/** Store functions ******************************************************/

function set_moltin_breadcrumb($entity, $entity_id = false) {
	global $moltin_breadcrumb;

	return $moltin_breadcrumb = array('entity' => $entity, 'entity_id' => $entity_id);
}

function is_moltin($entity = false, $entity_id = false) {
	global $moltin_breadcrumb;

	if(!isset($moltin_breadcrumb)) {
		return false;
	}

	if(!$entity && !$entity_id) {
		return $moltin_breadcrumb;
	}
	elseif($entity == $moltin_breadcrumb['entity'] && $entity_id == $moltin_breadcrumb['entity_id']) {
		return $moltin_breadcrumb;
	}
	elseif($entity == $moltin_breadcrumb['entity']) {
		return $moltin_breadcrumb;
	}
	
	return false;
}

function moltin_breadcrumb($sep = ' &middot; ') {

	$steps = array();

	// Check if we have a product
	if($bc = is_moltin('product')) {
		if($bc['entity_id']) {
			$product = moltin_call('get', 'product', array('slug' => $bc['entity_id'], 'status' => 1));

			$steps[product_link($product['result'])] = $product['result']['title'];

			$base_category = reset($product['result']['category']['data']);

			$steps[category_link($base_category)] = $base_category['title'];

			while($base_category['parent'] != NULL) {
				$steps[category_link($base_category['parent']['data'])] = $base_category['parent']['value'];
				$base_category = $base_category['parent']['data'];
			}
		}
	}

	// Check if we have a category
	if($bc = is_moltin('category')) {
		if($bc['entity_id']) {
			$category = moltin_call('get', 'category', array('slug' => $bc['entity_id'], 'status' => 1));

			$base_category = $category['result'];

			$steps[category_link($base_category)] = $base_category['title'];

			while($base_category['parent'] != NULL) {
				$steps[category_link($base_category['parent']['data'])] = $base_category['parent']['value'];
				$base_category = $base_category['parent'];
			}
		}
	}

	// Check if we are in the cart
	if($bc = is_moltin('cart')) {
		$steps[get_permalink(get_option('store_cart_page_id'))] = 'Shopping Cart';
	}

	// Check if we are checking out
	if($bc = is_moltin('checkout')) {
		$steps[] = 'Checkout';
		$steps[get_permalink(get_option('store_cart_page_id'))] = 'Shopping Cart';
	}

	// Flip it to get the right order
	$steps[site_url(get_option('store_base_uri'))] = 'Store';

	$steps = array_reverse($steps);
	array_walk($steps, 'moltin_breadcrumb_value_parse');

	return '<div class="moltin-breadcrumb">' . implode($steps, $sep) . '</div>';
}

function moltin_breadcrumb_value_parse(&$item, $key) {
	$item = '<a href="' . $key . '">' . $item . '</a>';
}

function moltin_get_messages($type = false) {
	global $moltin;

	return $moltin->get_messages($type);
}

function moltin_render_messages($type = false) {
	$m   = moltin_get_messages($type);
	$str = '';

	if($m) {
		foreach($m as $message_type => $messages) {
			foreach($messages as $message) {
				$str .= '<div class="moltin-message ' . $message_type . '-message"><p>' . $message . '</p></div>';
			}
		}
	}

	return $str;
}

function moltin_set_message($msg, $type = 'info') {
	global $moltin;

	return $moltin->set_message($msg, $type);
}


/**
 * Get template part (for templates like the shop-loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function moltin_get_template_part( $slug, $name = '', $args = array() ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}
	
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/woocommerce/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woocommerce/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugin filter template file from their plugin
	$template = apply_filters( 'moltin_get_template_part', $template, $slug, $name, $args );

	if ( $template ) {
		include( $template );
	}
}

function moltin_paginate($pagination = '') {
	if(!$pagination) {
		global $pagination;
	}

    // Don't print empty markup if there's only one page.
    if ( $pagination['total'] < get_option('store_results_per_page') ) {
        return;
    }

    $paged        = get_query_var('moltin_page') ? intval( get_query_var('moltin_page') ) : 1;
    $pagenum_link = html_entity_decode( get_pagenum_link() );
    $query_args   = array();
    $url_parts    = explode( '?', $pagenum_link );

    if ( isset( $url_parts[1] ) ) {
        wp_parse_str( $url_parts[1], $query_args );
    }

    $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
    $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

    $format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
    $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

    // Set up paginated links.
    $links = paginate_links( array(
        'base'     => $pagenum_link,
        'format'   => $format,
        'total'    => $pagination['total'],
        'current'  => $paged,
        'mid_size' => 1,
        'add_args' => $query_args,
        'prev_text' => __( '&larr; Previous', 'moltin' ),
        'next_text' => __( 'Next &rarr;', 'moltin' ),
    ) );

    return $links;
}

/** Category helpers ******************************************************/

function moltin_categories() {
	$categories = moltin_call('get', 'categories/tree', array('status' => 1));

	return ($categories['status'] == 'true') ? $categories['result'] : array();
}

/** Helper functions ******************************************************/

/**
 * Get the plugin url.
 *
 * @return string
 */
function plugin_url() {
	return untrailingslashit( plugins_url( '/', __FILE__ ) );
}

/**
 * Get the plugin path.
 *
 * @return string
 */
function plugin_path() {
	return untrailingslashit( plugin_dir_path( __FILE__ ) . '../' );
}

/**
 * Get the template path.
 *
 * @return string
 */
function template_path() {
	return 'moltin/';
}

function product_link($p) {
	$base		= trim(get_option('store_base_uri'), '/');
	$product	= trim(get_option('store_product_uri'), '/');

	if(count($p['category']['data'])) {
		$first_cat = reset($p['category']['data']);

		$category_modifier = $first_cat['slug'] . '/';
	}

	return site_url($base . '/' . $product . '/' . $category_modifier . $p['slug'] . '/');
}

function category_link($c) {
	$base		= trim(get_option('store_base_uri'), '/');
	$category	= trim(get_option('store_category_uri'), '/');

	$base_category = $c;

	$steps   = array();
	$steps[] = $base_category['slug'];

	while($base_category['parent'] != NULL) {
		$steps[] = $base_category['parent']['data']['slug'];
		$base_category = $base_category['parent'];
	}

	$steps = array_reverse($steps);

	return site_url($base . '/' . $category . '/' . implode($steps, '/') . '/');
}

/** Product functions ******************************************************/

function product_has_images($p) {
	return !! (count($p) > 0);
}

function product_has_variations($p) {
	if(!$p['modifiers']) {
		return ;
	}

	$variations = array();
	
	foreach($p['modifiers'] as $m_id => $m) {
		if($m['type']['value'] == 'Variant') {
			$variations[$m_id] = $m;
		}
	}

	return $variations;
}

/** Cart functions ******************************************************/

function moltin_cart_add($product_id, $quantity, $modifiers = false, $ident = false) {
	// See if we need to generate an ident
	if(!$ident) {
		$ident = moltin_generate_ident();
	}

	// Check there is enough quantity left
	$product = moltin_call('get', 'product', array('id' => $product_id, 'bypass_cache' => true));

	if($quantity > $product['result']['stock_level']) {
		moltin_set_message('There isn\'t enough stock remaining to fulfil your desired quantity.', 'error');

		header('Location: ' . product_link($product['result']));
		exit;
	}

	$result = moltin_call('post', 'cart/' . $ident, array('id' => $product_id, 'quantity' => $quantity, 'modifier' => $modifiers));

	return $result;
}

function moltin_cart_remove($product_id, $ident = false) {
	// See if we need to generate an ident
	if(!$ident) {
		$ident = moltin_generate_ident();
	}

	$request = moltin_call('delete', 'cart/' . $ident . '/item/' . $product_id);

	return $request;
}

function moltin_cart_fetch($ident = false) {
	// See if we need to generate an ident
	if(!$ident) {
		$ident = moltin_generate_ident();
	}

	$request = moltin_call('get', 'cart/' . $ident);

	// Run check to ensure products still exist, only run on cart and checkout
	if(is_moltin('cart') || is_moltin('checkout')) {
		foreach($request['result']['contents'] as $basket_key => $basket_item) {
			if($check_still_available = moltin_call('get', 'product', array('slug' => $basket_item['slug'], 'status' => 1, 'bypass_cache' => true))) {
				if($check_still_available['status'] == false || ($check_still_available['result']['stock_status']['value'] == 'Out of Stock' || $check_still_available['result']['stock_status']['value'] == 'More Stock Ordered' || ($check_still_available['result']['stock_status']['value'] != 'Unlimited' && ($basket_item['quantity'] > $check_still_available['result']['stock_level'])))) {
					// Uh-oh, no longer available in that QTY. Remove it from the basket and show an error message.
					moltin_set_message('Sorry, but "<em>' . $basket_item['quantity'] . 'x ' . $basket_item['title'] . '</em>" is no longer available.');

					moltin_call('put', 'cart/' . $ident . '/item/' . $basket_key, array('quantity' => 0));
				}
			}
		}
	}

	return $request['result'];
}

function moltin_cart_shipping_methods($cart) {
	$methods   = moltin_call('get', 'shipping');
	$available = array();

	// Get the cart variables we need to check against
	$total_price	= $cart['total'];
	$total_weight	= $cart['weight'];

	foreach($methods['result'] as $method) {
		if(($total_price >= $method['price_min'] && ($total_price <= $method['price_max'] || $method['price_min'] > $method['price_max'] || $method['price_max'] == 0)) && ($total_weight >= $method['weight_min'] && ($total_price <= $method['weight_max'] || $method['weight_min'] > $method['weight_max'] || $method['weight_max'] == 0))) {
			$available[] = $method;
		}
	}

	return $available;
}

function moltin_cart_payment_methods() {
	$methods = moltin_call('get', 'gateways/enabled');

	return $methods['result'];
}

function moltin_generate_ident() {
	$salt = AUTH_SALT;

	// Check if user is logged in
	if(is_user_logged_in()) {
		$ident = get_current_user_id();
	}
	// Otherwise, create cookie
	else {
		// Check if an identifier is already set
		if(!$_COOKIE['moltin_ident']) {
			$ident = uniqid();

			// Set cookie to track the identifier
			setcookie('moltin_ident', moltin_salt_ident($ident), time()+7200, '/');
		} else {
			// Load the existing identifier
			return $_COOKIE['moltin_ident'];
		}
	}

	return moltin_salt_ident($ident);
}

function moltin_salt_ident($ident) {
	return md5($salt . $ident);
}

function set_address_post_array($input_str, $arr_key) {
	return preg_replace('|name="([^"]+)"|', 'name="' . $arr_key . '[$1]"', $input_str);
}

function moltin_currency_format($amount, $format = '£{price}', $decimal = '.', $thousand = ',', $rounding = 2) {
	$decimals = ($rounding) ? 2 : 0 ;

	$amount = str_replace('{price}', number_format($amount, $decimals, $decimal, $thousand), $format);

	return $amount;
}