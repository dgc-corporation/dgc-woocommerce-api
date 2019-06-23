<?php
/**
 * Copyright 2019 dgc.network
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Grouped Product Data Store class.
 */
class WC_Product_Grouped_Data_Store_Custom_Table extends WC_Product_Data_Store_Custom_Table implements WC_Object_Data_Store_Interface {

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @since  3.0.0
	 * @param  WC_Product $product Product Object.
	 */
	protected function handle_updated_props( &$product ) {
		if ( in_array( 'children', $this->updated_props, true ) ) {
			$this->update_prices_from_children( $product );
		}
		parent::handle_updated_props( $product );
	}

	/**
	 * Sync grouped product prices with children.
	 *
	 * @since 3.0.0
	 * @param WC_Product $product Product Object.
	 */
	public function sync_price( &$product ) {
		$this->update_prices_from_children( $product );
	}

	/**
	 * Loop over child products and update the grouped product price to match the lowest child price.
	 *
	 * @param WC_Product $product Product object.
	 */
	protected function update_prices_from_children( &$product ) {
		global $wpdb;
/*
		$min_price = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT price
				FROM {$wpdb->prefix}wc_products as products
				LEFT JOIN {$wpdb->posts} as posts ON products.product_id = posts.ID
				WHERE posts.post_parent = %d
				order by price ASC",
				$product->get_id()
			)
		); // WPCS: db call ok, cache ok.

		$wpdb->update(
			"{$wpdb->prefix}wc_products",
			array(
				'price' => wc_format_decimal( $min_price ),
			),
			array(
				'product_id' => $product->get_id(),
			)
		); // WPCS: db call ok, cache ok.
*/		
		// dgc-API for $wpdb->get_var
		$post_id = $wpdb->get_var(
			$wpdb->prepare("SELECT ID FROM {$wpdb->posts} as posts 
				WHERE posts.post_parent = %d", $product->get_id()
			)
		);

		// dgc-API-call: /retrieveRecords
		$dgc_API_args = array(
			'table'		=> $wpdb->prefix . 'wc_products',
			'query'		=> array(
				'product_id'=> $post_id,
			)
		);
		$dgc_API_res = dgc_API_call('/retrieveRecords/', 'POST', $dgc_API_args);
		foreach(json_decode($dgc_API_res['body']) as $dgc_API_row) {
			$min_price = $dgc_API_row->properties->price;
			// dgc-API-call: /updateRecords
			$dgc_API_args = array(
				'table'	=> $wpdb->prefix . 'wc_products',
				'query'	=> array(
					'product_id'	=> $product->get_id(),
				),
				'data'	=> array(
					'price' 		=> wc_format_decimal( $min_price ),
				),
			);
			dgc_API_call('/updateRecords', 'POST', $dgc_API_args);
			// dgc-API-call
		}
		// dgc-API-call		
	}

	/**
	 * Empty method that overrides parent method and prevent the use of
	 * WC_Product_Grouped::extra_data. If we don't do this, the post meta
	 * '_children' will be used instead of product relationships from the
	 * table wp_wc_product_relationships to get grouped products children.
	 *
	 * @param WC_Product $product Product object.
	 */
	protected function read_extra_data( &$product ) {}
}
