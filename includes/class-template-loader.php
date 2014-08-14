<?php
class Moltin_Template_Loader {

	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_loader' ) );
	}

	public function template_loader( $template ) {
		$find = array();
		$file = '';

		if ( is_page( get_option('store_cart_page_id') ) ) {

			set_moltin_breadcrumb('cart');

			// Check if we are going to the checkout
			if($_POST['checkout_cart'] && count($_POST['update_qty'])) {
				header('Location: ' . get_permalink(get_option('store_cart_page_id')) . '/checkout');exit;
			}

			// Check for QTY update
			if($_POST['update_cart'] && count($_POST['update_qty'])) {
				$ident = moltin_generate_ident();
				$count = 0;
				foreach($_POST['update_qty'] as $update_key => $update_qty) {
					if($_POST['original_qty'][$update_key] != $update_qty) {
						moltin_call('put', 'cart/' . $ident . '/item/' . $update_key, array('quantity' => $update_qty));
						moltin_set_message('Cart quantities have been updated', 'success');

						++$count;
					}
				}

				if($count) {
					header('Location: ' . get_permalink(get_option('store_cart_page_id')));exit;
				}
			}

			// Check for basket removal
			if($_GET['remove_item']) {
				$ident = moltin_generate_ident();
				$item  = $_GET['remove_item'];

				moltin_call('delete', 'cart/' . $ident . '/item/' . $item);
				moltin_set_message('Product has been removed.', 'success');

				header('Location: ' . get_permalink(get_option('store_cart_page_id')));exit;
			}

			// Load the cart
			$cart 		= moltin_cart_fetch();
			$shipping	= moltin_cart_shipping_methods($cart);

			$shipping_price = 99999999;

			if(count($shipping) > 1) {
				foreach($shipping as $s) {
					$shipping_price = ($s['price'] < $shipping_price) ? $s['price'] : $shipping_price;
				}

				$shipping_price = $shipping_price;
			}
			elseif(count($shipping) == 0) {
				$shipping_price = 'Unknown';
			}
			else {
				$shipping_price = $shipping[0]['price'];
			}

			if($cart['contents']) {
				$file 	= 'cart-basket.php';
				$find[] = $file;
				$find[] = 'templates/' . $file;
			} else {
				$file 	= 'cart-empty.php';
				$find[] = $file;
				$find[] = 'templates/' . $file;
			}

		}

		if ( is_page( get_option('store_front_page_id') ) ) {

			set_moltin_breadcrumb('front');

			$file 	= 'store-front.php';
			$find[] = $file;
			$find[] = 'templates/' . $file;

			// Load categories
			$categories = moltin_categories();

		}

		if ( $file ) {
			$template = locate_template( $find );
			if ( ! $template ) {
				$template = plugin_path() . '/templates/' . $file;
			}
		}

		include $template;
		exit;
	}

}

new Moltin_Template_Loader();