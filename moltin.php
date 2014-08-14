<?php
/*
	Plugin Name: Moltin
	Plugin URI: http://www.molt.in
	Description: The WordPress plugin for the Molt.in system
	Version: 0.1
	Author: SteadyGo
	Author Email: tom.wade@steadygo.co.uk
	License:

	Copyright 2014 SteadyGo (tom.wade@steadygo.co.uk)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

session_start();

class Moltin_Init {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'Moltin';
	const slug = 'moltin';
	
	/**
	 * Constructor
	 */
	function __construct() {
		//register an activation hook for the plugin
		register_activation_hook( __FILE__, array( &$this, 'install_moltin' ) );

		// Hook up to the init action
		add_action( 'init', array( &$this, 'init_moltin' ) );

		// Load our widgets
		add_action( 'widgets_init', array(&$this, 'moltin_widgets_init') );

		// Add our rewrites
		add_action('generate_rewrite_rules', array( &$this, 'moltin_add_rewrite_rules'));
		add_filter('query_vars', array( &$this, 'moltin_add_query_vars'));
		add_action('admin_init', array( &$this, 'moltin_flush_rewrite_rules'));
		add_action('template_redirect', array( &$this, 'hooker'));

		// Hook into user management
		add_action( 'user_register', array( &$this, 'moltin_user_create' ), 10, 1 );
		add_action( 'profile_update', array( &$this, 'moltin_user_update' ), 10, 2 );
	
		// Create the user areas in admin
		add_action('admin_menu', array(&$this, 'moltin_user_area'));

		// Dev mode footer
		add_action('wp_footer', array(&$this, 'moltin_dev_mode_footer'));
	}
  
	/**
	 * Runs when the plugin is activated
	 */  
	function install_moltin() {
		// do not generate any output here
	}
  
	/**
	 * Runs when the plugin is initialized
	 */
	function init_moltin() {
		// Setup localization
		load_plugin_textdomain( 'moltin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Load JavaScript and stylesheets
		$this->register_scripts_and_styles();

		// Register our shortcodes
		foreach (glob(plugin_dir_path(__FILE__) . 'includes/shortcodes/moltin_*.php') as $filename) {
			include_once $filename;
		}

		// Include our functions
		include_once( 'includes/class-install.php' );
		include_once( 'includes/class-template-loader.php' );

		/**
		 * Load the SDK Middleware
		 */

		// Load the main SDK
		require_once('sdk/src/Moltin/SDK/SDK.php');

		// Load our interfaces
		require_once('sdk/src/Moltin/SDK/StorageInterface.php');
		require_once('sdk/src/Moltin/SDK/RequestInterface.php');
		require_once('sdk/src/Moltin/SDK/AuthenticateInterface.php');

		// Load Exception libraries
		require_once('sdk/src/Moltin/SDK/Exception/InvalidFieldTypeException.php');
		require_once('sdk/src/Moltin/SDK/Exception/InvalidRequestException.php');
		require_once('sdk/src/Moltin/SDK/Exception/InvalidResponseException.php');

		// Load Authentication libraries
		require_once('sdk/src/Moltin/SDK/Authenticate/ClientCredentials.php');
		require_once('sdk/src/Moltin/SDK/Authenticate/Password.php');
		require_once('sdk/src/Moltin/SDK/Authenticate/Refresh.php');

		// Load Storage method
			// Session
			require_once('sdk/src/Moltin/SDK/Storage/Session.php');

		// Load Request method
			// CURL
			require_once('sdk/src/Moltin/SDK/Request/CURL.php');

		// Load Flows
		require_once('sdk/src/Moltin/SDK/Flows.php');

		// Load our core class extensions
		require_once( 'includes/class-moltin.php' );

		/**
		 * Load our helper classes
		 */
		require_once('helpers/moltin.php');

		/**
		 * Load the admin area
		 */
		if(is_admin()) {
			include_once( 'includes/admin/class-admin-dashboard.php' );
			include_once( 'includes/admin/class-admin-menus.php' );
			include_once( 'includes/admin/class-admin-options.php' );
		}
	}


	function moltin_widgets_init() {
		/**
		 * Load our widget classes
		 */
		require_once('includes/widgets/class-widget-latest_products.php');

		register_widget( 'Moltin_Widget_LatestProducts' );
	}


	/**
	 * Rewrites
	 */
	function moltin_add_rewrite_rules( $wp_rewrite ) 
	{
		$base		= trim(get_option('store_base_uri'), '/');
		$product	= trim(get_option('store_product_uri'), '/');
		$category	= trim(get_option('store_category_uri'), '/');

	  	$new_rules = array(
	  						$base . '/cart/checkout/success/(.+)' => 'index.php?moltin_cart_success=true&moltin_ref=' . $wp_rewrite->preg_index(1),
	  						$base . '/cart/checkout/failure/(.+)' => 'index.php?moltin_cart_failure=true&moltin_ref=' . $wp_rewrite->preg_index(1),
	  						$base . '/cart/checkout' => 'index.php?moltin_cart_checkout=true',
	  						$base . '/cart/add' => 'index.php?moltin_cart_add=true',

							$base . '/search/page/(.+)' => 'index.php?moltin_page=' . $wp_rewrite->preg_index(1) . '&moltin_search=true',
							$base . '/search/' => 'index.php?moltin_search=true',

	  						$base . '/' . $product . '/(.+)/(.+)' => 'index.php?&moltin_product=' . $wp_rewrite->preg_index(2) . '&moltin_category=' . $wp_rewrite->preg_index(1),
	  						$base . '/' . $product . '/(.+)' => 'index.php?&moltin_product=' . $wp_rewrite->preg_index(1),
	  						$base . '/' . $category . '/(.+)/page/(.+)' => 'index.php?moltin_page=' . $wp_rewrite->preg_index(2) . '&moltin_category=' . $wp_rewrite->preg_index(1),
	  						$base . '/' . $category . '/(.+)' => 'index.php?moltin_category=' . $wp_rewrite->preg_index(1),
	  					);

		//​ Add the new rewrite rule into the top of the global rules array
		$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	}

	function moltin_add_query_vars($public_query_vars) {
 
		$public_query_vars[] = 'moltin_product';
		$public_query_vars[] = 'moltin_category';
		$public_query_vars[] = 'moltin_page';

		$public_query_vars[] = 'moltin_cart_add';
		$public_query_vars[] = 'moltin_cart_checkout';
		$public_query_vars[] = 'moltin_ref';
		$public_query_vars[] = 'moltin_cart_success';
		$public_query_vars[] = 'moltin_cart_failure';

		$public_query_vars[] = 'moltin_search';
	 	
		return $public_query_vars;
	}

	function moltin_flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	function hooker() {
		global $wp_query;

		// Check for product queries matching
		if(isset($wp_query->query['moltin_product'])) {
			if($product = moltin_call('get', 'product', array('slug' => $wp_query->query['moltin_product'], 'status' => 1))) {
				if( isset($product['status']) && $product['status'] == 'true' && ($product['result']['status']['value'] == 'Live' || is_admin()) ) {
					$base_category 	= reset($product['result']['category']['data']);
					$related 		= moltin_call('get', 'products/search', array('category' => $base_category['id'], 'limit' => 4));

					foreach($related['result'] as $k => $r) {
						if($r['slug'] == $wp_query->query['moltin_product']) {
							unset($related['result'][$k]);
						}
					}

					set_moltin_breadcrumb('product', $wp_query->query['moltin_product']);
					moltin_get_template_part('product', 'single', array('product' => $product['result'], 'related' => $related['result']));
				}
				else {
					moltin_get_template_part('product', '404');
				}

				exit;
			}
		}
		// Check for category queries matching
		elseif(isset($wp_query->query['moltin_category'])) {

			$chunks = explode('/', $wp_query->query['moltin_category']);
			$chunks = array_reverse($chunks);

			$category = moltin_call('get', 'category', array('slug' => $chunks[0], 'status' => 1));
			$valid    = true;

			$chunk_i  = 0;

			if( isset($category['status']) && $category['status'] == 'true' ) {

				$base_category = $category['result'];

				while($base_category['parent'] != NULL) {

					$parent_category = moltin_call('get', 'category', array('slug' => $base_category['parent']['data']['slug'], 'status' => 1));

					if( isset($parent_category['status']) && $parent_category['status'] == 'true' && $chunks[++$chunk_i] == $parent_category['result']['slug'] ) {
						//
					}
					else {
						$valid = false;
						break;
					}

					$base_category = $parent_category['result'];
				}
			}
			else {
				$valid = false;
			}

			if($valid) {

				// Get products for this category
				$paged    = get_query_var('moltin_page') ? intval( get_query_var('moltin_page') ) : 1;
				$offset   = ($paged - 1) * get_option('store_results_per_page');
				$products = moltin_call('get', 'products/search', array('category' => $category['result']['id'], 'limit' => get_option('store_results_per_page'), 'offset' => $offset));

				// Load the category
				set_moltin_breadcrumb('category', $chunks[0]);
				moltin_get_template_part('category', 'single', array('category' => $category['result'], 'products' => $products['result'], 'pagination' => $products['pagination']));
			}
			else {
				moltin_get_template_part('category', '404');
			}

			exit;
		}
		// Check for cart addition
		elseif(isset($wp_query->query['moltin_cart_add'])) {
	
			set_moltin_breadcrumb('cart');

			// Validate our post
			if($_POST['id'] && $_POST['quantity']) {
				// Verify the nonce
				$verify = wp_verify_nonce( $_REQUEST['moltin-nonce'], 'moltin-product-add' );

				if($verify) {

					$result = moltin_cart_add($_POST['id'], $_POST['quantity'], $_POST['modifier']);

					moltin_set_message('Product has been added to your cart.', 'success');

					if(isset($_POST['redirect'])) {
						header('Location: ' . $_POST['redirect']);
					} else {
						moltin_get_template_part('cart', 'basket', array('add' => $result['status']));
					}

					exit;

				}
			}

			moltin_get_template_part('cart', 'basket', array('add' => false));
			exit;
		}
		// Check for cart addition
		elseif(isset($wp_query->query['moltin_cart_checkout'])) {
			$user 		= wp_get_current_user();
			$cart 		= moltin_cart_fetch();
			$shipping	= moltin_cart_shipping_methods($cart);
			$payment	= moltin_cart_payment_methods();

			$ident 		= moltin_generate_ident();

			if(!$cart['contents']) {
				header('Location: ' . get_permalink(get_option('store_cart_page_id')));
				exit;
			}

			set_moltin_breadcrumb('checkout');

			// Verify the nonce
			$verify = wp_verify_nonce( $_REQUEST['moltin-nonce'], 'moltin-checkout' );

			if($verify) {
		// More checks coming soon


		// Check if user wants to register inline
				$email_address = $_POST['billing']['email'];

				if( !$user && $_POST['create_user'] && username_exists( $email_address ) == null ) {

					// Generate the password and create the user
					$password 	= wp_generate_password( 12, false );
					$user_id 	= wp_create_user( $email_address, $password, $email_address );

					if(!$user_id) {
						echo 'User already exists';
						continue;
					}

					// Set the nickname
					wp_update_user(
						array(
							'ID'          =>    $user_id,
							'nickname'    =>    $email_address,
							'first_name'  =>    $_POST['billing']['first_name'],
							'last_name'  =>    	$_POST['billing']['last_name'],
						)
					);

					// Set the role
					$user = new WP_User( $user_id );

					// Email the user
					wp_mail( $email_address, 'Welcome!', 'Your Password: ' . $password );

				} // end if


        // create billing address

				$billing_data = $_POST['billing'];

        // create shipping address

				if($_POST['ship_to_billing']) {

					$shipping_data = 'bill_to';

				} else {

					$shipping_data = $_POST['shipping'];

				}

        // create order

				$order_data = array();

				if($user) {
					$order_data['customer'] = get_user_meta($user->ID, 'moltin_user_id', true);
				}

				$order_data['gateway'] = $_POST['payment_method'];
				$order_data['ship_to'] = $shipping_data;
				$order_data['bill_to'] = $billing_data;
				$order_data['shipping'] = $_POST['shipping_method'];

				$order = moltin_call('post', 'cart/' . $ident . '/checkout', $order_data);

				$order_id = $order['result']['id'];

		// Now we have the order, process the payment

				$expiry_date = explode('/', $_POST['expiry']);

				$expiry_date[1] = trim($expiry_date[1]);

				if(strlen($expiry_date[1]) == 2) {
					$expiry_date[1] = substr(date('Y'), 0, 2) . $expiry_date[1];
				}

				$process_data = array();

				$process_data['data'] 					= array();
				$process_data['data']['number'] 		= preg_replace('/[^0-9]/', '', $_POST['number']);
				$process_data['data']['expiry_month'] 	= trim($expiry_date[0]);
				$process_data['data']['expiry_year'] 	= trim($expiry_date[1]);
				$process_data['data']['cvv'] 			= intval($_POST['cvc']);

				$process = moltin_call('post', 'checkout/payment/purchase/' . $order_id, $process_data);

		// Check if the process failed..

				if($process['status'] === false) {
					header('Location: ' . get_permalink(get_option('store_cart_page_id')) . 'checkout/failure/' . $process['result']['reference']);
					exit;
				}

		// We have passed it, we can delete the basket now..

				moltin_call('delete', 'cart/' . $ident);

				$_SESSION['order_complete'] = $order_id;

				header('Location: ' . get_permalink(get_option('store_cart_page_id')) . 'checkout/success/' . $process['result']['reference']);
				exit;
			}

			moltin_get_template_part('cart', 'checkout', array('cart' => $cart, 'shipping' => $shipping, 'user' => $user, 'payment' => $payment));
			exit;
		}
		// Check for successful orders
		elseif(isset($wp_query->query['moltin_cart_success'])) {

			set_moltin_breadcrumb('cart');

			if(isset($_SESSION['order_id'])) {
				moltin_get_template_part('cart', 'success', array('order_id' => $_SESSION['order_id']));
			} else {
				header('Location: ' . get_permalink(get_option('store_cart_page_id')));
			}

			exit;
		}
		// Check for failed orders
		elseif(isset($wp_query->query['moltin_cart_failure'])) {

			set_moltin_breadcrumb('cart');

			moltin_get_template_part('cart', 'failure');
			exit;
		}
		// Check for search
		elseif(isset($wp_query->query['moltin_search'])) {

			$term 	  = $_GET['s'];

			// Get products for this category
			$paged    = get_query_var('moltin_page') ? intval( get_query_var('moltin_page') ) : 1;
			$offset   = ($paged - 1) * get_option('store_results_per_page');
			$products = moltin_call('get', 'products/search', array('title' => '%' . $term . '%', 'limit' => get_option('store_results_per_page'), 'offset' => $offset));

			moltin_get_template_part('store', 'search', array('products' => $products['result'], 'pagination' => $products['pagination']));
			exit;
		}
	}

	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {
			$this->load_file( self::slug . '-admin-script', '/assets/backend/js/functions.js', true );
			$this->load_file( self::slug . '-admin-style', '/assets/backend/css/style.css' );
		} else {
			$this->load_file( self::slug . '-bootstrap-js', '/assets/frontend/js/bootstrap.js', true );
			$this->load_file( self::slug . '-touchspin-js', '/assets/frontend/js/bootstrap.touchspin.js', true );
			$this->load_file( self::slug . '-card-js', '/assets/frontend/js/card.js', true );
			$this->load_file( self::slug . '-smoothproducts-js', '/assets/frontend/js/smoothproducts.min.js', true );
			$this->load_file( self::slug . '-script', '/assets/frontend/js/functions.js', true );

			$this->load_file( self::slug . '-style', '/assets/frontend/css/style.css' );
			$this->load_file( self::slug . '-card-css', '/assets/frontend/css/card.css' );
			
		}
	}
	
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false ) {

		$url  = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') ); //depends on jquery
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			}
		}

	}

    function moltin_user_update( $user_id, $old_user_data ) {
    	$user 		= get_userdata( $user_id );

		$first_name = ($user->first_name) ? $user->first_name : 'WordPress';
		$last_name 	= ($user->last_name) ? $user->last_name : 'User';

    	$result 	= moltin_call('post', 'customer', array('first_name' => $first_name, 'last_name' => $last_name, 'email' => $user->user_email));

    	return $result['status'];
    }

    function moltin_user_create( $user_id ) {
    	$user 	 	= get_userdata( $user_id );
		$created 	= false;

		$first_name = ($user->first_name) ? $user->first_name : 'WordPress';
		$last_name 	= ($user->last_name) ? $user->last_name : 'User';

    	$result  	= moltin_call('post', 'customer', array('first_name' => $first_name, 'last_name' => $last_name, 'email' => $user->user_email));

    	if($result['status'] == 'true') {
    		$created = update_user_meta( $user_id, 'moltin_user_id', $result['result']['id'] );
    	}

    	return $created;
    }
  
    function moltin_user_area() {
    	add_users_page('My Orders', 'My Orders', 'read', 'orders', array(&$this, 'moltin_profile_orders'));
    	add_users_page('My Addresses', 'My Addresses', 'read', 'addresses', array(&$this, 'moltin_profile_orders'));
    }

    function moltin_profile_orders() {
    	$orders = moltin_call('get', 'orders', array('customer' => get_user_meta(get_current_user_id(), 'moltin_user_id', true)));

    	moltin_get_template_part('profile', 'orders', array('orders' => $orders['result']));
    }

    function moltin_dev_mode_footer() {
    	global $moltin;

    	if(get_option('moltin_dev')) {
	    	echo '<p style="text-align:center;">Fresh API calls: ' . $moltin->moltin_api_new . '/' . ( $moltin->moltin_api_new + $moltin->moltin_api_cached ) . ' (' . $moltin->moltin_api_cached . ' cached)</p>';
	    }
	}
}

new Moltin_Init();