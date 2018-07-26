<?php
/**
 * @package bibly
 * @version 0.8.6
 */
/*
Plugin Name: bib.ly - Bible reference shortener
Plugin URI: http://bib.ly/
Description: Finds Bible references, creates a popup with the Biblical text, and links users to a page where they choose their favorite website (YouVersion, BibleGateway).
Author: John Dyer
Version: 0.8.6
Author URI: http://j.hn/
License: GPLv3, MIT
*/

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'bibly_install'); 

function bibly_install() {
	add_option('bibly_linkVersion', '');
	add_option('bibly_popupVersion', '');
	add_option('bibly_enablePopups', '1');
	add_option('bibly_startNodeId', '');
}

// create custom plugin settings menu
add_action('admin_menu', 'bibly_create_menu');

function bibly_create_menu() {

	//create new top-level menu
	add_options_page('bib.ly Settings', 'bib.ly Settings', 'administrator', __FILE__, 'bibly_settings_page');

	//call register settings function
	add_action( 'admin_init', 'bibly_register_settings' );
}


function bibly_register_settings() {
	//register our settings
	
	register_setting( 'bibly_settings', 'bibly_linkVersion' );
	register_setting( 'bibly_settings', 'bibly_enablePopups' );
	register_setting( 'bibly_settings', 'bibly_popupVersion' );
	register_setting( 'bibly_settings', 'bibly_startNodeId' );
}

function bibly_settings_page() {
?>
<div class="wrap">
<h2>bib.ly Options</h2>

<p>See <a href="http://bib.ly/">bib.ly</a> for more examples.</p>

<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>


	<h3 class="title"><span>bib.ly Settings</span></h3>
		
	<table  class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="bibly_linkVersion">Linked Version</label>
			</th>
			<td >
				<select name="bibly_linkVersion" id="bibly_linkVersion">
					<option value="">None</option>
					<option value="ESV" <?php echo (get_option('bibly_linkVersion') == 'ESV') ? ' selected' : ''; ?> >ESV - English Standard Version</option>
					<option value="HCSB"<?php echo (get_option('bibly_linkVersion') == 'HCSB') ? ' selected' : ''; ?>>HCSB - Holman Christian Standard Bible</option>
					<option value="KJV" <?php echo (get_option('bibly_linkVersion') == 'KJV') ? ' selected' : ''; ?> >KJV - King James Version</option>
					<option value="NASB"<?php echo (get_option('bibly_linkVersion') == 'NASB') ? ' selected' : ''; ?>>NASB - New American Standard Version</option>
					<option value="NCV" <?php echo (get_option('bibly_linkVersion') == 'NCV') ? ' selected' : ''; ?> >NCV - New Century Version</option>
					<option value="NJKV"<?php echo (get_option('bibly_linkVersion') == 'NJKV') ? ' selected' : ''; ?>>NKJV - New King James Version</option>
					<option value="NIV" <?php echo (get_option('bibly_linkVersion') == 'NIV') ? ' selected' : ''; ?> >NIV - New International Version</option>										
					<option value="NET" <?php echo (get_option('bibly_linkVersion') == 'NET') ? ' selected' : ''; ?> >NET - New English Translation</option>
					<option value="NLT" <?php echo (get_option('bibly_linkVersion') == 'NLT') ? ' selected' : ''; ?> >NLT - New Living Translation</option>
					<option value="MSG" <?php echo (get_option('bibly_linkVersion') == 'MSG') ? ' selected' : ''; ?> >MSG - The Message</option>										
				</select>
				
				<p>The version selected when a person clicks a link. Leave blank to let the user select a version.</p>
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="bibly_enablePopups">Enable Popups</label>
			</th>
			<td >
				<input name="bibly_enablePopups" id="bibly_enablePopups_yes" type="radio" value="1" <?php echo (get_option('bibly_enablePopups') !== '0') ? ' checked' : ''; ?>><label for="bibly_enablePopups_yes">Yes</label>
				<input name="bibly_enablePopups" id="bibly_enablePopups_no" type="radio" value="0" <?php echo (get_option('bibly_enablePopups') === '0') ? ' checked' : ''; ?>><label for="bibly_enablePopups_no">No</label> 
				<br/>
				<p>Determines whether or not the Biblical text is shown in a hover box when a person mouses over a Bible link</p>
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="bibly_linkPopup">Popup Version</label>
			</th>
			<td >
				<select name="bibly_popupVersion" id="bibly_popupVersion">
					<option value="ESV"<?php echo (get_option('bibly_popupVersion') == 'ESV') ? ' selected' : ''; ?>>ESV - English Standard Version</option>
					<option value="KJV"<?php echo (get_option('bibly_popupVersion') == 'KJV') ? ' selected' : ''; ?>>KJV - King James Version</option>
					<option value="NET"<?php echo (get_option('bibly_popupVersion') == 'NET') ? ' selected' : ''; ?>>NET - New English Translation</option>					
				</select>
				<p>The version to show in the popups.</p>
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row">
				<label for="bibly_startNodeId">Start Node ID</label>
			</th>
			<td >
				<input type="text" name="bibly_startNodeId" id="bibly_startNodeId" value="<?php echo get_option('bibly_startNodeId'); ?>" />
				<p>The DOM ID of an element you want to limit bib.ly to checking. Leave blank to check the entire page (the <code>&lt;body&gt;</code> tag).</p>
			</td>
		</tr>					
	</table>

	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="bibly_startNodeId,bibly_popupVersion,bibly_linkVersion,bibly_enablePopups" />

	<p>
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

</div>

</form>
</div>
<?php
}

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'bibly_remove' );
function bibly_remove() {
	delete_option('bibly_startNodeId');
	delete_option('bibly_popupVersion');
	delete_option('bibly_linkVersion');
	delete_option('bibly_enablePopups');
}

function bibly_add_scripts(){		
	//echo '<link rel="stylesheet" href="http://code.bib.ly/bibly.min.css" type="text/css"  />';
	/*
	echo '<script src="http://code.bib.ly/bibly.min.js" type="text/javascript"></script>';
	echo 
	'<script type="text/javascript">' .
	'bibly.startNodeId = "' . get_option('bibly_startNodeId') . '";' .
	'bibly.enablePopups = ' . (get_option('bibly_enablePopups') === '0' ? 'false' : 'true' ) . ';'  .
	'bibly.popupVersion = "' . get_option('bibly_popupVersion') . '";' .
	'bibly.linkVersion = "' . get_option('bibly_linkVersion') . '";' .
	'</script>';
	*/
	
	echo 
"<script type='text/javascript'>".
"(function(){".
	/* setup features */
	"window.bibly = window.bibly || {};".
	"bibly.startNodeId = '". get_option('bibly_startNodeId') ."';".
	"bibly.enablePopups = ". (get_option('bibly_enablePopups') === '0' ? 'false' : 'true' ) .";".
	"bibly.popupVersion = '". get_option('bibly_popupVersion') ."';".
	"bibly.linkVersion = '". get_option('bibly_linkVersion') ."';".
	
	/* load script async */
	"var bjs = document.createElement('script'),".
	" bcss = document.createElement('link'),".
	" root = (document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]);".
	"bjs.type = 'text/javascript';".
	"bjs.async = true;".
	"bjs.src = 'http://code.bib.ly/bibly.min.js';".
	"root.appendChild(bjs);".
	
	/* load style async */
	
	"bcss.rel = 'stylesheet';".
	"bcss.type = 'text/css';".
	"bcss.href = 'http://code.bib.ly/bibly.min.css';".
	"root.appendChild(bcss);".	
"})();".
"</script>";
	
	
}

add_action('wp_head','bibly_add_scripts');

?>
