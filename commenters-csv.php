<?php
/*
Plugin Name: SEER Commenter Contact Exporter
Description: Exports commenters&#39; contacts and Page/Domain Authority (by SEOmoz) of their URLs into CSV-file.
Author: SEER Interactive
Author URI: http://www.seerinteractive.com
Version: 1.0.1
*/

function commcsv_tool() {
	add_submenu_page('edit-comments.php', 'Export Commenters data into CSV', 'Export commenters&#39; contacts', 'manage_options', 'commcsv_page', 'commcsv_page' );
}

add_action('admin_menu', 'commcsv_tool');

include 'get-seomoz.php';
include 'seomoz-id.php';

function commcsv_page() {
	$pluginDir = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$seomoz = get_option('seomoz_id');
	if (empty($seomoz['access_id'])) {
		$hasSeoMoz = false;
	} else {
		$hasSeoMoz = true;
	}
?>
<div class="wrap">
	<h2>Commenter Contact Exporter</h2>
	by <strong><a href="http://www.seerinteractive.com"  target="_blank">SEER Interactive</a></strong>
	with data provided by <strong><a href="http://www.seomoz.org"  target="_blank">SEOmoz&#39;s Linkscape</a></strong>.</p>
	<p>&nbsp;</p>
	<div id="csvexporter" class="metabox-holder">

		<?php
		if ($hasSeoMoz) { 
			?>

			<div id="instructions" class="postbox ">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span>Download your CSV</span></h3>
				<div class="inside">
					<p><strong>Sweet! You're all setup!</strong></p>
					<p>Don't forget to <strong><a href="edit-comments.php?page=commcsv_get_mozrank">update Page Authority / Domain Authority data</a></strong>!</p>
					<p><a href="edit-comments.php?page=commcsv_page&amp;act=export"><button class="button-primary">Download CSV file »</button></a></p>
				</div>
			</div>
			<p><i>(Have you checked out the <strong><a href="http://www.seerinteractive.com/blog" target="_blank">Seer Interactive's blog</a></strong>?  
			Check us out on Twitter too <strong><a href="http://www.twitter.com/seerinteractive" target="_blank">@SeerInteractive</a></strong>)</p></i>

		<?php
		} else {
		?>
			<div id="instructions" class="postbox ">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span>Download your CSV</span></h3>
				<div class="inside">
					<p><strong>Wait! You haven't entered your SEOmoz API keys.</strong></p>
					<p>You can still export your commenter's info but you won't get any mozRank and mozTrust with your download.</p>
					<p><a href="edit-comments.php?page=commcsv_page&amp;act=export"><button class="button-primary">Download CSV file »</button></a></p>
					<p><strong>No keys? No problem!</strong></p>
					<p>Sign up for SEOmoz's <strong><a href="http://apiwiki.seomoz.org/w/page/13991148/SEOmoz%20Free%20API">free API</a></strong>.  Trust us.  It's awesome.
					Tell them that <strong><a href="http://www.seerinteractive.com">Seer Interactive</a></strong> sent you!</p>
				</div>
			</div>

			<div id="keyinstructions" class="postbox ">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span>How to setup your SEOmoz API key</span></h3>
				<div class="inside">
					<p><strong>To setup SEOmoz:</strong></p>
					<ul>
						<li>1. Go to <strong><a href="http://www.seomoz.org/api">http://www.seomoz.org/api.</a></strong></li>
						<li>2. Look for your Access ID and Secret Key.	</li>
						<li><img src="<?php echo $pluginDir . "/apikeys.jpg" ?>" border="0"></li>
						<li>3. Write down your Access ID and Secret Key.</li>
						<li>4. Go to the <a href="edit-comments.php?page=commcsv_seomoz_id"><strong>Exporter settings</strong></a> page</li>
						<li>5. Enter in your SEOmoz Access ID and Secret Key.</li>
					</ol>
					<p><i>(With the Commenter Contact Exporter plugin you will always get the mozRank number but you need a pro SEOmoz account to get mozTrust.)</i></p>
				</div>
			</div>

			<?php
		}
		?>
	</div>
</div>

<?php
}


function commcsv_export() {
	global $wpdb;
	$exporter= get_option('commenter_exporter');

	if ( empty($exporter['separator']) )
		$sep= ',';
	else
		$sep= $exporter['separator'];

	if ( !current_user_can('manage_options') )
		wp_die('You are not allowed to export comments data.');

	$data= $wpdb->get_results("SELECT comment_ID, comment_author, comment_author_email, comment_author_url, comment_post_ID
		FROM $wpdb->comments WHERE comment_approved='1'");
	$seomozdata= $wpdb->get_results("SELECT comment_id, meta_value FROM $wpdb->commentmeta WHERE meta_key='seomoz'");

	foreach( $seomozdata as $v ) {
		$tmp= json_decode($v->meta_value, $assoc= true);

		if ( isset($tmp['upa'] ) )
			$upa[$v->comment_id]= $tmp['upa'];
		else
			$upa[$v->comment_id]= '';

		if ( isset($tmp['pda'] ) ) 
			$pda[$v->comment_id]= $tmp['pda'];
		else
			$pda[$v->comment_id]= '';

	}

	foreach ( $data as $res ) {
		if ( !isset($post_link[$res->comment_post_ID]) ) {
			$post_link[$res->comment_post_ID] = get_permalink($res->comment_post_ID);
		}
		
		if ( !isset($commented_by[$res->comment_author_email]) ||
			!in_array($post_link[$res->comment_post_ID], $commented_by[$res->comment_author_email] ) )
		{
			$commented_by[$res->comment_author_email][]= $post_link[$res->comment_post_ID];
		}

	}

	$email_is_in_csv= array();

	$out= "\"Name\"$sep\"Email\"$sep\"Url\"$sep\"Commented Posts\"$sep\"Page Authority\"$sep\"Domain Authority\"";
	foreach ( $data as $res ) {
		if ( !in_array($res->comment_author_email, $email_is_in_csv) ) {

			$out .= "\r\n\"$res->comment_author\"$sep\"$res->comment_author_email\"$sep\"$res->comment_author_url\"$sep\"".
				implode($commented_by[$res->comment_author_email], ' ')
				."\"$sep\"" . ( isset($upa[$res->comment_ID])?$upa[$res->comment_ID]:'' ) .
				"\"$sep\"" . ( isset($pda[$res->comment_ID])?$pda[$res->comment_ID]:'' ). "\"";
			
			$email_is_in_csv[]= $res->comment_author_email;

		}
	}

	header('Content-Type: text/x-csv; charset=utf-8');
	header("Content-Disposition: attachment;filename=commenters-".date("Ymd").".csv");
	header("Content-Transfer-Encoding: binary");

	echo $out;
	die();
	
}

if ( strpos($_SERVER['REQUEST_URI'], 'commcsv_page&act=export') ) {
	add_action('admin_init', 'commcsv_export');
}

