<?php
/*
Plugin Name: Import from Soup
Version: 1.0.0
Plugin URI: http://www.knappe.pl/?p=241
Description: Can import post from www.soup.io RSS to your wordpress
Author: Marek Knappe
Author URI: http://www.knappe.pl
*/

/*
Change Log

1.0
  * First public release.
*/ 


function activate_import_from_soup(){
	global $post;
	$current_options = get_option('import_from_soup_options');
	$soup_username = $current_options['soup_username'];
        
}

activate_import_from_soup();


// Create the options page
function import_from_soup_options_page() { 
	global $wpdb;
	$current_options = get_option('import_from_soup_options');
	$soup_username = $current_options["soup_username"];
	$soup_status = $current_options["soup_status"];
	$soup_category = $current_options["soup_category"];

        $catsRes=$wpdb->get_results("
                                    SELECT
                                    term_id as id,name
                              
                                    FROM
                                    `" . $wpdb->terms . "`");

	if ($_POST['action']){ ?>
		<div id="message" class="updated fade"><p><strong>Options saved.</strong></p></div>
	<?php } ?>
	<div class="wrap" id="import-from-soup-options">
		<h2>Import from Soup Options</h2>

		<h3>Remember to add <?php bloginfo('wpurl');;?>/wp-content/plugins/import-from-soup/wp-soup.php to your cron job !</h3>		
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']; ?>">
			<fieldset>
				<legend>Options:</legend>
				<input type="hidden" name="action" value="save_import_from_soup_options" />
				<table width="100%" cellspacing="2" cellpadding="5" class="editform">
					<tr>
						<th valign="top" scope="row"><label for="Soup.io username">www.soup.io Username:</label></th>
						<td><input type="text" name="soup_username" value="<?php echo $soup_username;?>" /></td>
					</tr>
					<TR><th valign="top" scope="row">Auto-add to category:</th><td><select name="soup_category">
<option value=\"\">Default</option>
<?
            if($catsRes) 
            foreach ($catsRes as $v)  {
		    if ($v->id == $soup_category) {
			$selected = "selected";
		    } else $selected="";
		    print "<option value=\"$v->id\" $selected>$v->name</option>";
                }

?>

</select}


</TD>
					<TR><th valign="top" scope="row">Status:</th><TD><?php echo $soup_status;?></TD></tr>
				</table>
			</fieldset>
			<p class="submit">
				<input type="submit" name="Submit" value="Update Options &raquo;" />
			</p>
		</form>
	</div>
<?php 
}

function import_from_soup_add_options_page() {
	// Add a new menu under Options:
	add_options_page('Import from Soup', 'Import from Soup', 10, __FILE__, 'import_from_soup_options_page');
}

function import_from_soup_save_options() {
	// create array
	$import_from_soup_options["soup_username"] = $_POST["soup_username"];	
        $import_from_soup_options["soup_category"] = $_POST["soup_category"];

	update_option('import_from_soup_options', $import_from_soup_options);
	$options_saved = true;
}

function import_from_soup_check_username() {
        $current_options = get_option('import_from_soup_options');
        $soup_username = $current_options["soup_username"];
        $import_from_soup_options = $current_options;

	$test = @file_get_contents("http://".$soup_username.".soup.io/rss");
	
	if ($test) {
	    $import_from_soup_options["soup_status"]="OK";
	} else {
	    $import_from_soup_options["soup_status"]="DENIED, check your username and check if page <a href=\"http://".$soup_username.".soup.io/rss\">http://".$soup_username.".soup.io/rss</A> works";
	}
        update_option('import_from_soup_options', $import_from_soup_options);

}
add_action('admin_menu', 'import_from_soup_add_options_page');


if (!get_option('import_from_soup_options')){
	// create default options
	$import_from_soup_options["soup_username"] = '';

	update_option('import_from_soup_options', $import_from_soup_options);
}

if ($_POST['action'] == 'save_import_from_soup_options'){
	import_from_soup_save_options();
	import_from_soup_check_username();

}


?>