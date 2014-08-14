<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Moltin_Admin_Menus' ) ) :

class Moltin_Admin_Menus {

	private $_menu_slug = 'moltin_menu';

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Only hook in admin parts if the user has admin access
		if ( is_admin() ) {
			$this->init();
		}
	}

	/**
	 * Init dashboard widgets
	 */
	public function init() {

		// Genearte menu
		add_action('generate_rewrite_rules', array( &$this, 'generate_category_menu'));

	}

	public function generate_category_menu() {

		// Get a list of all the categories
		$categories = moltin_categories();

		// Assign a unique menu slug
		$menu_name  = $this->_menu_slug;

		// Check if the menu exists
		$menu_exists = wp_get_nav_menu_object( $menu_name );

		// If it doesn't exist, let's create it.
		if(!$menu_exists) {
			// Create the menu instance
		    $menu_id = wp_create_nav_menu($menu_name);

		    // Loop through the categories
		    $this->generate_category_menu_children($menu_id, $categories, 0);
		}
	}

	private function generate_category_menu_children($menu_id, $categories, $parent_id = 0) {
		// Check that child categories are set
		if($categories) {
			// Loop through the child categories
		    foreach($categories as $category) {
		    	// Create a new nav item for each
		    	$nav_item = wp_update_nav_menu_item($menu_id, 0, array(
		    		// Assign the navigation title
		        	'menu-item-title' 		=> $category['title'],

		        	// Assign a class to it for styling purposes
		        	'menu-item-classes' 	=> 'category-' . $category['id'],

		        	// Generate the permalink
		        	'menu-item-url' 		=> category_link($category),

		        	// Publish it straight away
		        	'menu-item-status' 		=> 'publish',

		        	// Set the parent ID
		        	'menu-item-parent-id' 	=> $parent_id
		        ));

		    	// Add any child categories that may be present
		        $this->generate_category_menu_children($menu_id, $category['children'], $nav_item);
		    }
		}
	}

}

endif;

return new Moltin_Admin_Menus();
