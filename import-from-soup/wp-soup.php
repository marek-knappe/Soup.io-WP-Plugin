<?php
/**
 * Gets the email message from the soup.io 
 * @package import_from_soup
 */



define(MAGPIE_CACHE_ON,false);

require('rss_fetch.inc');

require(dirname(__FILE__) . '/../../../wp-load.php');

$current_options = get_option('import_from_soup_options');
$url = "http://".$current_options['soup_username'].".soup.io/rss";


if ($current_options['soup_status'] != "OK") die('Status is not OK, check your configuration !!!');
 
$rss = fetch_rss($url);

foreach ($rss->items as $k=>$v) {
if (!czy_bylo($v['guid'])) {

      $post_content = $v['description'];
      $post_title =$v['title'];
      $post_category =array($current_options['soup_category']);
      $post_status = 'publish';
      $post_data = compact('post_content','post_title','post_date','post_date_gmt','post_author','post_category', 'post_status');
      $post_data = compact('post_content','post_title','post_category');
      $post_data = add_magic_quotes($post_data);
      $post_ID =  wp_insert_post($post_data);
	if ( is_wp_error( $post_ID ) )
		echo "\n" . $post_ID->get_error_message();
	else 
	     wp_publish_post($post_ID);


dodaj($v['guid']);
}
}

function czy_plik() {
if (!file_exists(dirname(__FILE__) ."/added.txt")) {
$ourFileHandle = fopen(dirname(__FILE__) ."/added.txt", 'w') or die("can't open file");
fclose($ourFileHandle);
}
}
function czy_bylo($guid) {
czy_plik();
$a = file(dirname(__FILE__) ."/added.txt");
foreach ($a as $k=>$v) {
if ($v=="$guid"."\n")
return 1;
}
return 0;
}
function dodaj($guid) {
czy_plik();
$fp=fopen(dirname(__FILE__) ."/added.txt", "a");
fwrite($fp, $guid."\n");
fclose($fp);
return 0;
}


?>
