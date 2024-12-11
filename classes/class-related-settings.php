<?php
class SRRS_Status_Table {

    public function __construct() {
		add_action( 'woocommerce_system_status_report', array( $this, 'add_related_settings_table' ) );
 }

	function add_related_settings_table() {
		if ( ! class_exists( 'WC_Admin_Status' ) ) {
			return;
		}
	
        // Check for common multilingual plugins by plugin names
        $multilingual_plugins = array(
            'WPML Multilingual CMS' => is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ),
            'Polylang' => is_plugin_active( 'polylang/polylang.php' ),
            'Polylang Pro' => is_plugin_active( 'polylang-pro/polylang.php' ),
            'qTranslate X' => is_plugin_active( 'qtranslate-x/qtranslate.php' ),
            'Weglot' => is_plugin_active( 'weglot/weglot.php' ),
            'TranslatePress' => is_plugin_active( 'translatepress-multilingual/index.php' ),
        );

        // Check for common multi-currency plugins by plugin names
        $multi_currency_plugins = array(
            'Aelia Currency Switcher for WooCommerce' => is_plugin_active( 'woocommerce-aelia-currencyswitcher/woocommerce-aelia-currencyswitcher.php' ),
            'Aelia Tax Display by Country for WooCommerce' => is_plugin_active( 'woocommerce-aelia-taxdisplaybycountry/woocommerce-aelia-taxdisplaybycountry.php' ),
            'WooCommerce Multilingual – run WooCommerce with WPML' => is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ),
            'WOOCS – WooCommerce Currency Switcher' => is_plugin_active( 'woocommerce-currency-switcher/woocommerce-currency-switcher.php' ),
        );

		 // Check if plugins have the latest versions
		 include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		 $plugin_updates = array();
		 $plugins = get_plugins();
		 $update_plugins = get_plugin_updates();
		 foreach ( $plugins as $plugin_file => $plugin_data ) {
			 if ( is_plugin_active( $plugin_file ) && isset( $update_plugins[ $plugin_file ] ) ) {
				 $current_version = $plugin_data['Version'];
				 $latest_version = $update_plugins[ $plugin_file ]->update->new_version;
				 if ( version_compare( $current_version, $latest_version, '<' ) ) {
					 $plugin_updates[] = $plugin_data['Name'] . ' (Current: ' . $current_version . ', Latest: ' . $latest_version . ')';
				 }
			 }
		 }

        // Get the settings values
        $countries = WC()->countries->countries;
        $specific_countries = get_option( 'woocommerce_specific_allowed_countries', array() );
        $specific_shipping_countries = get_option( 'woocommerce_specific_ship_to_countries', array() );

        $settings = array(
            'WC General Settings' => ' ',
            'Base Country' => get_option( 'woocommerce_default_country', 'N/A' ),
            'Selling Location(s)' => get_option( 'woocommerce_allowed_countries', 'N/A' ),
            'Specific Countries' => implode( ', ', array_map( function( $code ) use ( $countries ) {
                return $countries[ $code ] ?? $code;
            }, $specific_countries ) ),
            'Shipping Location(s)' => get_option( 'woocommerce_ship_to_countries', 'N/A' ),
            'Specific Shipping Countries' => implode( ', ', array_map( function( $code ) use ( $countries ) {
                return $countries[ $code ] ?? $code;
            }, $specific_shipping_countries ) ),
            'Default Customer Location' => get_option( 'woocommerce_default_customer_address', 'N/A' ),
            'Enable Taxes' => get_option( 'woocommerce_calc_taxes', 'no' ) === 'yes' ? 'Yes' : 'No',
            'WC Product Settings' => ' ',
            'Manage Stock' => get_option( 'woocommerce_manage_stock', 'no' ) === 'yes' ? 'Yes' : 'No',
            'Hold Stock (minutes)' => get_option( 'woocommerce_hold_stock_minutes', 'N/A' ),
            'WC Tax Settings' => ' ',
            'Prices Entered With Tax' => get_option( 'woocommerce_prices_include_tax', 'no' ) === 'yes' ? 'Yes' : 'No',
			'Calculate Tax Based On' => get_option( 'woocommerce_tax_based_on', 'N/A' ),
			'Shipping Tax Class' => get_option( 'woocommerce_shipping_tax_class', 'N/A' ),
			'Tax Rounding' => get_option( 'woocommerce_tax_round_at_subtotal', 'no' ) === 'yes' ? 'Yes' : 'No',
            'Display Prices in the Shop' => get_option( 'woocommerce_tax_display_shop', 'excl' ) === 'incl' ? 'Including tax' : 'Excluding tax',
            'Display Prices During Cart and Checkout' => get_option( 'woocommerce_tax_display_cart', 'excl' ) === 'incl' ? 'Including tax' : 'Excluding tax',
            'WC Shipping Settings' => ' ',
            'Hide Shipping Costs Until Address is Entered' => get_option( 'woocommerce_shipping_cost_requires_address', 'no' ) === 'yes' ? 'Yes' : 'No',
            'Force Shipping to Customer Billing Address' => get_option( 'woocommerce_ship_to_destination', 'shipping' ) === 'billing' ? 'Yes' : 'No',
            'Other Checks' => ' ',
            'Site Visibility' => get_option( 'blog_public', '1' ) === '1' ? 'Live' : 'Hidden',
			'Checkout Endpoint: Order Received' => get_option( 'woocommerce_checkout_order_received_endpoint', 'order-received' ),
            'Checkout Endpoint: Pay' => get_option( 'woocommerce_checkout_pay_endpoint', 'pay' ),
			'Multilingual Plugins' => implode( ', ', array_keys( array_filter( $multilingual_plugins ) ) ) ?: 'None',
			'Multi-Currency Plugins' => implode( ', ', array_keys( array_filter( $multi_currency_plugins ) ) ) ?: 'None',
			'Plugins to Update' => implode( ', ', $plugin_updates ) ?: 'None',
        );

        // Map the settings values to readable formats
        $settings['Selling Location(s)'] = $settings['Selling Location(s)'] === 'all' ? 'Sell to all countries' : ( $settings['Selling Location(s)'] === 'specific' ? 'Sell to specific countries only' : 'Sell to no countries' );
        $settings['Shipping Location(s)'] = $settings['Shipping Location(s)'] === 'all' ? 'Ship to all countries' : ( $settings['Shipping Location(s)'] === 'all_except' ? 'Ship to all countries you sell to' : ( $settings['Shipping Location(s)'] === 'specific' ? 'Ship to specific countries only' : ( $settings['Shipping Location(s)'] === 'disabled' ? 'Disable shipping and shipping calculations' : 'Ship to no countries' ) ) );
        $settings['Default Customer Location'] = $settings['Default Customer Location'] === 'base' ? 'Shop base address' : ( $settings['Default Customer Location'] === 'geolocation' ? 'Geolocate' : ( $settings['Default Customer Location'] === 'geolocation_ajax' ? 'Geolocate (with page caching support)' : 'No location by default' ) );

        // Adjust the shipping location based on selling location
        if ($settings['Selling Location(s)'] === 'Sell to all countries') {
            $settings['Specific Countries'] = '';
            $settings['Specific Shipping Countries'] = '';
        } elseif ($settings['Selling Location(s)'] === 'Sell to specific countries only') {
            $settings['Shipping Location(s)'] = 'Ship to specific countries only';
            $settings['Specific Shipping Countries'] = implode( ', ', array_map( function( $code ) use ( $countries ) {
                return $countries[ $code ] ?? $code;
            }, $specific_shipping_countries ) );
        } elseif ($settings['Selling Location(s)'] === 'Sell to all countries except for') {
            $settings['Specific Shipping Countries'] = implode( ', ', array_map( function( $code ) use ( $countries ) {
                return $countries[ $code ] ?? $code;
            }, $specific_countries ) );
        } elseif ($settings['Shipping Location(s)'] === 'Disable shipping and shipping calculations') {
            $settings['Specific Shipping Countries'] = 'Disabled';
        }

        // Map the "Calculate Tax Based On" setting to a readable format
        $settings['Calculate Tax Based On'] = $settings['Calculate Tax Based On'] === 'shipping' ? 'Customer shipping address' : ( $settings['Calculate Tax Based On'] === 'billing' ? 'Customer billing address' : 'Shop base address' );

        // Map the "Shipping Tax Class" setting to a readable format
        $settings['Shipping Tax Class'] = $settings['Shipping Tax Class'] === '' ? 'Standard' : $settings['Shipping Tax Class'];

        ?>
            <table class="wc_status_table widefat" autofocus>
                <thead>
                    <tr>
                        <th colspan="6" data-export-label="Krokedil Related Settings">
                            <h2>Krokedil Related Settings</h2>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $settings as $label => $value ) : ?>
                        <?php if ( in_array( $label, array( 'WC General Settings', 'WC Product Settings', 'WC Tax Settings', 'WC Shipping Settings', 'Other Checks' ) ) ) : ?>
                            <tr>
                                <td colspan="2"><strong><?php esc_html_e( $label ); ?></strong></td>
                            </tr>
                        <?php else : ?>
                            <tr>
                                <td><?php esc_html_e( $label ); ?></td>
                                <td><?php echo esc_html( $value ); ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php
	}

}