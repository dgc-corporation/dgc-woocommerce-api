<?php
/**
 * Plugin Name: dgc WooCommerce API
 * Plugin URI: https://dgc.network/
 * Description: Implements new data-stores hook up with dgc-network blockchain and moves product data into custom tables, with a new, normalised data structure.
 * Requires PHP 5.3 or greater.
 * Version: 1.0.0-dev
 * @author: The dgc.network
 * Author URI: https://dgc.network
 * @package dgc WooCommerce API
 *
 * Text Domain: dgc-woocommerce-api
 * Domain Path: /languages/
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
add_action( 'plugins_loaded', 'dgc_API_prefix' );
add_action( 'user_register', 'dgc_API_create_user_shortcode', 10, 1 );
add_action( 'edit_user_profile_update', 'dgc_API_update_user_shortcode');

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
 * dgc REST api
 */
add_shortcode( 'dgc-api-test', 'dgc_API_test_shortcode' );

function dgc_API_test_shortcode() {
	return dgc_API_last_exchange_shortcode();
	return dgc_API_retrieve_exchanges_shortcode();
	//return dgc_API_create_participant_shortcode();
	//return dgc_API_apply_DGC_credit_shortcode();
	return dgc_API_buy_DGC_proposal_shortcode();
	//return dgc_API_sell_DGC_proposal_shortcode();
	return dgc_API_transfer_DGC_proposal_shortcode();
	//return dgc_API_transfer_custodianship_shortcode();
	//return dgc_API_retrieve_proposals_shortcode();
	//return wc_custom_product_tables_activate();
	return dgc_migrate_data_shortcode();
	return dgc_API_create_record_shortcode();
	return dgc_API_retrieve_records_shortcode();
	//return dgc_API_update_records_shortcode();
	//return dgc_API_delete_records_shortcode();
	return dgc_API_retrieve_participants_shortcode();
	//return dgc_API_update_participants_shortcode();
	//return dgc_API_mapsApiKey();
}

function dgc_API_last_exchange_shortcode() {
	$dgc_API_args = array(
		'query'	=> array(
			'currencyIsoCodes'	=> 'TWD',
		),
	);
	$dgc_API_res = dgc_API_call('/lastExchange', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
	return $dgc_API_res['body'];
}

function dgc_API_retrieve_exchanges_shortcode() {
	$dgc_API_args = array(
		'query'	=> array(
			'currencyIsoCodes'	=> 'USD',
		),
	);
	$dgc_API_res = dgc_API_call('/retrieveExchanges', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
	return $dgc_API_res['body'];
}

function dgc_API_retrieve_proposals_shortcode() {
	$dgc_API_args = array(
		'query'	=> array(
			'role'		=> 'buyDGC',
			'status'	=> 'OPEN',
		),
	);
	$dgc_API_res = dgc_API_call('/retrieveProposals', 'POST', $dgc_API_args);
	//return json_encode($dgc_API_res);
	return $dgc_API_res['body'];
}

function dgc_API_apply_DGC_credit_shortcode() {
	$dgc_API_args = array(
		'data'	=> array(
			'receivingKey'	=> '02f60be85ff7faa45106c2ffefd225deeb6bbd437485ff8b7ad1abdc8cef5d8e09', //receiving_participant_public_key
			'DGC'	=> 10000.00,
		),
	);
	$dgc_API_res = dgc_API_call('/applyDGCoinCredit', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_sell_DGC_proposal_shortcode() {
	$dgc_API_args = array(
		'data'	=> array(
			'DGC'	=> 200,
			'TWD'	=> 199.99,  //lowest price to sell
		),
	);
	$dgc_API_res = dgc_API_call('/sellDGCoinProposal', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_buy_DGC_proposal_shortcode() {
	$dgc_API_args = array(
		'data'	=> array(
			'DGC'	=> 300,
			'TWD'	=> 301,  //highest price to buy
		),
	);
	$dgc_API_res = dgc_API_call('/buyDGCoinProposal', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_transfer_DGC_proposal_shortcode() {
	$dgc_API_args = array(
		'data'	=> array(
			'receivingKey'	=> '02d73ff52fc955d6b6a3bec3453cd2c2aca179317b4bdc144c00433054d223b594', //receiving_participant_public_key
			'DGC'			=> 100,
		),
	);
	$dgc_API_res = dgc_API_call('/transferDGCoinProposal', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_answer_DGC_transfer_shortcode() {
	global $wpdb;
	$dgc_API_args = array(
		'query'	=> array(
			'proposalId'	=> 'transferDGC1562575541',
		),
		'data'	=> array(
			'response'	=> 'ACCEPT', //ACCEPT, REJECT, CANCEL,
		),
	);
	$dgc_API_res = dgc_API_call('/answerDGCoinTransfer', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_transfer_custodianship_shortcode() {
	global $wpdb;
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'query'		=> array(),
		'publicKey'	=> '034f355bdcb7cc0af728ef3cceb9615d90684bb5b2ca5f859ab0f0b704075871aa',
	);
	$dgc_API_res = dgc_API_call('/transferCustodianship', 'POST', $dgc_API_args);
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
	foreach(json_decode($dgc_API_res['body']) as $dgc_API_row) {
		if (null !== $dgc_API_row->properties) {
			//$this->total_count += 1;
		}
	}
	// dgc-API-call:end: /retrieveRecords
	return json_encode($dgc_API_res);
	//return $dgc_API_res['body'];
}

function dgc_API_update_records_shortcode() {
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
	$dgc_API_res = dgc_API_call('/updateRecords', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_API_delete_records_shortcode() {
	global $wpdb;
	$dgc_API_args = array(
		'table'		=> $wpdb->prefix . 'wc_products',
		'query'		=> array(
			'product_id'=> 1560580842,
		),
	);
	$dgc_API_res = dgc_API_call('/deleteRecords', 'POST', $dgc_API_args);
	return json_encode($dgc_API_res);
}

function dgc_migrate_data_shortcode() {
	WC_Product_Tables_Migrate_Data::migrate();
}

function dgc_API_mapsApiKey() {
	if (get_user_meta(get_current_user_id(), "mapsApiKey", true ) == null) {
	$dgc_API_args = array(
		'MAPS_API_KEY'	=> 'https://developers.google.com/maps/documentation/javascript/get-api-key',
	);
		$dgc_API_res = dgc_API_call('/mapsApiKey', 'POST');
//		update_user_meta(get_current_user_id(), 'mapsApiKey', json_decode($dgc_API_res['body'])->privateKey);
		return json_encode($dgc_API_res);
	} else {
		return 'mapsApiKey: ' . get_user_meta(get_current_user_id(), "mapsApiKey", true );
	}
}

function dgc_API_make_privateKey() {
	if (get_user_meta(get_current_user_id(), "privateKey", true ) == null) {
		$dgc_API_res = dgc_API_call('/makePrivateKey', 'POST');
		update_user_meta(get_current_user_id(), 'privateKey', json_decode($dgc_API_res['body'])->privateKey);
		update_user_meta(get_current_user_id(), 'publicKey', json_decode($dgc_API_res['body'])->publicKey);
		return json_encode($dgc_API_res);
	} else {
		return 'privateKey: ' . get_user_meta(get_current_user_id(), "privateKey", true );
	}
}
/*
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
*/
function dgc_API_prefix() {
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
	$dgc_API_url = get_option('endpoint_field_option');
	if ( isset( $dgc_API_url ) ) {
		$dgc_API_url = get_option('endpoint_field_option');
    } else {
		$dgc_API_url = "https://api.scouting.tw/v1";
	}

	return wp_remote_request(($dgc_API_url . $dgc_API_endpoint),
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