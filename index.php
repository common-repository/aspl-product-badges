<?php

/*  Plugin Name: ASPL Product Badges
	Plugin URI: https://acespritech.com/services/wordpress-extensions/
	Description: This plugin uses a nice flash-tag functionality to highlight the products with a label. 
	Author: Acespritech Solutions Pvt. Ltd.
	Author URI: https://acespritech.com/
	Version: 1.1.0
	Domain Path: /languages/
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	

	} else {
		deactivate_plugins(plugin_basename(__FILE__));
	    add_action( 'admin_notices', 'aspl_pb_woocommerce_not_installed' );
	}

	/*	
	 *	Add Setting Link On Plugin List Table - Deactive | Setting  
	*/	

	function aspl_pb_plugin_add_settings_link( $links ) {
	    $settings_link = '<a href="admin.php?page=wc-settings&tab=products&section=aspl_product_badges">' . __( 'Settings' ) . '</a>;';
	    array_push( $links, $settings_link );
	    return $links;
	}
	$plugin = plugin_basename( __FILE__ );
	add_filter( "plugin_action_links_$plugin", 'aspl_pb_plugin_add_settings_link' );

	/*	
	 *	Create Admin Notice - Active Woocommerce Plugin 
	*/

	function aspl_pb_woocommerce_not_installed()
	{
	    ?>
	    <div class="error notice">
	      <p><?php _e( 'You need to install and activate WooCommerce to use ASPL Product Badges !', 'ASPL-Product-Badges' ); ?></p>
	    </div>
	    <?php
	}

	/*	
	 *	Include Css and Js Admin Side
	*/

	function aspl_pb_css_hook_admin() {
		wp_enqueue_script('jquery');
		wp_enqueue_media();
	    wp_enqueue_script( 'aspl_pb_custom_js', plugin_dir_url(__FILE__) . 'js/aspl_pb_custom_js.js', array('jquery')); 
	    wp_enqueue_script('media-uploader');
	}
	add_action('admin_enqueue_scripts', 'aspl_pb_css_hook_admin');

	/*	
	 *	Create Fields in Inventory Product Data Tab
	*/

	add_action( 'woocommerce_product_options_inventory_product_data', 'add_aspl_pb_custom_product_data_fields' );
	function add_aspl_pb_custom_product_data_fields() {
	    global $woocommerce, $post;
	    ?>
	    	<h3 class="form-field" style="margin-left: 10px;">Product Badges</h3>
        <?php
        woocommerce_wp_checkbox( array( 
            'id'            => '_aspl_pb_new_check', 
            'label'         => __( 'New Product', 'aspl_pb' ),
            'description'   => __( 'Check this checkbox if you add lable as "new" in this product.', 'aspl_pb' ),
            'default'       => '0',
            'desc_tip'      => false,
        ) );

	}

	/*	
	 *	Save a Custom Product Tab Fields Data
	*/

	add_action( 'woocommerce_process_product_meta', 'aspl_pb_woocommerce_process_product_meta_fields_save' );
	function aspl_pb_woocommerce_process_product_meta_fields_save( $post_id ){

	    $woo_checkbox = isset( $_POST['_aspl_pb_new_check'] ) ? 'yes' : 'no';
	    update_post_meta( $post_id, '_aspl_pb_new_check', $woo_checkbox );

	}

	/*	
	 *	Add Lable Image in product image
	*/

	function aspl_pb_action_woocommerce_before_shop_loop_item_title() { 

	    global $product , $post;
	    $product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;

	    $new = get_post_meta( $post->ID, '_aspl_pb_new_check', true );
	    $soldout = get_option('aspl_soldout_setting_check');
	    // var_dump($soldout);

	  	$img_new = get_option('aspl_pb_new_lable_image');
	  	$img_soldout = get_option('aspl_pb_soldout_lable_image');

	  	$new_lable_position = get_option('aspl_new_lable_position');
	  	$soldout_lable_position = get_option('aspl_soldout_lable_position');

	    if($new == 'yes'){
	    	if ($new_lable_position == 'right') {
	    		?>
	    			<img class="aspl-flase-image-right" style="position: absolute;right: 0;" width="100px" height="100px" src="<?php _e($img_new); ?>">
		    	<?php
	    	}else{
				?>
	    			<img width="100px" height="100px" style="position: absolute;left: 0;" src="<?php _e($img_new); ?>">
		    	<?php
	    	}
	    }

		if($soldout == 'yes'){
			if ( ! $product->managing_stock() && ! $product->is_in_stock() ){

		    	if ($soldout_lable_position == 'right') {
		    		if ($new_lable_position == 'right') {
		    			?>
		    			<img class="aspl-flase-image-right" style="position: absolute;right: 0; top: 70px;" width="100px" height="100px" src="<?php _e($img_soldout); ?>">
				    	<?php	
		    		}else{	
			    		?>
			    			<img class="aspl-flase-image-right" style="position: absolute;right: 0;" width="100px" height="100px" src="<?php _e($img_soldout); ?>">
				    	<?php
		    		}

		    	}else{
		    		if ($new_lable_position == 'left') {
		    			?>
			    			<img width="100px" height="100px" style="position: absolute;left: 0; top:70px;" src="<?php _e($img_soldout); ?>">
				    	<?php
		    		}else{
						?>
			    			<img width="100px" height="100px" style="position: absolute;left: 0;" src="<?php _e($img_soldout); ?>">
				    	<?php
		    		}
		    	}
		    }
	    }	    

	}
	add_action( 'woocommerce_before_shop_loop_item_title', 'aspl_pb_action_woocommerce_before_shop_loop_item_title' );


	/*
	 * Create the section in Woocommerce setting products tab
	*/

	add_filter( 'woocommerce_get_sections_products', 'aspl_pb_product_badges_section' );
	function aspl_pb_product_badges_section( $sections ) {
			
		$sections['aspl_product_badges'] = __( 'Product Badges', 'woocommerce-aspl' );
		return $sections;
			
	}

	add_filter( 'woocommerce_get_settings_products', 'aspl_pb_product_badges_settings', 10, 2 );
	function aspl_pb_product_badges_settings( $settings, $current_section ) {

		if ( $current_section == 'aspl_product_badges' ) {

			$settings_qa = array();
				
			$settings_qa[] = array( 'name' => __( 'Product Badges', 'woocommerce-aspl' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure Product Badges.', 'woocommerce-qa' ), 'id' => 'aspl_product_badges' );

			$settings_qa[] = array(
				'name'     => __( 'New Lable Image Path', 'woocommerce' ),
				'id'       => 'aspl_pb_new_lable_image',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
			);

			$settings_qa[] = array(
				'name' => __( 'Insert Image' ),
				'type' => 'button',
				'desc_tip' => true,
				'class' => 'button-secondary',
				'id'	=> 'upload_aspl_pb_new_lable_image',
			);

			$settings_qa[] = array(
			    'name'    => __( 'New Lable Position', 'woocommerce' ),
			    'desc'    => __( '', 'woocommerce' ),
			    'id'      => 'aspl_new_lable_position',
			    'css'     => 'min-width:150px;',
			    'std'     => 'left', 	// WooCommerce < 2.0
			    'default' => 'left', 	// WooCommerce >= 2.0
			    'type'    => 'select',
			    'options' => array(
						      'left'        => __( 'Left', 'woocommerce' ),
						      'right'       => __( 'Right', 'woocommerce' )
						    ),
			    'desc_tip' =>  true,
			  );

			$settings_qa[] = array(
			    'name'    => __( 'Enable Sold-out Lable', 'woocommerce' ),
			    'desc'    => __( '', 'woocommerce' ),
			    'id'      => 'aspl_soldout_setting_check',
			    'std'     => 'yes', 	// WooCommerce < 2.0
			    'default' => 'no', 		// WooCommerce >= 2.0
			    'type'    => 'checkbox'
			);

			$settings_qa[] = array(
				'name'     => __( 'Sold-Out Lable Image Path', 'woocommerce' ),
				'id'       => 'aspl_pb_soldout_lable_image',
				'type'     => 'text',
				'css'      => 'min-width:300px;',
			);

			$settings_qa[] = array(
				'name' => __( 'Insert Image' ),
				'type' => 'button',
				'desc_tip' => true,
				'class' => 'button-secondary',
				'id'	=> 'upload_aspl_pb_soldout_lable_image',
			);

			$settings_qa[] = array(
			    'name'    => __( 'Sold-Out Lable Position', 'woocommerce' ),
			    'desc'    => __( '', 'woocommerce' ),
			    'id'      => 'aspl_soldout_lable_position',
			    'css'     => 'min-width:150px;',
			    'std'     => 'right', 	// WooCommerce < 2.0
			    'default' => 'right', 	// WooCommerce >= 2.0
			    'type'    => 'select',
			    'options' => array(
						      'left'        => __( 'Left', 'woocommerce' ),
						      'right'       => __( 'Right', 'woocommerce' )
						    ),
			    'desc_tip' =>  true,
			  );

			$settings_qa[] = array( 'type' => 'sectionend', 'id' => 'aspl_product_badges' );

			return $settings_qa;
			
		}else{

			return $settings;
		
		}
	}

	/*
	 * Create image upload button in setting.
	*/

	add_action( 'woocommerce_admin_field_button' , 'aspl_pb_add_admin_field_button' );

	function aspl_pb_add_admin_field_button( $value ){
	    $option_value = (array) WC_Admin_Settings::get_option( $value['id'] );
	    $description = WC_Admin_Settings::get_field_description( $value );
	    
	    ?>

	    <tr valign="top">
	        <th scope="row" class="titledesc">
	        </th>
	        <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	             <input
	                    name ="<?php echo esc_attr( $value['name'] ); ?>"
	                    id   ="<?php echo esc_attr( $value['id'] ); ?>"
	                    type ="submit"
	                    style="<?php echo esc_attr( $value['css'] ); ?>"
	                    value="<?php echo esc_attr( $value['name'] ); ?>"
	                    class="<?php echo esc_attr( $value['class'] ); ?>"
	            /> 
	        </td>
	    </tr>
	<?php       
	}

