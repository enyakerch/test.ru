<?php
class wps_customer_ctr {
	
	function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box_to_customer_entity') );
		add_action( 'admin_init', array( $this, 'wps_customer_admin_int_actions' ) );
		add_action('admin_init', array( $this, 'customer_action_on_plugin_init'));
	}
	
	/**
	 * Admin init actions
	 */
	function wps_customer_admin_int_actions() {
		if ( !empty($_GET['download_users']) ) {
			$this->download_newsletters_users( $_GET['download_users'] );
		}
	}
	
	/**
	 * Add Meta Box to Customer Entity
	 */
	function add_meta_box_to_customer_entity() {
		add_meta_box( 'wps_customer_informations', __( 'Customer\'s account informations', 'wpshop' ), array( $this, 'wps_customer_account_informations' ), WPSHOP_NEWTYPE_IDENTIFIER_CUSTOMERS, 'normal', 'low' );
		add_meta_box( 'wps_customer_orders', __( 'Customer\'s orders', 'wpshop' ), array( $this, 'wps_customer_orders_list' ), WPSHOP_NEWTYPE_IDENTIFIER_CUSTOMERS, 'normal', 'low' );
		add_meta_box( 'wps_customer_addresses_list', __( 'Customer\'s addresses', 'wpshop' ), array( $this, 'wps_customer_addresses_list' ), WPSHOP_NEWTYPE_IDENTIFIER_CUSTOMERS, 'normal', 'low' );
		add_meta_box( 'wps_customer_messages_list', __( 'Customer\'s send messages', 'wpshop' ), array( $this, 'wps_customer_messages_list' ), WPSHOP_NEWTYPE_IDENTIFIER_CUSTOMERS, 'side', 'low' );
		add_meta_box( 'wps_customer_coupons_list', __( 'Customer\'s coupons list', 'wpshop' ), array( $this, 'wps_customer_coupons_list' ), WPSHOP_NEWTYPE_IDENTIFIER_CUSTOMERS, 'side', 'low' );
	}
	
	/**
	 * META-BOX CONTENT - Display customer's order list in customer back-office interface
	 */
	function wps_customer_orders_list() {
		global $post;
		$output = '';
		$wps_orders = new wps_orders_ctr();
		$output = $wps_orders->display_orders_in_account( $post->post_author);
		echo $output;
	}
	
	/**
	 * META-BOX CONTENT - Display Customer's addresses in customer back-office interface
	 */
	function wps_customer_addresses_list() {
		global $post;
		$output = '';
		$wps_addresses = new wps_address();
		$output = $wps_addresses->display_addresses_interface( $post->post_author );
		echo $output;
	}
	
	/**
	 * META-BOX CONTENT - Display customer's send messages
	 */
	function wps_customer_messages_list() {
		global $post;
		$wps_messages = new wps_message_ctr();
		$output = $wps_messages->display_message_histo_per_customer( array(),$post->post_author);
		echo $output;
	}
	
	/**
	 * META-BOX CONTENT - Display wps_customer's coupons list
	 */
	function wps_customer_coupons_list() {
		global $post;
		$wps_customer = new wps_coupon_ctr();
		$output = $wps_customer->display_coupons( $post->post_author );
		echo $output;
	}

	/**
	 * META-BOX CONTENT - Display Customer's account informations in administration panel
	 */
	function wps_customer_account_informations() {
		global $post;
		$wps_account = new wps_account_ctr();
		$output = $wps_account->display_account_informations( $post->post_author );
		echo $output;
	}
	
	/**
	 * Return a list  of users
	 * @param array $customer_list_params
	 * @param integer $selected_user
	 * @param boolean $multiple
	 * @param boolean $disabled
	 * @return string
	 */
	function custom_user_list($customer_list_params = array('name'=>'user[customer_id]', 'id'=>'user_customer_id'), $selected_user = "", $multiple = false, $disabled = false) {
		$content_output = '';
	
		// USERS
		$wps_customer_mdl = new wps_customer_mdl();
		$users = $wps_customer_mdl->getUserList();
		$select_users = '';
		if( !empty($users) ) {
			foreach($users as $user) {
				if ($user->ID != 1) {
					$select_users .= '<option value="'.$user->ID.'"' . ( ( !$multiple ) && ( $selected_user == $user->ID ) ? ' selected="selected"' : '') . ' >'.$user->user_login.' ('.$user->user_email.')</option>';
				}
			}
			$content_output = '
			<select name="' . $customer_list_params['name'] . '" id="' . $customer_list_params['id'] . '" data-placeholder="' . __('Choose a customer', 'wpshop') . '" class="chosen_select"' . ( $multiple ? ' multiple="multiple" ' : '') . '' . ( $disabled ? ' disabled="disabled" ' : '') . '>
				<option value="0" ></option>
				'.$select_users.'
			</select>';
		}
		return $content_output;
	}

	/**
	 * Download a CSV with all customers who subscribe to newsletter
	 * @param string indicator to know which list as been generated
	 */
	function download_newsletters_users( $users_preference_indicator ) {
		require (ABSPATH . WPINC . '/pluggable.php');
		$current_user_def = wp_get_current_user();
		if( !empty($current_user_def) && $current_user_def->ID != 0 && array_key_exists('administrator', $current_user_def->caps) && is_admin() ) {
			$users = get_users();
			$users_array = array();
			if ( !empty( $users ) ) {
				foreach( $users as $user ) {
					$user_preference = get_user_meta( $user->ID, 'user_preferences', true );
					if(  !empty($user_preference) && !empty($user_preference[ $users_preference_indicator ]) ) {
						$tmp_array = array();
						$tmp_array['name'] = get_user_meta( $user->ID, 'last_name', true );
						$tmp_array['first_name'] = get_user_meta( $user->ID, 'first_name', true );
						$tmp_array['email'] = $user->user_email;
	
						$users_array[] = $tmp_array;
					}
				}
			}
	
			$filename = 'newsletter_contacts_' .$users_preference_indicator. '.csv';
			$fp = fopen( $filename, 'w' );
	
			if ( !empty( $users_array ) ) {
				foreach ($users_array as $fields) {
					fputcsv($fp, $fields);
				}
			}
			else {
				fputcsv($fp, array( __( 'No user have selected to receive newsletter', 'wpshop' ), ));
			}
	
			fclose($fp);
			header("Content-type: application/force-download");
			header("Content-Disposition: attachment; filename=".$filename);
			readfile($filename);
	
			unlink( $filename );
			exit;
		}
	}
	
	/** 
	 * Action on plug-on action
	 */
	public static function customer_action_on_plugin_init() {
		global $wpdb;
		$user_meta_for_wpshop = array('metaboxhidden_'.WPSHOP_NEWTYPE_IDENTIFIER_PRODUCT);
	
		/*	Get user list from user meta	*/
		$query = "SELECT ID FROM {$wpdb->users}";
		$user_list = $wpdb->get_results($query);
	
		/*	Get the different meta needed for user in wpshop	*/
		foreach ($user_list as $user) {
			/*	Check if meta exist for each user	*/
			foreach($user_meta_for_wpshop as $meta_to_check){
				$query = $wpdb->prepare("SELECT meta_value FROM ".$wpdb->usermeta." WHERE user_id=%d AND meta_key=%s", $user->ID, $meta_to_check);
				$meta_value = $wpdb->get_var($query);
				if(empty($meta_value)){
					update_user_meta($user->ID, $meta_to_check, unserialize(WPSHOP_PRODUCT_HIDDEN_METABOX));
				}
			}
		}
		return;
	}
}

