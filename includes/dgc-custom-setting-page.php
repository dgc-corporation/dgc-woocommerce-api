<?php 
/**
 * Copyright 2019 dgc Corporation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ----------------------------------------------------------------------------
 */

/**
 * dgc custom setting
 */
function setting_page_create() {
	add_options_page('dgc_setting', 'dgc setting', 'manage_options', 'myPlugin', 'setting_option_page');
}
add_action('admin_menu', 'setting_page_create');

function setting_option_page() {
?>
<div class="wrap">
	<h1>dgc Setting Page</h1>
	<form method="post" action="options.php">
		<?php
		// display all sections for theme-options page
		do_settings_sections("theme-options");
		// display settings field on theme-option page
		settings_fields("theme-options-grp");

		submit_button();
		?>
	</form>
</div>
<?php
}

function add_theme_menu_item() {
	add_theme_page("Theme Customization", "Theme Customization", "manage_options", "theme-options", "theme_option_page", null, 99);
}
add_action("admin_menu", "add_theme_menu_item");

function dgc_theme_settings(){
	add_option('first_field_option',1); //Value for this option name.
	add_option('second_field_option', 'https://api.yourDomainName/v1'); //Value for this option name.
	add_settings_section( 'first_section','dgc-REST-api Setting','first_theme_section_description','theme-options');

	//add settings field to the “first_section”
	add_settings_field('first_field_option','dgc is the default payment','first_callback','theme-options','first_section');
	register_setting( 'theme-options-grp','first_field_option');
		
	//add settings filed with callback display_test_twitter_element.
	add_settings_field('second_field_option', 'Set dgc-REST-api endpoint', 'second_callback', 'theme-options', 'first_section');
	register_setting( 'theme-options-grp', 'second_field_option');

	//add settings filed with callback display_test_twitter_element.
	add_settings_field('third_field_option', 'Set $wpdb->prefix for using', 'third_callback', 'theme-options', 'first_section');
	register_setting( 'theme-options-grp', 'third_field_option');

}
add_action('admin_init','dgc_theme_settings');

function first_theme_section_description(){
	echo '<p>
	<b>First, you need to cofirm the endpoint of the dgc-REST-api URL.</b><br>
	The default endpoint of your dgc-REST-api URL address is <i><font color=red>https://api.yourDomainName/v1</font></i> if your domain has joined the dgc Blockchain. <br>
	If this is not the case, you could set the <i><font color=red>https://api.iotcloudengine.com/v1</font></i> instead of. <br>
	<br>
	<b>Second, you need to confirm the namespace for the $wpdb->prefix.</b><br>
	The recommended namespace is to reverse your domain name as the prefix for your further tables using. <br>
	Your further table name will look like the below <i><font color=red>com_iotcloudengine_www_yourTableName</font></i><br>
	</p>';
}

function first_callback(){
	$options = get_option( 'first_field_option' );
	echo '<input name="first_field_option" id="first_field_option" type="checkbox" value="1" class="code" ' . checked( 1, $options, false ) . ' /> Check for enabling custom help text.';
}

function second_callback(){
	//Populate the correct endpoint for the API request
	global $dgc_API_url;
	$dgc_API_url = 'https://api' . substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'], '.')) . '/v1';
	?>
	<input type="text" name="second_field_option" id="second_field_option" size=50 value="<?php echo $dgc_API_url; ?>" />
	<?php
	$dgc_API_url = $_POST['second_field_option'];
}

function third_callback(){
	//php code to take input from text field for twitter URL.
	global $wpdb;
	dgc_API_global();
	?>
	<input type="text" name="third_field_option" id="third_field_option" size=50 value="<?php echo $wpdb->prefix; ?>" />
	<?php
	$wpdb->prefix = $_POST['third_field_option'];
}
