<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Moltin_Install' ) ) :

/**
 * Moltin_Install Class
 */
class Moltin_Install {

	public function __construct() {
		register_activation_hook( WC_PLUGIN_FILE, array( $this, 'install' ) );

		add_action( 'admin_init', array( $this, 'install_actions' ) );
		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
	}

	public function check_version() {
		//
	}

	public function install_actions() {
		//
	}

	public function install() {
		$this->create_pages();

		// Flush rules after install
		flush_rewrite_rules();

		// Redirect to welcome screen
		set_transient( '_wc_activation_redirect', 1, 60 * 60 );
	}

	public function update() {
		// Do updates
		$current_db_version = get_option( 'moltin_db_version' );

		if ( version_compare( $current_db_version, '0.1', '<' ) ) {
			include( 'updates/woocommerce-update-0.1.php' );
			update_option( 'moltin_db_version', '0.1' );
		}

		update_option( 'moltin_db_version', WC()->version );
	}

	private function create_cron_jobs() {
		/*
		// Cron jobs
		wp_clear_scheduled_hook( 'woocommerce_scheduled_sales' );

		$ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';

		wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'daily', 'woocommerce_scheduled_sales' );

		$held_duration = get_option( 'woocommerce_hold_stock_minutes', null );

		if ( is_null( $held_duration ) ) {
			$held_duration = '60';
		}

		if ( $held_duration != '' ) {
			wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 ), 'woocommerce_cancel_unpaid_orders' );
		}

		wp_schedule_event( time(), 'twicedaily', 'woocommerce_cleanup_sessions' );
		*/
	}

	public static function create_pages() {
	/*
		$pages = apply_filters( 'woocommerce_create_pages', array(
			'shop' => array(
				'name'    => _x( 'shop', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'Shop', 'Page title', 'woocommerce' ),
				'content' => ''
			),
			'cart' => array(
				'name'    => _x( 'cart', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'Cart', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']'
			),
			'checkout' => array(
				'name'    => _x( 'checkout', 'Paeg slug', 'woocommerce' ),
				'title'   => _x( 'Checkout', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']'
			),
			'myaccount' => array(
				'name'    => _x( 'my-account', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'My Account', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']'
			)
		) );

		foreach ( $pages as $key => $page ) {
			wc_create_page( esc_sql( $page['name'] ), 'woocommerce_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wc_get_page_id( $page['parent'] ) : '' );
		}
	*/
	}
}

endif;

return new Moltin_Install();
