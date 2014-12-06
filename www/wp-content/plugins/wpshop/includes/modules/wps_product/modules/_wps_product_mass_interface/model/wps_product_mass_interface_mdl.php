<?php
/**
 * Model file for product mass modification module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */

/**
 * Model class for product mass modification module
 *
 * @author Eoxia development team <dev@eoxia.com>
 * @version 1.0
 */
class wps_product_mass_interface_mdl extends wps_product_mdl {

	/**
	 * Returns Products with post data and its attributes configuration
	 * @param integer $limit
	 * @param integer $count_products
	 * @return array
	 */
	function get_quick_interface_products( $start_limit = 0, $nb_product_per_page = 20 ) {
		global $wpdb;

		$products_data = array();
		// Get products in queried limits
		$query = $wpdb->prepare(
			"SELECT *
			FROM {$wpdb->posts}
			WHERE post_type = %s
				AND post_status IN ( 'publish', 'draft' )
			ORDER BY ID DESC
			LIMIT " . $start_limit * $nb_product_per_page . ", {$nb_product_per_page}"
			, WPSHOP_NEWTYPE_IDENTIFIER_PRODUCT );
		$products = $wpdb->get_results( $query );

		if( !empty($products) ) {
			foreach( $products as $product ) {
				// For each product stock Post Datas and attributes definition
				$tmp = array();
				$tmp['post_datas'] = $product;
				$tmp['attributes_datas'] = $this->get_product_atts_def($product->ID);
				$products_data[] = $tmp;
			}
		}
		return $products_data;
	}

}

?>