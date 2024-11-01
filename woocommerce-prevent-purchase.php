<?php
/**
 * Plugin Name: WooCommerce - Prevent Purchase
 * Plugin URI: http://sumobi.com
 * Description: Prevents a product from being purchased
 * Author: Andrew Munro
 * Author URI: http://sumobi.com
 * Version: 1.0
 * Text Domain: woocommerce-prevent-purchase
 * Domain Path: languages
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

final class WooCommerce_Prevent_Purchase {

	/** Singleton *************************************************************/

	/**
	 * @var WooCommerce_Prevent_Purchase The one true WooCommerce_Prevent_Purchase
	 * @since 1.0
	 */
	private static $instance;

	public static  $plugin_dir;
	public static  $plugin_url;
	private static $version;

	/**
	 * Main WooCommerce_Prevent_Purchase Instance
	 *
	 * Insures that only one instance of WooCommerce_Prevent_Purchase exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0
	 * @static
	 * @staticvar array $instance
	 * @return The one true WooCommerce_Prevent_Purchase
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WooCommerce_Prevent_Purchase ) ) {
			self::$instance = new WooCommerce_Prevent_Purchase;

			self::$plugin_dir = plugin_dir_path( __FILE__ );
			self::$plugin_url = plugin_dir_url( __FILE__ );
			self::$version    = '1.0';

			self::$instance->load_textdomain();
			self::$instance->hooks();

		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-prevent-purchase' ), '1.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce-prevent-purchase' ), '1.0' );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
		$lang_dir = apply_filters( 'woocommerce_prevent_purchase_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale   = apply_filters( 'plugin_locale',  get_locale(), 'woocommerce-prevent-purchase' );
		$mofile   = sprintf( '%1$s-%2$s.mo', 'woocommerce-prevent-purchase', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/woocommerce-prevent-purchase/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/woocommerce-prevent-purchase/ folder
			load_textdomain( 'woocommerce-prevent-purchase', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/woocommerce-prevent-purchase/languages/ folder
			load_textdomain( 'woocommerce-prevent-purchase', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'woocommerce-prevent-purchase', false, $lang_dir );
		}
	}


	/**
	 * Setup the default hooks and actions
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function hooks() {

		add_filter( 'woocommerce_is_purchasable', array( $this, 'prevent_purchase_validation' ), 10, 2 );
		add_action( 'woocommerce_product_meta_start', array( $this, 'show_message' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'fields' ) );
		add_action( 'woocommerce_process_product_meta_simple', array( $this, 'save_meta' ), 10 );
		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'save_meta' ), 10 );
	}

	/**
	 * Determines if a product is purchasable without having to set the stock control to "out of stock"
	 */
	public function prevent_purchase_validation( $purchasable, $product ) {

		$prevent_purchase = get_post_meta( $product->id, '_wc_prevent_purchase', true );

		if ( $prevent_purchase ) {
			$purchasable = false;
		}

		return $purchasable;
	}

	/**
	 * Show message
	 *
	 * @since 1.0
	 */
	public function show_message() {

		$prevent_purchase     = get_post_meta( get_the_ID(), '_wc_prevent_purchase', true );
		$prevent_purchase_msg = get_post_meta( get_the_ID(), '_wc_prevent_purchase_msg', true );

		if ( $prevent_purchase ) {
			$message = ! empty( $prevent_purchase_msg ) ? $prevent_purchase_msg : apply_filters( 'wc_prevent_purchase_message', __( 'This product cannot be purchased.', 'woocommerce-prevent-purchase' ) );

			if ( $message ) {
				echo '<div class="woocommerce-info">' . $message . '</div>';
			}
		}

	}

	/**
	 * Add prevent purchase fields to admin screen
	 *
	 * @since 1.0
	 */
	public function fields() {
		global $thepostid;

		echo '<div class="options_group hide_if_external">';

		// checkbox
		woocommerce_wp_checkbox( 
			array( 
				'id'            => 'wc-prevent-purchase', 
				'name'          => '_wc_prevent_purchase',
				'wrapper_class' => '', 
				'label'         => __( 'Prevent Purchase', 'woocommerce-prevent-purchase' ),
				'cbvalue'       => true,
				'value'         => get_post_meta( $thepostid, '_wc_prevent_purchase', true )
			)
		);

		// input field
		woocommerce_wp_text_input( 
			array( 
				'id'            => 'wc-prevent-purchase', 
				'name'          => '_wc_prevent_purchase_msg',
				'wrapper_class' => '', 
				'label'         => __( 'Prevent Purchase Message', 'woocommerce-prevent-purchase' ),
				'value'         => get_post_meta( $thepostid, '_wc_prevent_purchase_msg', true )
			)
		);

		echo '</div>';

	}
	
	/**
	 * Save prevent purchase option for simple and variable products
	 *
	 * @since 1.0
	 */
	public function save_meta( $post_id ) {
		
		if ( isset( $_POST['_wc_prevent_purchase'] ) ) {
			update_post_meta( $post_id, '_wc_prevent_purchase', true );
		} else {
			delete_post_meta( $post_id, '_wc_prevent_purchase' );
		}

		if ( isset( $_POST['_wc_prevent_purchase_msg'] ) && ! empty ( $_POST['_wc_prevent_purchase_msg'] ) ) {
			update_post_meta( $post_id, '_wc_prevent_purchase_msg', $_POST['_wc_prevent_purchase_msg'] );
		} else {
			delete_post_meta( $post_id, '_wc_prevent_purchase_msg' );
		}

	}

}

/**
 * The main function responsible for returning the one true WooCommerce_Prevent_Purchase
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $woocommerce_prevent_purchase = woocommerce_prevent_purchase(); ?>
 *
 * @since 1.0
 * @return object The one true WooCommerce_Prevent_Purchase Instance
 */
function woocommerce_prevent_purchase() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        if ( ! class_exists( 'WooCommerce_Activation' ) ) {
            require_once 'includes/class-activation.php';
        }

        $activation = new WooCommerce_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return WooCommerce_Prevent_Purchase::instance();
    }
}
add_action( 'plugins_loaded', 'woocommerce_prevent_purchase', 100 );