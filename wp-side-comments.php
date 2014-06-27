<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   WP_Side_Comments
 * @author    Pierre SYLVESTRE <pierre@strategio.fr>
 * @license   GPL-2.0+
 * @link      http://www.strategio.fr
 * @copyright 2014 Strategio
 *
 * @wordpress-plugin
 * Plugin Name:       WP Side Comments
 * Plugin URI:        http://www.strategio.fr
 * Description:       WP Side Comments create a new way to display comments like in medium.com network. It's based on SideComment.js
 * Version:           1.0.0
 * Author:            Pierre SYLVESTRE
 * Author URI:        http://www.strategio.fr
 * Text Domain:       wp-side-comments
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/strategio/wp-side-comments
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-wp-side-comments.php' );

register_activation_hook( __FILE__, array( 'WP_Side_Comments', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WP_Side_Comments', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'WP_Side_Comments', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-wp-side-comments-admin.php' );
	add_action( 'plugins_loaded', array( 'WP_Side_Comments_Admin', 'get_instance' ) );

}
