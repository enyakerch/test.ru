<?php
/**
 * Main controller file for product mass modification module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Main controller class for product mass modification module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */
class wps_product_mass_interface_ctr {

	/**
	 * Instanciate the module: declare scripts, styles, hook wordpress
	 */
	function __construct() {
		// Add submenu
		add_action('admin_menu', array( $this, 'register_mass_products_edit_submenu' ), 350 );
		
		// Declare Styles and JS Files
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts') );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_css' ) );	
		add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );	
		
		// Trigger ajax action
		add_action( 'wp_ajax_wps_mass_edit_change_page', array( $this, 'wps_mass_edit_change_page') );
		add_action ( 'wp_ajax_wps_mass_edit_product_save_action', array( $this, 'wps_save_product_quick_interface') );
		add_action( 'wp_ajax_wps_mass_interface_new_product_creation', array($this, 'wps_mass_interface_new_product_creation' ) );
		add_action( 'wp_ajax_wps_mass_delete_file', array($this, 'wps_mass_delete_file' ) );
		add_action( 'wp_ajax_wps_mass_edit_update_files_list', array($this, 'wps_mass_edit_update_files_list' ) );
	}
	
	function register_mass_products_edit_submenu() {
		add_submenu_page( 'edit.php?post_type=wpshop_product', __('Mass product edit', 'wpshop' ), __('Mass product edit', 'wpshop'), 'manage_options', 'mass_edit_interface', array($this, 'wps_display_mass_edit_interface'));
	}

	/**
	 * Define the aministration pat styles
	 */
	function admin_css() {
		wp_enqueue_style( 'wps-mass-product-update', WPS_PDCT_MASS_URL.'/assets/css/backend.css', '', WPS_PDCT_MASS_VERSION);
	}

	/**
	 * Add javascript to administration
	 */
	function add_admin_scripts() {
		wp_enqueue_script( 'admin_product_js', WPS_PDCT_MASS_URL.'/assets/js/backend.js', '', WPS_PDCT_MASS_VERSION, true);
		wp_enqueue_media();
	}
	
	function admin_print_scripts() {
		$output = '<script type="text/javascript">';
		$output .= 'var WPS_MASS_ERROR_INIT = "' .__( 'An error was occured, the page cannot be initialized', 'wpshop' ). '";';
		$output .= 'var WPS_MASS_ERROR_PRODUCT_CREATION = "' .__( 'An error was occured, the new product cannot be created', 'wpshop' ). '";';
		$output .= 'var WPS_MASS_ERROR_PRODUCT_SAVE = "' .__( 'You must select product to save', 'wpshop' ). '";';
		$output .= '</script>';
		echo $output;
	}

	/**
	 * Create an array with all attributes used
	 *
	 * @param array $attribute_list The list of attributes associated to the product
	 *
	 * @return array
	 */
	function check_attribute_to_display_for_quick_add( $attribute_list ) {
		$quick_add_form_attributes = array();
	
		if ( !empty( $attribute_list ) ) {
			foreach( $attribute_list as $attributes_group ) {
				foreach( $attributes_group as $attributes_sections ) {
					if( !empty($attributes_sections) && !empty($attributes_sections['attributes']) ) {
						foreach( $attributes_sections['attributes'] as $attribute_id => $att_def ) {
							if( !empty($att_def) && !empty($att_def['is_used_in_quick_add_form']) && $att_def['is_used_in_quick_add_form'] == 'yes' ) {
								$quick_add_form_attributes[ $attribute_id ] = $att_def;
							}
						}
					}
				}
			}
		}
	
		return $quick_add_form_attributes;
	}
	
	/**
	 * Display products list tab
	 * @param integer $default : attribute set id
	 * @param integer $page
	 * @return string
	 */
	function display_products_list( $default = 1, $page = 0  ) {
		global $wpdb;
		// Product entity
		$entity_class = new wpshop_entities();
		$product_entity_id = $entity_class->get_entity_identifier_from_code( WPSHOP_NEWTYPE_IDENTIFIER_PRODUCT );
		
		$user_id = get_current_user_id();
		// Check product limit
		$product_limit = get_user_meta( $user_id, 'edit_wpshop_product_per_page', true );
		$product_limit = ( !empty($product_limit) ) ? $product_limit : 20;
		
		// Get products for the current page
		$wps_product_mass_interface_mdl = new wps_product_mass_interface_mdl();
		$products = $wps_product_mass_interface_mdl->get_quick_interface_products( $default, $page, $product_limit );
		
		// Construct Table Head Data
		$quick_add_form_attributes = array();
		if( !empty($products) ) {
			foreach( $products as $product ) {
				if( !empty($product) && !empty( $product['attributes_datas'] ) ) {
					$quick_add_form_attributes = $this->check_attribute_to_display_for_quick_add( $product['attributes_datas'] );
				}
			}
		}
	
		ob_start();
		require( wpshop_tools::get_template_part( WPS_PDCT_MASS_DIR, WPS_PDCT_MASS_TEMPLATES_MAIN_DIR, "backend", "quick_add_interface", "product_list" ) );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	/**
	 * Display interafce
	 */
	function wps_display_mass_edit_interface() {
		global $wpdb;
		
		$wps_product_mass_interface_mdl = new wps_product_mass_interface_mdl();
		$products_attributes_groups = $wps_product_mass_interface_mdl->get_product_attributes_sets();
		
		$default = '';
		if( !empty($products_attributes_groups) ) {
			foreach( $products_attributes_groups as $products_attributes_group ) {
				if( !empty($products_attributes_group->default_set) && $products_attributes_group->default_set == 'yes' ) {
					$default = $products_attributes_group->id;
				}
			}
		}
		// Check page
		$page = ( !empty( $_GET[ 'page' ] ) && is_int( (int)$_GET[ 'page' ] ) && $_GET[ 'page' ] > 0 ) ? (int)($_GET[ 'page' ] - 1) : 0;
		// Display product tab
		$product_list_interface = $this->display_products_list( $default, $page );

		// Get pagination
		$pagination = $this->get_products_pagination( $page, $default );
		
		require( wpshop_tools::get_template_part( WPS_PDCT_MASS_DIR, WPS_PDCT_MASS_TEMPLATES_MAIN_DIR, "backend", "quick_add_interface" ) );
	}

	/**
	 * Display pagination
	 *
	 * @return string
	 */
	function get_products_pagination( $current_page, $attribute_set_id = 1 ) {
		global $wpdb;
		$user_id = get_current_user_id();
		$output = '';
		/**	Define the element number per page. If the user change the default value, take this value	*/
		$one_page_limit = get_user_meta( $user_id, 'edit_wpshop_product_per_page', true );
		$one_page_limit = ( !empty($one_page_limit) ) ? $one_page_limit : 20;
	
		/**	Count the number of product existing in the shop	*/
		$query = $wpdb->prepare(
				"SELECT COUNT( * ) AS products_number
				FROM {$wpdb->posts}, {$wpdb->postmeta}
				WHERE post_type = %s
				AND ID = post_id
				AND meta_key = %s
				AND meta_value = %s
				AND post_status IN ( 'publish', 'draft' )",
				WPSHOP_NEWTYPE_IDENTIFIER_PRODUCT, '_wpshop_product_attribute_set_id', $attribute_set_id);
		$products = $wpdb->get_var( $query );
	
			if( !empty($products) ) {
			$args = array(
			'base' => '%_%',
			'format' => admin_url( 'admin-ajax.php?action=wps_add_quick_interface&page=%#%' ),
					'current' => ( $current_page + 1 ),
					'total' => ceil( $products / $one_page_limit ),
						'type' => 'array',
						'prev_next' => false,
						'show_all' => true,
			);
			$paginate = paginate_links( $args );
	
				$wps_product_ctr = new wps_product_ctr();
				ob_start();
				require( wpshop_tools::get_template_part( WPS_PDCT_MASS_DIR, WPS_PDCT_MASS_TEMPLATES_MAIN_DIR, "backend", "quick_add_interface_pagination" ) );
				$output = ob_get_contents();
			ob_end_clean();
			}
			return $output;
			}

	function wps_product_attached_files( $product_id ) {
		global $wpdb;
		$output = '';
		$query = $wpdb->prepare( 'SELECT * FROM ' .$wpdb->posts. ' WHERE post_parent = %d AND post_type = %s AND post_mime_type LIKE %s', $product_id, 'attachment', 'application%' );
		$files = $wpdb->get_results( $query );
		if( !empty($files) ) {
			ob_start();
			require( wpshop_tools::get_template_part( WPS_PDCT_MASS_DIR, WPS_PDCT_MASS_TEMPLATES_MAIN_DIR, "backend", "quick_add_interface", "attached_files_list" ) );
			$output = ob_get_contents();
			ob_end_clean();	
		}
		return $output;
	}
			
			
	/**
	 * AJAX - Change page action on mass edit product interface
	 */
	function wps_mass_edit_change_page() {
		$status = false; $response = '';
		$page = ( !empty($_POST['page_id']) ) ? intval( $_POST['page_id'] ) - 1 : 0;
		$attribute_set_id = ( !empty($_POST['att_set_id']) ) ? intval( $_POST['att_set_id'] ) : 1;
		if( !empty($attribute_set_id) ) {
			// Get tab
			$response = $this->display_products_list( $attribute_set_id, $page );
			$pagination = $this->get_products_pagination( $page, $attribute_set_id );
			if( !empty($response) ) {
				$status = true;
			}
		}
		echo json_encode( array( 'status' => $status, 'response' => $response, 'pagination' => $pagination ) );
		wp_die();
	}
	
	/**
	 * AJAX - Create a draft product and display the line allowing to edit informations for this product
	 */
	function wps_mass_interface_new_product_creation() {
		global $wpdb;
		$output = $pagination = '';
		$status = false;
	
		$new_product_id = wp_insert_post( array(
				'post_type' => WPSHOP_NEWTYPE_IDENTIFIER_PRODUCT,
				'post_status' => 'publish',
				'post_title' => __( 'New product', 'wpshop' ),
		) );
	
		if( !is_object($new_product_id) ) {
			$status = true;
		}
		if ( !empty( $new_product_id ) ) {
			$product_attribute_set_id = ( !empty($_POST['attributes_set']) ) ? intval( $_POST['attributes_set'] ) : 1;
			update_post_meta( $new_product_id, '_' . WPSHOP_NEWTYPE_IDENTIFIER_PRODUCT . '_attribute_set_id', $product_attribute_set_id );
		}
	
		echo json_encode( array( 'status' => $status, 'response' => $output, 'pagination' => $pagination ) );
		wp_die();
	}
	
	/**
	 * AJAX - Save datas
	 */
	function wps_save_product_quick_interface() {
		global $wpdb;
		$response = ''; $status = false;
		$count_products_to_update = 0; $total_updated_products = 0;
	
	
		if( !empty($_REQUEST['wps_product_quick_save']) ) {
			$count_products_to_update = count( $_REQUEST['wps_product_quick_save'] );
			foreach( $_REQUEST['wps_product_quick_save'] as $product_to_save ) {
				$data_to_save = array();
				// Update post
				$updated_post = wp_update_post( array( 'ID' => $product_to_save,
						'post_title' => $_REQUEST['wps_mass_interface'][$product_to_save]['post_title'],
						'post_content' => $_REQUEST['wps_mass_interface'][$product_to_save]['post_content'], 
						)
				);
				// Update attributes
				if( !empty($updated_post) ) {
					// Update Featured picture
					if( !empty($_REQUEST['wps_mass_interface'][$product_to_save]['picture']) ) {
						wp_update_post( array('ID' => $_REQUEST['wps_mass_interface'][$product_to_save]['picture'], 'post_parent' => $updated_post) );
						update_post_meta( $updated_post, '_thumbnail_id', $_REQUEST['wps_mass_interface'][$product_to_save]['picture'] );
					}
					
					// Update files datas
					if( !empty($_REQUEST['wps_mass_interface'][$product_to_save]['files']) ) {
						$files_data = explode( ',', $_REQUEST['wps_mass_interface'][$product_to_save]['files'] );
						if( !empty($files_data) && is_array($files_data) ) {
							foreach( $files_data as $file_id ) {
								if( !empty($file_id) ) {
									wp_update_post( array('ID' => $file_id, 'post_parent' => $updated_post) );
								}
							}
						}
					}
					
					$data_to_save['post_ID'] = $data_to_save['product_id'] = intval( $product_to_save );
					$data_to_save['wpshop_product_attribute'] = ( !empty($_REQUEST['wpshop_product_attribute'][ $product_to_save ]) ) ? $_REQUEST['wpshop_product_attribute'][ $product_to_save ] : array();
					$data_to_save['user_ID'] = get_current_user_id();
					$data_to_save['action'] = 'editpost';
					if( !empty($product_to_save) && !empty( $data_to_save['user_ID'] ) ) {
						$product_class = new wpshop_products();
						$product_class->save_product_custom_informations( $product_to_save, $data_to_save );
						$total_updated_products++;
					}
				}
			}
		}
		// Checking status
		$status = ( $total_updated_products == $count_products_to_update ) ? true : false;
	
		if( $status ) {
			$response = sprintf( __( '%d products have been successfully updated', 'wpshop'), $total_updated_products );
		}
		else {
			if( !empty($total_updated_products) ) {
				$response = sprintf( __( 'All selected products do not be updated. %d on %d products have been successfully updated', 'wpshop'), $total_updated_products, $count_products_to_update );
			}
			else {
				$response = __( 'No product have been updated', 'wpshop');
			}
		}
	
		echo json_encode( array('status' => $status, 'response' => $response ) );
		wp_die();
	}
	
	/**
	* Delete product list
	**/
	function wps_mass_delete_file() {
		$status = false;
		$file_id = (!empty($_POST['file_id']) ) ? intval($_POST['file_id']) : null;
		if( !empty($file_id) ) {
			wp_update_post( array('ID' => $file_id, 'post_parent' => 0) );
			$status = true;
		}
		echo json_encode( array( 'status' => $status ) );
		wp_die();
	}
	
	/**
	* Update product files list
	*/
	function wps_mass_edit_update_files_list() {
		$status = false; $response = '';
		$product_id = ( !empty($_POST['product_id']) ) ? intval($_POST['product_id']) : null;
		$files = ( !empty($_POST['files']) ) ? intval($_POST['files']) : null;
		if( !empty($product_id ) ) {
			// Update files datas
			if( !empty($files) ) {
				$files_data = explode( ',', $files );
				if( !empty($files_data) && is_array($files_data) ) {
					foreach( $files_data as $file_id ) {
						if( !empty($file_id) ) {
							wp_update_post( array('ID' => $file_id, 'post_parent' => $product_id) );
						}
					}
				}
			}
		
		
			$response = $this->wps_product_attached_files( $product_id );
			$status = true;
		}
		echo json_encode( array( 'status' => $status, 'response' => $response ) );
		wp_die();
	}
	
}

?>