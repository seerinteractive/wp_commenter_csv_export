<?php

function commcsv_seomoz_commenters() {
	add_submenu_page('edit-comments.php', 'Check mozRank', 'Check mozRank', 'manage_options', 'commcsv_get_mozrank', 'commcsv_get_mozrank' );

}

add_action('admin_menu', 'commcsv_seomoz_commenters');

function commcsv_get_mozrank() {
	global $wpdb;

	$seomoz_id= get_option('seomoz_id');
	if ( empty($seomoz_id['access_id']) ) {
		echo '<p>You must enter your SEOmoz access data first.</p>';
		return;
	}

	$wpdb->show_errors();
	$urls= $wpdb->get_results("SELECT comment_ID, comment_author_url FROM $wpdb->comments
		WHERE comment_author_url!='' AND comment_approved='1' GROUP BY comment_author_url");

	$seomozes_raw= $wpdb->get_results("SELECT comment_id, meta_value FROM $wpdb->commentmeta WHERE meta_key='seomoz'", ARRAY_A);
	foreach ($seomozes_raw as $row ) {
		$seomoz[ $row['comment_id'] ]= (array)json_decode($row['meta_value']);
	}

?>

<div class="wrap">
<div id="icon-tools" class="icon32"><br /></div><h2>Retrieve Page Authority / Domain Authority for commenters&#39; URLs</h2>
<p>
	<!--
	<button class="button-primary" onclick="run_getting_seomoz();">Update all the URLs now »</button> (This might take a while) <br />
	-->
	<a style="font-weight: normal" href="<?php echo WP_PLUGIN_URL.'/commenters-csv/ajax.php?delete_all_seomoz_data=1' ?>"><button class="button-primary">Reset all Page Authority / Domain Authority »</button></a>
</p>
<table id="table-seomoz"><thead><tr>
<th class="th1">Commenter&#39;s URL</th><th>Page Authority</th><th>Domain Authority</th><th class="th4">Update</th></tr></thead><tbody>

<?php
	$row_mod_2=0;
	foreach ( $urls as $url ) {
		echo '<tr class="'.(($row_mod_2++)%2 ? 'even':'odd').'" id="cid_'.($url->comment_ID).'">
		<td><a class="check_url" id="url_'.($url->comment_ID).'" href="'.($url->comment_author_url).'">' .($url->comment_author_url). '</a></td>
		<td id="upa_'.$url->comment_ID.'">'. ( $seomoz[$url->comment_ID] ?
			($seomoz[$url->comment_ID]['upa']) : '') . '</td>
		<td id="pda_'.$url->comment_ID.'">'.( $seomoz[$url->comment_ID] ?
			($seomoz[$url->comment_ID]['pda']) : '') . '</td> <td>' . '&nbsp; <a href="javascript:update_seomoz_for(\'' .($url->comment_author_url). '\','.($url->comment_ID).');">update &raquo;</a> '.'</td> </tr>';
	}

?>
</tbody>
</table>

<?php if ( !$urls ) echo '<p>There are no commenter&#39; URLs in your blog now.</p>';

?>

<p>Data provided by <a href="http://www.seomoz.org/"  target="_blank">SEOmoz's Linkscape</a>.</p>
</div>
<?php
}

function commcsv_style() {
?>
<style type="text/css" title="">
#table-seomoz thead tr {background:#eee;}
#table-seomoz td, #table-seomoz th {padding:3px 5px;}
#table-seomoz thead th {width:200px;}
#table-seomoz thead th.th1 {width:360px;}
#table-seomoz thead th.th4 {width:90px;}
#table-seomoz td.url {oveflow:hidden;}
#table-seomoz tr.odd {background:#ececff;}
#table-seomoz tr.even {background:#e0f0e0;}
</style>

<script type="text/javascript">

function wait(ms) {
	ms += new Date().getTime();
	while (new Date() < ms){}
}

function update_seomoz_for(url, cid) {

	jQuery("#upa_"+cid).html("checking...");

	jQuery.post("<?php echo plugins_url( 'commenters-csv/ajax.php' ); ?>", {"url": url},
		function (data) {
			try {
				res= eval('('+data+')');
				err=0;
			}
			catch (e) {
				err=1;
			}
			finally {
				if (1==err) {
					jQuery("#upa_"+cid).html(data);
				}
				else {
					jQuery("#upa_"+cid).html( res['upa']);
					jQuery("#pda_"+cid).html( res['pda']);
				}
			}
		}
	
	) 
}

function run_getting_seomoz() {
	var lines= jQuery("#table-seomoz tbody a.check_url");
	for (i=0; i<lines.length; i++) {
		comment_id= lines[i].getAttribute("id").substr(4);
		update_seomoz_for( lines[i].href, comment_id );			
	}

}

</script>
<?php
}

add_action ('admin_head', 'commcsv_style');

