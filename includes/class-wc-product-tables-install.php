<?php
// Copyright (c) The dgc.network
// SPDX-License-Identifier: Apache-2.0

/**
 * Class handling table installs
 */
class WC_Product_Tables_Install {

	/**
	 * Activate function, runs on plugin activation
	 *
	 * @return void
	 */
	public static function activate() {
		
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		
		global $wpdb;
		$wpdb->prefix = get_option('prefix_field_option');
		if ( isset( $wpdb->prefix ) ) {
			$wpdb->prefix = get_option('prefix_field_option');
		} else {
			dgc_API_global();
		}
	
		//dgc_API_create_user_shortcode();
		$dgc_API_args = array(
			'data'		=> array(),
		);
/*			
			CREATE TABLE {$wpdb->prefix}wc_products (
			  `product_id` bigint(20) NOT NULL,
			  `sku` varchar(100) NULL default '',
			  `image_id` bigint(20) NULL default 0,
			  `height` double NULL default NULL,
			  `width` double NULL default NULL,
			  `length` double NULL default NULL,
			  `weight` double NULL default NULL,
			  `stock_quantity` double NULL default NULL,
			  `type` varchar(100) NULL default 'simple',
			  `virtual` tinyint(1) NULL default 0,
			  `downloadable` tinyint(1) NULL default 0,
			  `tax_class` varchar(100) NULL default '',
			  `tax_status` varchar(100) NULL default 'taxable',
			  `total_sales` double NULL default 0,
			  `price` double NULL default NULL,
			  `regular_price` double NULL default NULL,
			  `sale_price` double NULL default NULL,
			  `date_on_sale_from` datetime NULL default NULL,
			  `date_on_sale_to` datetime NULL default NULL,
			  `average_rating` float NULL default 0,
			  `stock_status` varchar(100) NULL default 'instock',
*/
		$dgc_API_arg = array(
			'name'			=> $wpdb->prefix . 'wc_products',
			'properties'	=> array(
				array(
					'name'			=> 'product_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'sku',
					'dataType'		=> 4,
				),
				array(
					'name'			=> 'image_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'default'		=> 0,
				),
				array(
					'name'			=> 'height',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'width',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'length',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'weight',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'stock_quantity',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'type',
					'dataType'		=> 4,
					'default'		=> 'simple',
				),
				array(
					'name'			=> 'virtual',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'default'		=> 0,
				),
				array(
					'name'			=> 'downloadable',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'default'		=> 0,
				),
				array(
					'name'			=> 'tax_class',
					'dataType'		=> 4,
				),
				array(
					'name'			=> 'tax_status',
					'dataType'		=> 4,
					'default'		=> 'taxable',
				),
				array(
					'name'			=> 'total_sales',
					'dataType'		=> 3,
					'numberExponent'=> 6,
					'default'		=> 0,
				),
				array(
					'name'			=> 'price',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'regular_price',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'sale_price',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'date_on_sale_from',
					'dataType'		=> 3,
					'numberExponent'=> 0,
				),
				array(
					'name'			=> 'date_on_sale_to',
					'dataType'		=> 3,
					'numberExponent'=> 0,
				),
				array(
					'name'			=> 'average_rating',
					'dataType'		=> 3,
					'numberExponent'=> 6,
				),
				array(
					'name'			=> 'stock_status',
					'dataType'		=> 4,
					'default'		=> 'instock',
				)
			)
		);
		$dgc_API_args['data'][] = $dgc_API_arg;
/*			
			CREATE TABLE {$wpdb->prefix}wc_product_attributes (
			  `product_attribute_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `product_id` bigint(20) NOT NULL,
			  `name` varchar(1000) NOT NULL,
			  `is_visible` tinyint(1) NOT NULL,
			  `is_variation` tinyint(1) NOT NULL,
			  `priority` int(11) NOT NULL default 1,
			  `attribute_id` bigint(20) NOT NULL,
*/
		$dgc_API_arg = array(
			'name'			=> $wpdb->prefix . 'wc_product_attributes',
			'properties'	=> array(
				array(
					'name'			=> 'product_attribute_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'product_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'name',
					'dataType'		=> 4,
					'required'		=> true,
				),
				array(
					'name'			=> 'is_visible',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'is_variation',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'priority',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'default'		=> 1,
				),
				array(
					'name'			=> 'attribute_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				)
			)
		);
		$dgc_API_args['data'][] = $dgc_API_arg;
/*			
			CREATE TABLE {$wpdb->prefix}wc_product_attribute_values (
			  `attribute_value_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `product_id` bigint(20) NOT NULL,
			  `product_attribute_id` bigint(20) NOT NULL,
			  `value` text NOT NULL,
			  `priority` int(11) NOT NULL default 1,
			  `is_default` tinyint(1) NOT NULL,
*/
		$dgc_API_arg = array(
			'name'			=> $wpdb->prefix . 'wc_product_attribute_values',
			'properties'	=> array(
				array(
					'name'			=> 'attribute_value_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'product_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'product_attribute_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'value',
					'dataType'		=> 4,
					'required'		=> true,
				),
				array(
					'name'			=> 'priority',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
					'default'		=> 1,
				),
				array(
					'name'			=> 'is_default',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				)
			)
		);
		$dgc_API_args['data'][] = $dgc_API_arg;
/*			
			CREATE TABLE {$wpdb->prefix}wc_product_downloads (
			  `download_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `product_id` bigint(20) NOT NULL,
			  `name` varchar(1000) NOT NULL,
			  `file` text NOT NULL,
			  `priority` int(11) default 0,
*/
		$dgc_API_arg = array(
			'name'			=> $wpdb->prefix . 'wc_product_downloads',
			'properties'	=> array(
				array(
					'name'			=> 'download_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'product_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'name',
					'dataType'		=> 4,
					'required'		=> true,
				),
				array(
					'name'			=> 'file',
					'dataType'		=> 4,
					'required'		=> true,
				),
				array(
					'name'			=> 'priority',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'default'		=> 1,
				)
			)
		);
		$dgc_API_args['data'][] = $dgc_API_arg;
/*
			CREATE TABLE {$wpdb->prefix}wc_product_relationships (
			  `relationship_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `type` varchar(100) NOT NULL,
			  `product_id` bigint(20) NOT NULL,
			  `object_id` bigint(20) NOT NULL,
			  `priority` int(11) NOT NULL,
*/
		$dgc_API_arg = array(
			'name'			=> $wpdb->prefix . 'wc_product_relationships',
			'properties'	=> array(
				array(
					'name'			=> 'relationship_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'type',
					'dataType'		=> 4,
					'required'		=> true,
				),
				array(
					'name'			=> 'product_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'object_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'priority',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				)
			)
		);
		$dgc_API_args['data'][] = $dgc_API_arg;
/*			
			CREATE TABLE {$wpdb->prefix}wc_product_variation_attribute_values (
			  `variation_attribute_value_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `product_id` bigint(20) NOT NULL,
			  `value` text NOT NULL,
			  `product_attribute_id` bigint(20) NOT NULL,
*/
		$dgc_API_arg = array(
			'name'			=> $wpdb->prefix . 'wc_product_variation_attribute_values',
			'properties'	=> array(
				array(
					'name'			=> 'variation_attribute_value_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'product_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				),
				array(
					'name'			=> 'value',
					'dataType'		=> 4,
					'required'		=> true,
				),
				array(
					'name'			=> 'product_attribute_id',
					'dataType'		=> 3,
					'numberExponent'=> 0,
					'required'		=> true,
				)
			)
		);
		$dgc_API_args['data'][] = $dgc_API_arg;
		
		$dgc_API_res = dgc_API_call('/createTables', 'POST', $dgc_API_args);
		//$dgc_API_res = dgc_migrate_data_shortcode();
		return json_encode($dgc_API_res);

/*		
		global $wpdb;


		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
			CREATE TABLE {$wpdb->prefix}wc_products (
			  `product_id` bigint(20) NOT NULL,
			  `sku` varchar(100) NULL default '',
			  `image_id` bigint(20) NULL default 0,
			  `height` double NULL default NULL,
			  `width` double NULL default NULL,
			  `length` double NULL default NULL,
			  `weight` double NULL default NULL,
			  `stock_quantity` double NULL default NULL,
			  `type` varchar(100) NULL default 'simple',
			  `virtual` tinyint(1) NULL default 0,
			  `downloadable` tinyint(1) NULL default 0,
			  `tax_class` varchar(100) NULL default '',
			  `tax_status` varchar(100) NULL default 'taxable',
			  `total_sales` double NULL default 0,
			  `price` double NULL default NULL,
			  `regular_price` double NULL default NULL,
			  `sale_price` double NULL default NULL,
			  `date_on_sale_from` datetime NULL default NULL,
			  `date_on_sale_to` datetime NULL default NULL,
			  `average_rating` float NULL default 0,
			  `stock_status` varchar(100) NULL default 'instock',
			  PRIMARY KEY  (`product_id`),
			  KEY `image_id` (`image_id`),
			  KEY `type` (`type`),
			  KEY `virtual` (`virtual`),
			  KEY `downloadable` (`downloadable`),
			  KEY `stock_status` (`stock_status`)

			) $collate;

			CREATE TABLE {$wpdb->prefix}wc_product_attributes (
			  `product_attribute_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `product_id` bigint(20) NOT NULL,
			  `name` varchar(1000) NOT NULL,
			  `is_visible` tinyint(1) NOT NULL,
			  `is_variation` tinyint(1) NOT NULL,
			  `priority` int(11) NOT NULL default 1,
			  `attribute_id` bigint(20) NOT NULL,
			  PRIMARY KEY  (`product_attribute_id`),
			  KEY `product_id` (`product_id`),
			  KEY `attribute_id` (`attribute_id`)
			) $collate;

			CREATE TABLE {$wpdb->prefix}wc_product_attribute_values (
			  `attribute_value_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `product_id` bigint(20) NOT NULL,
			  `product_attribute_id` bigint(20) NOT NULL,
			  `value` text NOT NULL,
			  `priority` int(11) NOT NULL default 1,
			  `is_default` tinyint(1) NOT NULL,
			  PRIMARY KEY  (`attribute_value_id`),
			  KEY `product_id` (`product_id`),
			  KEY `product_attribute_id` (`product_attribute_id`)
			) $collate;

			CREATE TABLE {$wpdb->prefix}wc_product_downloads (
			  `download_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `product_id` bigint(20) NOT NULL,
			  `name` varchar(1000) NOT NULL,
			  `file` text NOT NULL,
			  `priority` int(11) default 0,
			  PRIMARY KEY  (`download_id`),
			  KEY `product_id` (`product_id`)
			) $collate;

			CREATE TABLE {$wpdb->prefix}wc_product_relationships (
			  `relationship_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `type` varchar(100) NOT NULL,
			  `product_id` bigint(20) NOT NULL,
			  `object_id` bigint(20) NOT NULL,
			  `priority` int(11) NOT NULL,
			  PRIMARY KEY  (`relationship_id`),
			  KEY `type` (`type`),
			  KEY `product_id` (`product_id`),
			  KEY `object_id` (`object_id`)
			) $collate;

			CREATE TABLE {$wpdb->prefix}wc_product_variation_attribute_values (
			  `variation_attribute_value_id` bigint(20) NOT NULL AUTO_INCREMENT,
			  `product_id` bigint(20) NOT NULL,
			  `value` text NOT NULL,
			  `product_attribute_id` bigint(20) NOT NULL,
			  PRIMARY KEY  (`variation_attribute_value_id`),
			  KEY `product_id` (`product_id`),
			  KEY `product_attribute_id` (`product_attribute_id`)
			) $collate;
		";

		dbDelta( $tables );
*/
	}
}