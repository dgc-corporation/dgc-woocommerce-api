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
 * Plugin Name: dgc WooCommerce API
 * Plugin URI: https://dgchen.com/
 * Description: Implements new data-stores and moves product data into custom tables, with a new, normalised data structure.
 * Requires PHP 5.3 or greater.
 * Version: 1.0.0-dev
 * Author: Automattic
 * Author URI: https://dgchen.com
 *
 * Text Domain: dgc-woocommerce-api
 * Domain Path: /languages/
 *
 * @package dgc WooCommerce API
 * @author Automattic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_PRODUCT_TABLES_FILE', __FILE__ );

/**
 * Admin notice for when WooCommerce not installed
 *
 * @return void
 */
function wc_custom_product_tables_need_wc() {
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error' ), esc_html( 'You need to have WooCommerce 3.5 development version or above installed to run the Custom Product Tables plugin.', 'woocommerce' ) );
}

/**
 * Bootstrap function, loads everything up.
 */
function wc_custom_product_tables_bootstrap() {
	
	if ( ! class_exists( 'WooCommerce' ) ) {
		if ( is_admin() ) {
			add_action( 'admin_notices', 'wc_custom_product_tables_need_wc' );
		}
		return;
	}

	if ( version_compare( WC_VERSION, '3.5.dev', '<' ) ) {
		WC_Admin_Notices::add_custom_notice( 'wc_custom_product_tables_need_wc', __( 'You need WooCommerce 3.5 development version or higher to run the Custom Product Tables plugin.', 'woocommerce' ) );
		return;
	}

	// Include the main bootstrap class.
	require_once dirname( __FILE__ ) . '/includes/class-wc-product-tables-bootstrap.php';
}

add_action( 'plugins_loaded', 'wc_custom_product_tables_bootstrap' );

/**
 * dgc API call
 */
add_action( 'plugins_loaded', 'dgc_API_global' );
add_action( 'user_register', 'dgc_API_create_user_shortcode', 10, 1 );
add_action( 'edit_user_profile_update', 'dgc_API_update_user_shortcode');
add_shortcode( 'dgc-api-test', 'dgc_API_test_shortcode' );

function dgc_API_test_shortcode() {
	//return dgc_API_retrieve_users_shortcode();
	//return dgc_migrate_data_shortcode();
	//return dgc_API_transfer_custodianship_shortcode();
	return dgc_API_retrieve_records_shortcode();
	//return dgc_API_delete_record_shortcode();
	//return dgc_API_update_record_shortcode();
	//return dgc_API_create_record_shortcode();
	//return dgc_API_update_user_shortcode();
	//return dgc_API_create_user_shortcode();
	//return dgc_API_make_privateKey();
	//return dgc_API_encryptedKey();
	//return dgc_API_authorization();
}

/**
 * dgc Payment
 */
$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
if(dgc_payment_is_woocommerce_active()){
	add_filter('woocommerce_payment_gateways', 'add_dgc_payment_gateway');
	function add_dgc_payment_gateway( $gateways ){
		$gateways[] = 'WC_dgc_Payment_Gateway';
		return $gateways; 
	}

	add_action('plugins_loaded', 'init_dgc_payment_gateway');
	function init_dgc_payment_gateway(){
		require dirname( __FILE__ ) . '/includes/class-wc-dgc-payment-gateway.php';
	}
}

/**
 * @return bool
 */
function dgc_payment_is_woocommerce_active()
{
	$active_plugins = (array) get_option('active_plugins', array());
	if (is_multisite()) {
		$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	}
	return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}

/**
 * dgc custom setting
 */
/*
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
/*
/**
 * dgc REST api
 */
function dgc_API_retrieve_records_shortcode() {
	global $wpdb;
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'query'		=> array(
			'price'		=> 399,
			'type'		=> 'iPhoneX',
		)
	);
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'query'		=> array(
			'product_id'=> 1560580842,
		)
	);
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'query'		=> array(),
	);
	$dgc_API_res = dgc_API_call('/retrieveRecords/', 'POST', $dgc_API_args);
	//return json_encode($dgc_API_res);
	return $dgc_API_res['body'];	
}

function dgc_API_delete_record_shortcode() {
	global $wpdb;
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'query'		=> array(
			'product_id'=> 1560580842,
		),
	);
	$dgc_API_res = dgc_API_call('/deleteRecord', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_update_record_shortcode() {
	global $wpdb;
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'query'		=> array(
			'product_id'=> 1560580842,
		),
		'data'	=> array(
			'price'		=> 1000,
			'type'		=> 'iPhoneX',
		)
	);
	$dgc_API_res = dgc_API_call('/updateRecord', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_create_record_shortcode() {
	global $wpdb;
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'data'		=> array(
			'product_id'=> time(),
			'sku'		=> 'HelloWorld in WooCommerence',
			'type'		=> 'Book',
		)
	);
	$dgc_API_res = dgc_API_call('/createRecord', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_migrate_data_shortcode() {

	WC_Product_Tables_Backwards_Compatibility::unhook();
	$products = WC_Product_Tables_Migrate_Data::get_products( 'product' );
	foreach ( $products as $product ) {
		WC_Product_Tables_Migrate_Data::migrate_product( $product);
	}

	$variations = WC_Product_Tables_Migrate_Data::get_products( 'product_variation' );
	foreach ( $variations as $product ) {
		WC_Product_Tables_Migrate_Data::migrate_product( $product);
	}

	wp_cache_flush();
	wc_delete_product_transients();
	WC_Product_Tables_Backwards_Compatibility::hook();
	return 'migrate is done!<br> $products = ' . json_encode($products) . '<br> $variations = ' . json_encode($variations);
}

function dgc_API_update_user_shortcode() {
	/**
	 * check the keys such like email for query if it is NOT existed in users then create a new user
	 */

	$dgc_API_args = array(
		'query'	=> array(
			'email'			=> get_userdata(get_current_user_id())->user_email,
		)
	);
	$dgc_API_res = dgc_API_call('/retrieveUsers', 'POST', $dgc_API_args);
	if (json_decode($dgc_API_res['body']) == []){		
		dgc_API_make_privateKey();
	}
	dgc_API_encryptedKey();
	dgc_API_authorization();
	$publicKey = get_user_meta(get_current_user_id(), "publicKey", true );
	$dgc_API_args = array(
		'data'	=> array(
			'username'		=> get_userdata(get_current_user_id())->user_login,
			'password'		=> get_userdata(get_current_user_id())->user_login,
			'publicKey'		=> get_user_meta(get_current_user_id(), "publicKey", true ),
			'name'			=> get_userdata(get_current_user_id())->user_login,
			'email'			=> get_userdata(get_current_user_id())->user_email,
			'privateKey'	=> get_user_meta(get_current_user_id(), "privateKey", true ),
			'encryptedKey'	=> get_user_meta(get_current_user_id(), "encryptedKey", true ),
			'hashedPassword'=> get_userdata(get_current_user_id())->user_pass,
		)
	);
	$dgc_API_res = dgc_API_call('/users/'.$publicKey, 'PATCH', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_retrieve_users_shortcode() {
	$dgc_API_args = array(
		'query'	=> array(
			'email'			=> get_userdata(get_current_user_id())->user_email,
		)
	);
	$dgc_API_res = dgc_API_call('/retrieveUsers', 'POST', $dgc_API_args);
	return $dgc_API_res['body'];
	return json_decode($dgc_API_res['body']);
}

function dgc_API_create_user_shortcode() {
	/**
	 * check the keys such like email for query if it is NOT existed in users then create a new user
	 */

	$dgc_API_args = array(
		'query'	=> array(
			'email'			=> get_userdata(get_current_user_id())->user_email,
		)
	);
	$dgc_API_res = dgc_API_call('/retrieveUsers', 'POST', $dgc_API_args);
	if (json_decode($dgc_API_res['body']) == []){		
		dgc_API_make_privateKey();
		dgc_API_encryptedKey();
		dgc_API_authorization();
		$dgc_API_args = array(
			'data'	=> array(
				'username'		=> get_userdata(get_current_user_id())->user_login,
				'password'		=> get_userdata(get_current_user_id())->user_login,
				'publicKey'		=> get_user_meta(get_current_user_id(), "publicKey", true ),
				'name'			=> get_userdata(get_current_user_id())->user_login,
				'email'			=> get_userdata(get_current_user_id())->user_email,
				'privateKey'	=> get_user_meta(get_current_user_id(), "privateKey", true ),
				'encryptedKey'	=> get_user_meta(get_current_user_id(), "encryptedKey", true ),
				'hashedPassword'=> get_userdata(get_current_user_id())->user_pass,
			)
		);
		$dgc_API_res = dgc_API_call('/createUser', 'POST', $dgc_API_args);
	}
	return json_encode($dgc_API_res);
}

function dgc_API_transfer_custodianship_shortcode() {
	global $wpdb;
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'query'		=> array(
			'product_id'=> 1558944044,
		),
		'publicKey'	=> '034f355bdcb7cc0af728ef3cceb9615d90684bb5b2ca5f859ab0f0b704075871aa',
	);
	$dgc_API_res = dgc_API_call('/transferCustodianship', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_make_privateKey() {
	$dgc_API_res = dgc_API_call('/makePrivateKey', 'POST');
	update_user_meta(get_current_user_id(), 'privateKey', json_decode($dgc_API_res['body'])->privateKey);
	update_user_meta(get_current_user_id(), 'publicKey', json_decode($dgc_API_res['body'])->publicKey);
	return json_encode($dgc_API_res);
}

function dgc_API_encryptedKey() {
	$dgc_API_args = array(
		'password'		=> get_userdata(get_current_user_id())->user_pass,
		'privateKey'	=> get_user_meta(get_current_user_id(), "privateKey", true ),
	);
	$dgc_API_res = dgc_API_call('/encryptKey', 'POST', $dgc_API_args);
	update_user_meta(get_current_user_id(), 'encryptedKey', json_decode($dgc_API_res['body'])->encryptedKey);
	return json_encode($dgc_API_res);
}

function dgc_API_authorization() {
	$dgc_API_args = array(
		'username'	=> get_userdata(get_current_user_id())->user_login,
		'password'	=> get_userdata(get_current_user_id())->user_pass,
	);
	$dgc_API_res = dgc_API_call('/authorization', 'POST', $dgc_API_args);
	update_user_meta(get_current_user_id(), 'authorization', json_decode($dgc_API_res['body'])->authorization);
	return json_encode($dgc_API_res);
}

function dgc_API_global() {
	global $wpdb;

	$loopArray = str_split($_SERVER['HTTP_HOST']);
	$returnArray = array();
	$loopString = '';
	$loopReturn = '';
	foreach($loopArray as $character){
		if ($character == '.') {
			array_push($returnArray, $loopString);
			$loopString = '';
		} else {
	    	$loopString .= $character;
		}
		if ($character == end($loopArray)) {
			array_push($returnArray, $loopString);
		}
	}
	$returnArray = array_reverse($returnArray, true);
	foreach($returnArray as $item){
		$loopReturn .= $item . '_';
	}
	$wpdb->prefix = $loopReturn;
}

function dgc_API_call($dgc_API_endpoint, $dgc_API_method = 'GET', $dgc_API_args = []) {

	$wp_request_headers = array(
		'Content-Type' => 'application/json',
		'authorization'=> get_user_meta(get_current_user_id(), "authorization", true ),
    );	

	$dgc_API_args['privateKey'] = get_user_meta(get_current_user_id(), "privateKey", true );
	
	//Populate the correct endpoint for the API request
	global $dgc_API_url;
	//$dgc_API_url = 'https://api' . substr($_SERVER['HTTP_HOST'], strpos($_SERVER['HTTP_HOST'], '.')) . '/v1';
	//$dgc_API_url = "https://api.scouting.tw/v1";
 
	//Make the call and store the response in $res
	return wp_remote_request(($dgc_API_url.$dgc_API_endpoint),
        array(
            'method'    => $dgc_API_method,
            'headers'   => $wp_request_headers,
            'body'   	=> json_encode($dgc_API_args),
		));
}

/**
 * Runs on activation.
 */
function wc_custom_product_tables_activate() {
	include_once dirname( __FILE__ ) . '/includes/class-wc-product-tables-install.php';
	WC_Product_Tables_Install::activate();
}

register_activation_hook( WC_PRODUCT_TABLES_FILE, 'wc_custom_product_tables_activate' );