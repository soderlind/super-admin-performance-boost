<?php
/**
 * Super Admin Performance Boost.
 *
 * @package     Super_Admin_Performance_Boost
 * @author      Per Soderlind
 * @copyright   2023 Per Soderlind
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Super Admin Performance Boost.
 * Plugin URI: https://github.com/soderlind/super-admin-performance
 * GitHub Plugin URI: https://github.com/soderlind/super-admin-performance
 * Description: Try to aviod using switch_to_blog() and restore_current_blog() when possible.
 * Version:     1.0.1
 * Author:      Per Soderlind
 * Author URI:  https://soderlind.no
 * Network:     true
 * Text Domain: super-admin-performance
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

declare( strict_types = 1 );

if ( ! defined( 'ABSPATH' ) ) {
	wp_die();
}

if ( ! class_exists( 'Super_Admin_Sites_List_Table' ) ) {
	require_once __DIR__ . '/class-super-admin-sites-list-table.php';
}

if ( ! class_exists( 'Super_Admin_Performance_Boost' ) ) {
	require_once __DIR__ . '/class-super-admin-performance-boost.php';
}
new Super_Admin_Performance_Boost();
