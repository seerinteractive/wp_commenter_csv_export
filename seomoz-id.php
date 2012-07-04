<?php


function commcsv_seomoz_id_page() {
	add_submenu_page('edit-comments.php', 'Exporter and SEOmoz auth. settings', 'Exporter settings', 'manage_options', 'commcsv_seomoz_id', 'commcsv_seomoz_id' );

}

add_action('admin_menu', 'commcsv_seomoz_id_page');

function commcsv_seomoz_id() {
	
	$seomoz= get_option('seomoz_id');
	if ( empty($seomoz['access_id']) ) {
		$seomoz['access_id']= $seomoz['secret_key']= '';
	}

	$exporter= get_option('commenter_exporter');
	if ( empty($exporter['separator']) ) {
		$exporter['separator']= ',';
	}


	if ( 'true'==$_GET['updated']) { ?>
		<div id="message" class="updated">
	<p><strong>Exporter settings and SEOmoz ID data saved.</strong></p>
	</div>
<?php } ?>

<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div><h2>Exporter settings and your SEOmoz ID</h2>
<form method="post" action="">
<table class="form-table">
	<tr>
		<th>Values separator in CSV</th>
		<td>
<input type="radio" name="commenter_exporter[separator]" value="," <?php echo ','==$exporter['separator']?' checked="checked"':'' ?> /> <b>,</b> (comma)
&nbsp; &nbsp; &nbsp;
<input type="radio" name="commenter_exporter[separator]" value=";"  <?php echo ';'==$exporter['separator']?' checked="checked"':'' ?> /> <b>;</b> (semicolon)<br />
<span style="font-size: smaller;">(CSV separator character depends on your OS locale)</span>
</td>
	</tr>
	<tr>
		<td colspan="2"><p>To receive mozTrust / mozRank data, your SEOmoz authorization data required:</p></td>
	</tr>

	<tr>
		<th>Your Access ID:</th>
		<td><input type="text" name="seomoz[access_id]" value="<?php echo $seomoz['access_id']; ?>" size="40" /></td>
	</tr>
	<tr>
		<th>Your Secret Key:</th>
		<td><input type="text" name="seomoz[secret_key]" value="<?php echo $seomoz['secret_key']; ?>" size="40" /></td>
	</tr>
	<tr>
		<th><input type="hidden" name="seomoz-save-id" value="1" /> </th>
		<td><input type="submit" class="button-primary" value="&nbsp; &nbsp; Save &nbsp; &nbsp;" /></td>
	</tr>
</table>

</form>

					<p><strong>No keys? No problem!</strong></p>
					<p>Sign up for SEOmoz's <strong><a href="http://apiwiki.seomoz.org/w/page/13991148/SEOmoz%20Free%20API" target="_blank">free API</a></strong>.  Trust us.  It's awesome.
					Tell them that <strong><a href="http://www.seerinteractive.com" target="_blank">Seer Interactive</a></strong> sent you!</li>
</div>
<?php
}

function commcsv_seomoz_save_id() {
	if ( 'POST'!=$_SERVER['REQUEST_METHOD'] || empty($_POST['seomoz-save-id']) ) {
		return;
	}
	
	$new_seomoz['access_id']= trim($_POST['seomoz']['access_id']);
	$new_seomoz['secret_key']= trim($_POST['seomoz']['secret_key']);

	$new_commenter_exporter['separator']= trim($_POST['commenter_exporter']['separator']);

	if ( ''!=$new_seomoz['access_id'] && ''!=$new_seomoz['secret_key'] ) {
		update_option('seomoz_id', $new_seomoz);
		update_option('commenter_exporter', $new_commenter_exporter);	
	}
	else {
		delete_option('seomoz_id');
		delete_option('commenter_exporter');
	}

	wp_redirect( admin_url(). 'edit-comments.php?page=commcsv_seomoz_id&updated=true');
	exit;

}

add_action('admin_init', 'commcsv_seomoz_save_id');

?>