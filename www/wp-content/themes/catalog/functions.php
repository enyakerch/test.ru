<?php

add_theme_support('post-thumbnails');
set_post_thumbnail_size(242,200,TRUE);
function short_title($char) {
	$post = get_the_title($post->ID);
	$post = mb_substr($title,0,$char);
	echo $post;
}
function short_post($id)
{
	//$id=98;
	$obj=get_post($id);
	$briefly = $obj->post_content;
	$briefly = mb_strcut($briefly,0,180);
	echo strip_tags($briefly);
}
?>