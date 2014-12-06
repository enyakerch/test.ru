<?php

add_action("wp_ajax_cart_remove", "cart_remove");
add_action("wp_ajax_nopriv_cart_remove", "cart_remove");
add_action("wp_ajax_cart_save", "cart_save");
add_action("wp_ajax_nopriv_cart_save", "cart_save");
add_action("wp_ajax_cart_load", "cart_load");
add_action("wp_ajax_nopriv_cart_load", "cart_load");
add_action("wp_ajax_set_currency", "set_currency");
add_action("wp_ajax_nopriv_set_currency", "set_currency");
add_action("wp_ajax_ajax_post", "ajax_post");
add_action("wp_ajax_nopriv_ajax_post", "ajax_post");
add_action("wp_ajax_delete_all", "delete_all");
add_action("wp_ajax_nopriv_delete_all", "delete_all");

function ajax_post(){
	if ($_POST['act'] == 'price_options')
  {
    update_option('wpshop_price_under_title', $_POST['under_title']);
  }
	die();
}

function delete_all(){
	global $wpdb;
	$res = $wpdb->query("SET AUTOCOMMIT=0;");
	$res = $wpdb->query("SET FOREIGN_KEY_CHECKS=0;");
	$res = $wpdb->query("DROP TABLE {$wpdb->prefix}wpshop_orders;");
	$res = $wpdb->query("DROP TABLE {$wpdb->prefix}wpshop_ordered;");
	$res = $wpdb->query("DROP TABLE {$wpdb->prefix}wpshop_selected_items;");
	$res = $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type='wpshopcarts';");
	
	die();
} 

function cart_remove(){
	global $wpdb;
	$wpshop_session_id	= session_id();
	$wpshop_id = $_POST['wpshop_id'];

	if ($wpshop_id=="-1"){ // Delete all selected items
		$res = $wpdb->get_results("DELETE FROM {$wpdb->prefix}wpshop_selected_items WHERE selected_items_session_id='".session_id()."'");
	}else{
		// Delete 1 selected item
		$res = $wpdb->get_results("DELETE FROM {$wpdb->prefix}wpshop_selected_items WHERE selected_items_session_id='".session_id()."' and selected_items_id='{$wpshop_id}'");
	}
	die();
} 

function set_currency(){
	global $wpdb;
	update_option('wp-shop-usd',$_POST['usd']);
  update_option('wp-shop-eur',$_POST['eur']);

  $usd_opt = $_POST['usd'];
  $eur_opt = $_POST['eur'];

  $results=$wpdb->get_results("SELECT * FROM $wpdb->posts");

  foreach($results as $row)
  {
    $temp = get_post_custom($row->ID);
    
    foreach($temp as $key => $value)
    {
      if (preg_match('/usd_(\d+)/',$key,$ar))
      {
        $usd = get_post_meta($row->ID,"usd_{$ar[1]}",true);
        
        if (update_post_meta($row->ID,"cost_{$ar[1]}",$usd * $usd_opt) === false)
        {
          add_post_meta($row->ID,"cost_{$ar[1]}",$usd * $usd_opt);
        }
      }
      if (preg_match('/eur_(\d+)/',$key,$ar))
      {
        $eur = get_post_meta($row->ID,"eur_{$ar[1]}",true);
        
        if (update_post_meta($row->ID,"cost_{$ar[1]}",$eur * $eur_opt) === false)
        {
          add_post_meta($row->ID,"cost_{$ar[1]}",$eur * $eur_opt);
        }
      }		
    }
  }
	die();
} 

function cart_save(){
	
	$wpshop_session_id	= session_id();
	$wpshop_item_id		= $_POST['wpshop_id'];
	$wpshop_key		= $_POST['wpshop_key'];
	$wpshop_name		= $_POST['wpshop_name'];
	$wpshop_href		= $_POST['wpshop_href'];
	$wpshop_cost		= $_POST['wpshop_cost'];
	$wpshop_num		= intval($_POST['wpshop_num']);
	$wpshop_sklad		= intval($_POST['wpshop_sklad']);
  
	#$wpshop_session_id	or die();
	#$wpshop_item_id		or die();
	#$wpshop_key			or die();
	#$wpshop_name		or die();
	#$wpshop_href		or die();
	#$wpshop_cost		or die();
	#$wpshop_num			or die();
	
	global $wpdb;
	$rows = $wpdb->get_results("SELECT count(*) as cnt FROM {$wpdb->prefix}wpshop_selected_items WHERE selected_items_session_id='".session_id()."' AND selected_items_id=".$wpshop_item_id);
	$row = $rows[0];
	if ($row->cnt>0){
		$wpdb->get_results("UPDATE {$wpdb->prefix}wpshop_selected_items SET selected_items_num='".$wpshop_num."' WHERE selected_items_session_id='".session_id()."' AND selected_items_id=".$wpshop_item_id);
		echo 'edit';
	}else{
		$data = array(
		
			'selected_items_session_id'	=> $wpshop_session_id,
			'selected_items_item_id'	=> $wpshop_item_id,
			'selected_items_key'		=> $wpshop_key,
			'selected_items_name'		=> $wpshop_name,
			'selected_items_href'		=> $wpshop_href,
			'selected_items_cost'		=> $wpshop_cost,
			'selected_items_num'		=> $wpshop_num,
			'selected_items_sklad'		=> $wpshop_sklad
		);
		$wpdb->insert($wpdb->prefix.'wpshop_selected_items', $data);
		echo 'add';
	}
  die();
	
}

function cart_load(){
	global $wpdb;
	$rows = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpshop_selected_items WHERE selected_items_session_id='".session_id()."'");
	$n=0;
	echo "window.__cart.a_thumbnail = [];\n";
	foreach ($rows as $row){
		echo "window.__cart.a_id[$n]   = \"$row->selected_items_id\";";
		echo "window.__cart.a_key[$n]  = \"$row->selected_items_key\";";
		echo "window.__cart.a_name[$n] = \"$row->selected_items_name\";";
		echo "window.__cart.a_href[$n] = \"$row->selected_items_href\";";
		echo "window.__cart.a_cost[$n] = \"$row->selected_items_cost\";";
		echo "window.__cart.a_num[$n]  = \"$row->selected_items_num\";";
		echo "window.__cart.a_sklad[$n]  = \"$row->selected_items_sklad\";";
		

		$thumbnail = get_post_meta($row->selected_items_item_id,'Thumbnail',true);
		$thumbnail1 = wp_get_attachment_url( get_post_thumbnail_id($row->selected_items_item_id) );
		if (!$thumbnail&&$thumbnail1) {
			$thumbnail = wp_get_attachment_url( get_post_thumbnail_id($row->selected_items_item_id) );
		}
		if (!$thumbnail&&!$thumbnail1) {
			$fetch_content = get_post($row->selected_items_item_id);
			$content_to_search_through = $fetch_content->post_content;
			$first_img = ”;
			ob_start();
			ob_end_clean();
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content_to_search_through, $matches);
			$first_img = $matches[1][0];

			if(empty($first_img)) {
				$first_img = “”;
			}
			$thumbnail = $first_img;
		}

		echo "window.__cart.a_thumbnail[$n]  = \"" . $thumbnail ."\";";
		echo "";
		$n++;
	}
	echo "window.__cart.count = $n;";
	//var_dump($rows);

	
  die();
}
?>