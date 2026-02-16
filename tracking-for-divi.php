<?php
/**
 * WordPress plugin to send tracking events on successful Divi form submission.
 *
 * @package           tracking-for-divi
 * @license           GPL-2.0-or-later
 * @author            Kuba Serafinowski
 *
 * @wordpress-plugin
 * Plugin Name:       Tracking for Divi
 * Plugin URI:        https://github.com/zizzfizzix/tracking-for-divi
 * Description:       Track successful Divi contact form submissions.
 * Author:            Kuba Serafinowski
 * Author URI:        https://kuba.wtf/
 * Text Domain:       tracking-for-divi
 * Domain Path:       /languages
 * x-release-please-start-version
 * Version:           1.0.1
 * x-release-please-end-version
 * Requires at least: 5.3
 * Requires PHP:      7.4
 * Tested up to:      6.9
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

namespace DigitallStudio\TrackingForDivi;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use Kucrut\Vite;
use DigitallStudio\TrackingForDivi\Admin;

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
 * Initialize server-side form submission handler.
 */
function init_form_submission_handler(): void {
	$theme = wp_get_theme();

	if ( 'Divi' === $theme->name || 'Divi' === $theme->parent_theme ) {
		new FormSubmissionHandler();
	}
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\init_form_submission_handler' );

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
