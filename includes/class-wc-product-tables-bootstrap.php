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

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * WC_Product_Tables_Bootstrap.
 */
class WC_Product_Tables_Bootstrap {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();
		add_filter( 'woocommerce_data_stores', array( $this, 'replace_core_data_stores' ) );
	}		

	/**
	 * Include classes
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/class-wc-product-tables-backwards-compatibility.php';
		include_once dirname( __FILE__ ) . '/class-wc-product-tables-install.php';
		include_once dirname( __FILE__ ) . '/class-wc-product-tables-migrate-data.php';
		include_once dirname( __FILE__ ) . '/class-wc-product-tables-query.php';
		include_once dirname( __FILE__ ) . '/dgc-custom-setting-page.php';
		include_once dirname( __FILE__ ) . '/compatibility/hacks.php';
		include_once dirname( __FILE__ ) . '/compatibility/class-wc-product-attribute.php';

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			include_once dirname( __FILE__ ) . '/class-wc-product-tables-cli.php';
		}

		$this->query = new WC_Product_Tables_Query();
	}

	/**
	 * Replace the core data store for products.
	 *
	 * @param array $stores List of data stores.
	 * @return array
	 */
	public function replace_core_data_stores( $stores ) {
		include_once dirname( __FILE__ ) . '/data-stores/class-wc-product-data-store-custom-table.php';
		include_once dirname( __FILE__ ) . '/data-stores/class-wc-product-grouped-data-store-custom-table.php';
		include_once dirname( __FILE__ ) . '/data-stores/class-wc-product-variable-data-store-custom-table.php';
		include_once dirname( __FILE__ ) . '/data-stores/class-wc-product-variation-data-store-custom-table.php';

		$stores['product']           = 'WC_Product_Data_Store_Custom_Table';
		$stores['product-grouped']   = 'WC_Product_Grouped_Data_Store_Custom_Table';
		$stores['product-variable']  = 'WC_Product_Variable_Data_Store_Custom_Table';
		$stores['product-variation'] = 'WC_Product_Variation_Data_Store_Custom_Table';

		return $stores;
	}
}

new WC_Product_Tables_Bootstrap();