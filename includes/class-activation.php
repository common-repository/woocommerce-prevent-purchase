<?php
/**
 * Activation handler
 *
 * @package     WooCommerce\ActivationHandler
 * @since       1.0.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * WooCommerce Activation Handler Class
 *
 * @since       1.0.0
 */
class WooCommerce_Activation {

    public $plugin_name, $plugin_path, $plugin_file, $has_woocommerce;

    /**
     * Setup the activation class
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        if ( isset( $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] ) ) {
            $this->plugin_name = str_replace( 'WooCommerce - ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );
        } else {
            $this->plugin_name = __( 'This plugin', 'woocommerce-prevent-purchase' );
        }

        // Is WooCommerce installed?
        foreach ( $plugins as $plugin_path => $plugin ) {
            if ( $plugin['Name'] == 'WooCommerce' ) {
                $this->has_woocommerce = true;
                break;
            }
        }
    }


    /**
     * Process plugin deactivation
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function run() {
        // Display notice
        add_action( 'admin_notices', array( $this, 'missing_woocommerce_notice' ) );
    }

    /**
     * Display notice if WooCommerce isn't installed
     *
     * @access      public
     * @since       1.0.0
     * @return      string The notice to display
     */
    public function missing_woocommerce_notice() {

        if ( $this->has_woocommerce ) {
           echo '<div class="error"><p>' .  $this->plugin_name . __( ' requires WooCommerce. Please activate it to continue.', 'woocommerce-prevent-purchase' ) . '</p></div>'; 

        } else {
            echo '<div class="error"><p>' . $this->plugin_name . __( ' requires WooCommerce. Please install it to continue.', 'woocommerce-prevent-purchase' ) . '</p></div>';
        }
    }
}
