<?php
/**
 * Plugin Name: WPCD Sample Rest API Extension
 * Plugin URI: https://wpclouddeploy.com
 * Description: A sample add-on that illustrates how to extend the core WPCD Rest API
 * Version: 1.0.0
 * Author: WPCloudDeploy
 * Author URI: https://wpclouddeploy.com
 */

require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Bootstrap class for this sample plugin.
 */
class WPCD_Sample_Rest_Api_AddOn {

	/**
	 *  Constructor function of course.
	 */
	public function __construct() {
		$plugin_data = get_plugin_data( __FILE__ );

		if ( ! defined( 'WPCDSAMPLE_API_EXT_URL' ) ) {
			define( 'WPCDSAMPLE_API_EXT_URL', plugin_dir_url( __FILE__ ) );
			define( 'WPCDSAMPLE_API_EXT_PATH', plugin_dir_path( __FILE__ ) );
			define( 'WPCDSAMPLE_API_EXT_PLUGIN', plugin_basename( __FILE__ ) );
			define( 'WPCDSAMPLE_API_EXT_EXTENSION', $plugin_data['Name'] );
			define( 'WPCDSAMPLE_API_EXT_VERSION', $plugin_data['Version'] );
			define( 'WPCDSAMPLE_API_EXT_TEXTDOMAIN', 'wpcd' );
			define( 'WPCDAMPLE_REQUIRES', '4.11.0' );
		}

		/* Run things after WordPress is loaded */
		add_action( 'init', array( $this, 'required_files' ), -20 );

		/* Make sure we include our new REST API controllers */
		add_action( 'wpcd_wpapp_include_rest_api', array( $this, 'required_rest_api_files' ), -20 );

		/* Add to the array list of controllers that need to be instantiated */
		add_filter( "wpcd_app_wordpress-app_rest_api_controller_list", array( $this, 'rest_api_controllers' ), -20, 1 );

	}

	/**
	 * Include additional files as needed
	 *
	 * Action Hook: init
	 */
	public function required_files() {

	}

	/**
	 * Include additional rest api controller files as needed
	 *
	 * Action Hook: wpcd_wpapp_include_rest_api
	 */
	public function required_rest_api_files() {
		include_once WPCDSAMPLE_API_EXT_PATH . '/includes/class-wpcd-rest-api-controller-ssh-logs.php';
	}

	/**
	 * Include additional rest api controller classes to be instantiated.
	 *
	 * @param array $controllers An array of existing controllers.
	 *
	 * Filter Hook: wpcd_app_{$this->get_app_name()}_rest_api_controller_list | wpcd_app_wordpress-app_rest_api_controller_list
	 */
	public function rest_api_controllers( $controllers ) {
		$controllers[] = WPCD_REST_API_Controller_Ssh_Logs::class;
		return $controllers;
	}

	/**
	 * Placeholder activation function.
	 *
	 * @TODO: You can hook into this function with a WP filter
	 * if you need to do things when the plugin is activated.
	 * Right now nothing in this gets executed.
	 */
	public function activation_hook() {
		// first install.
		$version = get_option( 'WPCDSAMPLE_API_EXT_version' );
		if ( ! $version ) {
			update_option( 'WPCDSAMPLE_API_EXT_last_version_upgrade', WPCDSAMPLE_API_EXT_VERSION );
		}

		if ( WPCDSAMPLE_API_EXT_VERSION !== $version ) {
			update_option( 'wpcd_version', WPCDSAMPLE_API_EXT_VERSION );
		}

		// Some setup options here?
	}
}

/**
 * Bootstrap the class
 */
if ( class_exists( 'WPCD_Init' ) ) {
	$wpcd_api_sample = new WPCD_Sample_Rest_Api_AddOn();
}
