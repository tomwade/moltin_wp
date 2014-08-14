<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Moltin_Options' ) ) :

/**
 * Moltin_Install Class
 */
class Moltin_Options {

    public function __construct() {
        add_action('admin_init', array($this, 'theme_settings_init'));
        add_action('admin_menu', array($this, 'add_settings_page'));
    }

    /*---------------------------------------------------
        register settings
    ----------------------------------------------------*/
    function theme_settings_init(){
    	register_setting( 'theme_settings', 'theme_settings' );
    }
     
    /*---------------------------------------------------
        add settings page to menu
    ----------------------------------------------------*/
    function add_settings_page() {
    	add_menu_page( __( 'Moltin', 'moltin' ), __( 'Moltin', 'moltin' ), 'manage_options', 'settings', array($this, 'theme_settings_page'), plugins_url('../../assets/backend/images/icon.png', __FILE__));
    }
    
    /*---------------------------------------------------
    Theme Panel Output
    ----------------------------------------------------*/
    function theme_settings_page() {

    	/* ---------------------------------------------------------
    	Declare options
    	----------------------------------------------------------- */
    	 
    	$theme_options = array (
    	 
    	    array( 'name' => 'Moltin Options', "type" => "title"),
    	 
    	    /* ---------------------------------------------------------
    	    	API section
    	    ----------------------------------------------------------- */
    	    array('name' => 'Moltin Account Information', 'type' => 'section'),
    	    array('type' => 'open'),
    	 
    	    array( 
    	    		"name" 	=> 'Client Key',
    				"desc" 	=> 'Please enter your Molton client key',
    	    		"id" 	=> 'moltin_key',
    	    		"type" 	=> 'text',
    	    		"std" 	=> '',
    	    		),

    	    array( 
    	    		"name" 	=> 'Client Sercret',
    				"desc" 	=> 'Please enter your Molton client secret',
    	    		"id" 	=> 'moltin_secret',
    	    		"type" 	=> 'text',
    	    		"std" 	=> '',
    	    		),

            array( 
                    "name"  => 'Dev Mode',
                    "desc"  => 'If turned on, will display additional dev information.',
                    "id"    => 'moltin_dev',
                    "type"  => 'checkbox',
                    "std"   => '',
                    ),
    	 	
    	    array('type' => 'close'),

            /* ---------------------------------------------------------
                URL section
            ----------------------------------------------------------- */
            array('name' => 'URL Information', 'type' => 'section'),
            array('type' => 'open'),
         
            array( 
                    "name"  => 'Store slug',
                    "desc"  => 'Please enter your stores base URL. <em>(e.g. /shop/)</em>',
                    "id"    => 'store_base_uri',
                    "type"  => 'text',
                    "std"   => '',
                    ),

            array( 
                    "name"  => 'Product slug',
                    "desc"  => 'Please enter your products URL. <em>(e.g. /product/)</em>',
                    "id"    => 'store_product_uri',
                    "type"  => 'text',
                    "std"   => '',
                    ),

            array( 
                    "name"  => 'Category slug',
                    "desc"  => 'Please enter your category URL. <em>(e.g. /category/)</em>',
                    "id"    => 'store_category_uri',
                    "type"  => 'text',
                    "std"   => '',
                    ),
            
            array('type' => 'close'),
    	
            /* ---------------------------------------------------------
                General section
            ----------------------------------------------------------- */
            array('name' => 'Store Information', 'type' => 'section'),
            array('type' => 'open'),
         
            array( 
                    "name"  => 'Results per page',
                    "desc"  => 'Results to display per page. <em>(default: 16)</em>',
                    "id"    => 'store_results_per_page',
                    "type"  => 'text',
                    "std"   => '',
                    ),

            array( 
                    "name"  => 'Store page ID',
                    "desc"  => 'ID of the cart page',
                    "id"    => 'store_front_page_id',
                    "type"  => 'text',
                    "std"   => '',
                    ),

            array( 
                    "name"  => 'Cart page ID',
                    "desc"  => 'ID of the cart page',
                    "id"    => 'store_cart_page_id',
                    "type"  => 'text',
                    "std"   => '',
                    ),
            
            array('type' => 'close'),
        );

        $i=0;
        $message=''; 
        if ( 'save' == $_REQUEST['action'] ) {
          
            foreach ($theme_options as $value) {
                update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
          
            foreach ($theme_options as $value) {
                if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
            $message='saved';
        }
        else if( 'reset' == $_REQUEST['action'] ) {
              
            foreach ($theme_options as $value) {
                delete_option( $value['id'] ); }
            $message='reset';        
        }
      
        ?>
        <div class="wrap options_wrap">
            <div id="icon-options-general"></div>
            <h2><?php _e( 'Moltin Options' ) //your admin panel title ?></h2>
            <?php
            if ( $message=='saved' ) echo '<div class="updated settings-error" id="setting-error-settings_updated"> 
            <p>'.$themename.' settings saved.</strong></p></div>';
            if ( $message=='reset' ) echo '<div class="updated settings-error" id="setting-error-settings_updated"> 
            <p>'.$themename.' settings reset.</strong></p></div>';
            ?>
            <div class="content_options">
                <form method="post">
      
                <?php foreach ($theme_options as $value) {
              
                    switch ( $value['type'] ) {
                  
                        case "open": ?>
                        <?php break;
                      
                        case "close": ?>
                        </div>
                        </div><br />
                        <?php break;
                      
                        case "title": ?>
                        <div class="message">
                            <p>To easily use the <?php echo $themename;?> theme options, you can use the options below.</p>
                        </div>
                        <?php break;
                      
                        case 'text': ?>
                        <div class="option_input option_text">
                        <label for="<?php echo $value['id']; ?>">
                        <?php echo $value['name']; ?></label>
                        <input id="" type="<?php echo $value['type']; ?>" name="<?php echo $value['id']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'])  ); } else { echo $value['std']; } ?>" />
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                        </div>
                        <?php break;
                      
                        case 'textarea': ?>
                        <div class="option_input option_textarea">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <textarea name="<?php echo $value['id']; ?>" rows="" cols=""><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id']) ); } else { echo $value['std']; } ?></textarea>
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                        </div>
                        <?php break;
                      
                        case 'select': ?>
                        <div class="option_input option_select">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
                        <?php foreach ($value['options'] as $option) { ?>
                                <option <?php if (get_settings( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option>
                        <?php } ?>
                        </select>
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                        </div>
                        <?php break;
                      
                        case "checkbox": ?>
                        <div class="option_input option_checkbox">
                        <label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
                        <?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
                        <input id="<?php echo $value['id']; ?>" type="checkbox" name="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> /> 
                        <small><?php echo $value['desc']; ?></small>
                        <div class="clearfix"></div>
                        </div>
                        <?php break;
                      
                        case "section": 
    					?>
                        <div class="input_section">
                        <div class="input_title">
                             
                            <h3><span class="wp-menu-image dashicons-before dashicons-admin-generic" style="position: relative; top: -3px">&nbsp;</span> <?php echo $value['name']; ?></h3>
                            <span class="submit"><input name="save<?php echo ++$i; ?>" type="submit" class="button-primary" value="Save changes" /></span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="all_options">
                        <?php break;
                         
                    }
                } ?>
              	<input type="hidden" name="action" value="save" />
              </form>
            </div>
        </div>
        <?php
    }

}

endif;

return new Moltin_Options();