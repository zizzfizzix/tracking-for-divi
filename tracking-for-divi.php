<?php
/**
 * WordPress plugin to send tracking events on successful Divi form submission.
 *
 * @package         tracking-for-divi
 * @license         Apache-2.0
 * @author          Kuba Serafinowski
 *
 * Plugin Name:     Tracking for Divi
 * Plugin URI:      https://github.com/zizzfizzix/tracking-for-divi
 * Description:     Track successful Divi contact form submissions.
 * Author:          Kuba Serafinowski
 * Author URI:      https://kuba.wtf/
 * Text Domain:     tracking-for-divi
 * Domain Path:     /languages
 * Version:         0.1.1
 */

namespace DigitallStudio\TrackingForDivi;

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use Kucrut\Vite;
use DigitallStudio\TrackingForDivi\Admin;

/**
 * Load translations
 */
function load_textdomain() {
	load_plugin_textdomain( 'tracking-for-divi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', __NAMESPACE__ . '\load_textdomain' );

/**
 * Main function to avoid global variables
 */
function add_front_script(): void {
	$theme = wp_get_theme();

	if ( 'Divi' === $theme->name || 'Divi' === $theme->parent_theme ) {
		add_action(
			'wp_enqueue_scripts',
			function (): void {
				$handle = 'tracking-for-divi';
				Vite\enqueue_asset(
					__DIR__ . '/js/dist',
					'js/client/main.ts',
					array(
						'handle'       => $handle,
						'dependencies' => array( 'jquery' ),
						'in-footer'    => true,
					)
				);

				// Need to assign to window as scripts of type 'module' don't do that automatically (https://stackoverflow.com/a/67415745/7736371) and Vite\enqueue_asset() sets that type for the handle used here.
				wp_add_inline_script( $handle, 'window.TRACKING_FOR_DIVI_OPTIONS = ' . wp_json_encode( get_option( 'tracking_for_divi_options' ) ), 'before' );
			}
		);
	}
}

add_front_script();

/**
 * Wrap settings bootstrap into a function to avoid global var scope.
 */
function bootstrap_settings() {
	define( __NAMESPACE__ . '\PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	$tracking_for_divi_settings_page = new Admin\SettingsPage();
	add_action( 'admin_enqueue_scripts', array( $tracking_for_divi_settings_page, 'enqueue_admin_script' ) );
	add_filter( 'plugin_action_links_' . PLUGIN_BASENAME, array( $tracking_for_divi_settings_page, 'add_settings_link' ) );
}

if ( is_admin() ) {
	bootstrap_settings();
}
