<?php
header('Content-Type: text/xml; charset=utf-8');

preg_match('|http://(.*)|', get_option('siteurl'), $m);
$shop_name = $m[1];


echo '<?xml version="1.0" encoding="utf-8" ?>'."\n".
	'<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'."\n\n".

	"<yml_catalog date=\"".date('Y-m-d H:i')."\">\n".
	"<shop>\n".
	"<name>".$shop_name."</name>\n".
	"<company>".get_option('blogname')."</company>\n".
	"<url>".get_option('siteurl')."</url>\n\n".

	"<currencies>\n".
	"\t".'<currency id="RUR" rate="1"/>'."\n".
	"</currencies>\n\n".

	"<categories>\n";

//$sql = "SELECT * FROM `group_kurs`";
$sql =
	"SELECT
		t.term_id as id,
		t.name
	FROM
		$wpdb->posts p,
		$wpdb->postmeta m,
		$wpdb->terms t,
		$wpdb->term_relationships tr,
		$wpdb->term_taxonomy tt
	WHERE
		m.post_id = p.ID AND
		m.meta_key like 'cost_%' AND 
		p.ID = tr.object_id AND
		tr.term_taxonomy_id = tt.term_taxonomy_id AND
		tt.taxonomy = 'category' AND
		tt.term_id = t.term_id AND
		p.post_type = 'post' AND
		p.post_status = 'publish'
	GROUP BY t.term_id
	";

if ( mysql_num_rows($res = mysql_query($sql)) )
{
	while ($row = mysql_fetch_assoc($res))
	{
		print "\t".'<category id="'.$row['id'].'">'.$row['name'].'</category>'."\n";
	}
}

print
	"</categories>\n\n".

	"<offers>\n";
	

//$sql = "SELECT * FROM `kursi` k, `descrip_kurs` d, `picture` p WHERE k.`id`=d.`id_kurs` AND d.`id_picture`=p.`id` GROUP BY d.`id_kurs` ORDER BY k.`sort`, k.`id_group`, d.`id`";

//MAX(cast(m.meta_value as unsigned)) as price if max price 

$sql =
	"SELECT
		p.ID,
		p.post_content as content,
		t.term_id as category,
		p.post_title as name,
		MIN(cast(m.meta_value as unsigned)) as price
	FROM
		$wpdb->posts p,
		$wpdb->postmeta m,
		$wpdb->terms t,
		$wpdb->term_relationships tr,
		$wpdb->term_taxonomy tt
	WHERE
		m.post_id = p.ID AND
		m.meta_key like '%cost_%' AND
		p.ID = tr.object_id AND
		tr.term_taxonomy_id = tt.term_taxonomy_id AND
		tt.taxonomy = 'category' AND
		tt.term_id = t.term_id AND
		p.post_type = 'post' AND
		p.post_status = 'publish'
		GROUP BY p.ID
	ORDER BY category, p.ID
	";

$res = mysql_query($sql);

while ($row = mysql_fetch_assoc($res))
{
	$sql = "SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `post_id` = '{$row['ID']}' AND `meta_key` = 'noyml'";
	if ( !mysql_num_rows($r = mysql_query($sql)) )
	{
		//$id_kurs = get_post_meta($row['ID'], 'id_kurs', true);
		//$picture = get_post_meta($row['ID'],'yml_pic',true);
                $picture = get_post_meta($row['ID'],'Thumbnail',true);
		// Делаем на случай, если помимо ссылки на картинку, мета-поле обтекает другой текст.
		//$picture = preg_match("#http://(.*)[\s\">]#U",$picture,$tmp);

		//$description = get_post_meta($row['ID'],'shorttext',true);
		$description = strip_tags ($row['content']);
		$permalink = get_permalink($row['ID']);
		echo "<offer id='{$row['ID']}' available='true'>\n";
		echo "\t<url>{$permalink}</url>\n";
		echo "\t<price>{$row['price']}</price>\n";
		echo "\t<currencyId>RUR</currencyId>\n";
		echo "\t<categoryId>{$row['category']}</categoryId>\n";
		echo "\t<picture>{$picture}</picture>\n";
		echo "\t<delivery>true</delivery>\n";
		echo "\t<name>{$row['name']}</name>\n";
		//echo "\t<vendorCode>{$id_kurs}</vendorCode>\n";
		echo "\t<description>{$description}</description>\n";
		//echo "\t<sales_notes>{$time}</sales_notes>\n";
		echo "</offer>\n";
	}
	mysql_free_result($r);
}

mysql_free_result($res);

echo "</offers>\n\n";
echo "</shop>\n";
echo "</yml_catalog>\n";



?>