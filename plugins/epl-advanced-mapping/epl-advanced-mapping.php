<?php
/*
 * Plugin Name: EPL - Advanced Mapping
 * Plugin URL: http://easypropertylistings.com.au/extension/advanced-mapping/
 * Description: Adds advanced map shortcode and tabbed maps to listing pages. Create a beautiful map showcasing hundreds your listings with a powerful shortcode. Maps now have tabbed options showing satellite, transit, bike and comparable tab. Add the shortcode in your posts and pages and select your shortcode options much more easily.
 * Version: 2.1
 * Author: Merv Barrett
 * Author URI: http://www.realestateconnected.com.au
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EPL_Advanced_Mapping' ) ) :
	/*
	 * Main EPL_Advanced_Mapping Class
	 *
	 * @since 1.0
	 */
	final class EPL_Advanced_Mapping {
		
		/*
		 * @var EPL_Advanced_Mapping The one true EPL_Advanced_Mapping
		 * @since 1.0
		 */
		private static $instance;
	
		/*
		 * Main EPL_Advanced_Mapping Instance
		 *
		 * Insures that only one instance of EPL_Advanced_Mapping exists in memory at any one time.
		 * Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static
		 * @staticvar array $instance
		 * @uses EPL_Advanced_Mapping::includes() Include the required files
		 * @see EPL_AM()
		 * @return The one true EPL_Advanced_Mapping
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EPL_Advanced_Mapping ) ) {
				self::$instance = new EPL_Advanced_Mapping;
				self::$instance->hooks();
				if ( defined('EPL_RUNNING') ) {
					self::$instance->setup_constants();
					self::$instance->includes();
				}
			}
			return self::$instance;
		}
		
		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			// activation
			add_action( 'admin_init', array( $this, 'activation' ) );
		}
		
		/**
		 * Activation function fires when the plugin is activated.
		 * @since 1.0
		 * @access public
		 *
		 * @return void
		 */
		public function activation() {
			if ( !defined('EPL_RUNNING') ) {
				// is this plugin active?
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
			 		// unset activation notice
			 		unset( $_GET[ 'activate' ] );
			 		// display notice
			 		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
			}
		}
		
		/**
		 * Admin notices
		 *
		 * @since 1.0
		*/
		public function admin_notices() {

			if ( !defined('EPL_RUNNING') ) {
				echo '<div class="error"><p>';
				_e( 'Please activate <b>Easy Property Listings</b> to enable all functions of EPL - Advanced Mapping', 'epl-am' );
				echo '</p></div>';
			}
		}
		
		/*
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {		
			// API URL
			if ( ! defined( 'EPL_TEMPLATES' ) ) {
				define( 'EPL_TEMPLATES', 'http://easypropertylistings.com.au' );
			}
			
			// Version
			if ( ! defined( 'EPL_AM_VERSION' ) ) {
				define( 'EPL_AM_VERSION', '2.1' );
			}
			
			// Extension name on API server
			if ( ! defined( 'EPL_AM_PRODUCT_NAME' ) ) {
				define( 'EPL_AM_PRODUCT_NAME', 'Advanced Mapping' );
			}
			
			// Plugin File
			if ( ! defined( 'EPL_AM_PLUGIN_FILE' ) ) {
				define( 'EPL_AM_PLUGIN_FILE', __FILE__ );
			}
			
			// Plugin Folder URL
			if ( ! defined( 'EPL_AM_PLUGIN_URL' ) ) {
				define( 'EPL_AM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}
			
			// Plugin Images Paths
			if ( ! defined( 'EPL_AM_PLUGIN_URL_IMAGES' ) ) {
				define( 'EPL_AM_PLUGIN_URL_IMAGES', EPL_AM_PLUGIN_URL . 'images/' );
			}
			
			// Plugin Icons Images Paths
			if ( ! defined( 'EPL_AM_PLUGIN_URL_IMAGES_ICONS' ) ) {
				define( 'EPL_AM_PLUGIN_URL_IMAGES_ICONS', EPL_AM_PLUGIN_URL_IMAGES . 'icons/' );
			}
			
			// Plugin Folder Path
			if ( ! defined( 'EPL_AM_PLUGIN_PATH' ) ) {
				define( 'EPL_AM_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			}
			
			// Plugin Includes Paths
			if ( ! defined( 'EPL_AM_PLUGIN_PATH_INCLUDES' ) ) {
				define( 'EPL_AM_PLUGIN_PATH_INCLUDES', EPL_AM_PLUGIN_PATH . 'includes/' );
			}
		}
		
		/*
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {
		
			require_once EPL_AM_PLUGIN_PATH_INCLUDES . 'install.php';
			
			if ( is_admin() ) {
				$eplam_license = new EPL_License( __FILE__, EPL_AM_PRODUCT_NAME, EPL_AM_VERSION, 'Merv Barrett' );
			}
			
			include_once( EPL_AM_PLUGIN_PATH_INCLUDES . 'functions.php' );
			include_once( EPL_AM_PLUGIN_PATH_INCLUDES . 'hooks.php' );
			include_once( EPL_AM_PLUGIN_PATH_INCLUDES . 'widget-map.php' );
		}
	}
endif; // End if class_exists check

/*
 * The main function responsible for returning the one true EPL_Advanced_Mapping
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $epl = EPL_AM(); ?>
 *
 * @since 1.0
 * @return object The one true EPL_Advanced_Mapping Instance
 */
function EPL_AM() {
	return EPL_Advanced_Mapping::instance();
}
// Get EPL_AM Running
EPL_AM();
