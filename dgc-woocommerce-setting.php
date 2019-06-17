<?php
/**
 * Plugin Name: dgc WooCommerce API
 * Plugin URI: https://dgchen.com/
 * Description: Implements new data-stores and moves product data into custom tables, with a new, normalised data structure.
 * Requires PHP 5.3 or greater.
 * Version: 1.0.0-dev
 * Author: Automattic
 * Author URI: https://dgchen.com
 *
 * Text Domain: dgc-woocommerce-API
 * Domain Path: /languages/
 *
 * @package dgc WooCommerce API
 * @author Automattic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function myplugin_options_page() {
?>
  <div>
  <?php screen_icon(); ?>
  <h2>My Plugin Page Title</h2>
  <form method="post" action="options.php">
  <?php settings_fields( 'myplugin_options_group' ); ?>
  <h3>This is my option</h3>
  <p>Some text here.</p>
  <table>
  <tr valign="top">
  <th scope="row"><label for="myplugin_option_name">Label</label></th>
  <td><input type="text" id="myplugin_option_name" name="myplugin_option_name" value="<?php echo get_option('myplugin_option_name'); ?>" /></td>
  </tr>
  </table>
  <?php  submit_button(); ?>
  </form>
  </div>
<?php
} 
?>