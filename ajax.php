<?php
include '../../../wp-load.php';

if ( !current_user_can('manage_options') ) {
	die('commcsv -- No access for current WP user.');
}

if ( isset($_GET['delete_all_seomoz_data']) ) {
	$wpdb->query("DELETE FROM $wpdb->commentmeta WHERE meta_key='seomoz'");
	wp_redirect( admin_url('edit-comments.php?page=commcsv_get_mozrank') );
	exit;
}

$seomoz_id= get_option('seomoz_id');
if ( empty($seomoz_id['access_id']) ) {
	die('commcsv -- empty SEOmoz ID.');
}

$url= trim($_POST['url']);

if ( !$url )
	die('commcsv -- no url given.');

$url_copy= $url;
$url_no_end_slash= preg_replace('/\/$/', '', $url);
$url= preg_replace('/https?:\/\//', '', $url);
$url= urlencode($url);

$curl= new WP_Http_Curl();

$curl_args= array( 'timeout' => 5, 'headers' => array('Authorization' => 'Basic '.base64_encode("$seomoz_id[access_id]:$seomoz_id[secret_key]") ) );
$answer= $curl->request( "http://lsapi.seomoz.com/linkscape/url-metrics/$url?Cols=103079215104", $curl_args );

if ( empty($answer['body'] ) )
	die('empty SEOmoz body');

if ( 'unauthorized'==$answer['body'] )
	die('unauthorized to SEOmoz');

$comment_ids= array();

$values= '';

$q= "SELECT comment_ID FROM $wpdb->comments WHERE comment_author_url LIKE '".mysql_real_escape_string($url_no_end_slash)."%'" ;
$comments_with_given_url= $wpdb->get_results($q);

$res= (array)json_decode($answer['body']);

if ( is_array($res) && count($res) ) {
	foreach ( $res as $key=>$val ) {
		if ( is_numeric($val) ) {
			$arr[$key]= trim(sprintf("%6.4g", $val));
		}
	}
	unset($res);
	$res= json_encode($arr);

} else {
	$res= $answer['body'];
}

foreach ($comments_with_given_url as $c ) {
	$comment_ids[]= $c->comment_ID;
	$values[]= "($c->comment_ID, 'seomoz', '".mysql_real_escape_string($res)."')" ;
}

if ( $comment_ids ) {
	$wpdb->query("DELETE FROM $wpdb->commentmeta WHERE meta_key='seomoz' AND comment_id IN (" .implode($comment_ids, ','). ")");
	$wpdb->query("INSERT INTO $wpdb->commentmeta (comment_id, meta_key, meta_value) VALUES " .implode($values, ',') );

}

die($res);

