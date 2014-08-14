<?php
// Creating the widget
class Moltin_Widget_LatestProducts extends WP_Widget {

	public function __construct() {
		parent::__construct(
			// Base ID of your widget
			'Moltin_Widget_LatestProducts', 

			// Widget name will appear in UI
			__('Moltin: Latest Products', 'moltin'), 

			// Widget description
			array( 'description' => __( 'The latest products.', 'moltin' ), ) 
		);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$count = (int) $instance['count'];
		
		include('views/html-widget-front-latest_products.php');
	}
	
	public function form( $instance ) {
		$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'Latest Products', 'moltin' );
		$count = (int) $instance[ 'count' ];

		// Widget admin form
		include('views/html-widget-admin-latest_products.php');
	}
	
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['count'] = ( ! empty( $new_instance['count'] ) ) ? strip_tags( $new_instance['count'] ) : '';
	
		return $instance;
	}
}

register_widget( 'Moltin_Widget_LatestProducts' );