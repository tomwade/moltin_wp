<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Moltin_Admin_Dashboard' ) ) :

class Moltin_Admin_Dashboard {

	private $_statistics;

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Only hook in admin parts if the user has admin access
		if ( is_admin() ) {
			add_action( 'wp_dashboard_setup', array( $this, 'init' ) );
		}
	}

	/**
	 * Init dashboard widgets
	 */
	public function init() {

		// Get a list of the 
		$load_stats = moltin_call('get', 'orders', array('bypass_cache' => true)); //, array('order' => 'created_at', 'sort' => 'desc'));
		$this->_statistics = $load_stats['result'];

		// Whilst custom ordering is being implemented in the API, we must traverse the array to sort it ourselves..
		usort($this->_statistics, function($a, $b) {
			return ($a['id'] < $b['id']);
		});

		wp_add_dashboard_widget( 'moltin_dashboard_status', __( 'Moltin Overview', 'woocommerce' ), array( $this, 'status_widget' ) );
		wp_add_dashboard_widget( 'moltin_dashboard_recent', __( 'Recent Sales', 'woocommerce' ), array( $this, 'recent_sales' ) );

	}

	/**
	 * Show status widget
	 */
	public function status_widget() {
		global $wpdb;

		$total_day   	= 0;
		$total_week  	= 0;
		$total_month 	= 0;
		$total_forever	= 0;

		$processing     = 0;
		$completed      = 0;

		$items 			= 0;
		$average_items  = 0;

		foreach($this->_statistics as $stat) {
			$order_time = strtotime($stat['created_at']);

			if(strtotime($stat['created_at']) > (time() - (60 * 60 * 24))) {
				$total_day += $stat['total'];
			}

			if(strtotime($stat['created_at']) > (time() - (60 * 60 * 24 * 7))) {
				$total_week += $stat['total'];
			}

			if(strtotime($stat['created_at']) > (time() - (60 * 60 * 24 * 31))) {
				$total_month += $stat['total'];
			}

			$total_forever += $stat['total'];

			if($stat['status'] == 'Paid') {
				++$completed;
			} else {
				++$processing;
			}

			$items += $stat['item_count'];
		}

		if($items) {
			$average_items = number_format($items / ($completed + $processing), 2);
		}
		?>
		<ul class="wc_status_list">
			<li class="processing-orders"><?php printf( _n( "<strong>" . moltin_currency_format($total_day) . "</strong> today", 			"<strong>" . moltin_currency_format($total_day) . "</strong> today", $total_day, 'moltin' ), $total_day ); ?></li>
			<li class="processing-orders"><?php printf( _n( "<strong>" . moltin_currency_format($total_week) . "</strong> this week", 	"<strong>" . moltin_currency_format($total_week) . "</strong> this week", $total_week, 'moltin' ), $total_week ); ?></li>
			<li class="processing-orders"><?php printf( _n( "<strong>" . moltin_currency_format($total_month) . "</strong> this month", 	"<strong>" . moltin_currency_format($total_month) . "</strong> this month", $total_month, 'moltin' ), $total_month ); ?></li>
			<li class="processing-orders"><?php printf( _n( "<strong>" . moltin_currency_format($total_forever) . "</strong> in total", 	"<strong>" . moltin_currency_format($total_forever) . "</strong> in total", $total_forever, 'moltin' ), $total_forever ); ?></li>

			<li class="processing-orders"><?php printf( _n( "<strong>%s order</strong> completed", "<strong>%s orders</strong> completed", $processing, 'moltin' ), $processing ); ?></li>
			<li class="processing-orders"><?php printf( _n( "<strong>%s order</strong> processing", "<strong>%s orders</strong> processing", $completed, 'moltin' ), $completed ); ?></li>

			<li class="processing-orders"><?php printf( _n( "<strong>%s item</strong> sold", "<strong>%s items</strong> sold", $items, 'moltin' ), $items ); ?></li>
			<li class="processing-orders"><?php printf( _n( "Average of <strong>%s item</strong> per order", "Average of <strong>%s items</strong> per order", $average_items, 'moltin' ), $average_items ); ?></li>
		</ul>
		<?php
	}

	/**
	 * Recent reviews widget
	 */
	public function recent_sales() {
		global $wpdb;
		
		echo '<ul>';

		$count = 0;

		foreach($this->_statistics as $stat) {
			echo '<li>';

				echo get_avatar( $stat['customer']['data']['email'], '32' );

				echo '<h4 class="meta">' . $stat['customer']['first_name'] . ' ' . $stat['customer']['last_name'] . '</h4>';

				echo '<blockquote>' . moltin_currency_format($stat['total'], $stat['currency']['data']['format'], $stat['currency']['data']['decimal_point'], $stat['currency']['data']['thousand_point']) . ' &middot; ' . _n( "<strong>" . $stat['item_count'] . " item</strong>", "<strong>" . $stat['item_count'] . " items</strong>", $stat['item_count'], 'moltin' ) . ' &middot; ' . date('h:i \- jS M Y', strtotime($stat['created_at'])) . '</blockquote>';

			echo '</li>';

			if(++$count == 5) {
				break;
			}
		}
		
		echo '</ul>';
		
	}

}

endif;

return new Moltin_Admin_Dashboard();
