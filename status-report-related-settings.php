<?php
/**
 * Plugin Name: Status Report Related Settings
 * Description: Show plugin related settings in WooCommerce status report.
 * Version: 1.0
 * Author: Louise Ernvik
 * Author URI: https://louiseernvik.com
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: status-report-related-settings
 * 
 * WC requires at least: 5.0.0
 * WC tested up to: 9.3.0
 * Requires Plugins: woocommerce
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package statusReportRelatedSettings
*/

/**
 * If this file is called directly, abort.
 */
defined( 'ABSPATH' ) or die( 'You are not allowed to access this page.' );

/**
 * Check if WooCommerce is active
*/
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // WooCommerce is not active, abort
    die( 'This plugin requires WooCommerce to be active.' );
}

/**
 * Include the class-weather-control.php file
*/
require_once plugin_dir_path( __FILE__ ) . 'classes/class-related-settings.php';

/**
 * Instantiate the WBP_Weather_Control class
*/
new SRRS_Status_Table();