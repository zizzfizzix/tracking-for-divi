<?php
/**
 * WordPress plugin to send tracking events on successful Divi form submission.
 *
 * @package         diviformtracking
 * @license         Apache-2.0
 * @author          Kuba Serafinowski
 *
 * Plugin Name:     Divi Form Tracking
 * Plugin URI:      https://kuba.wtf/
 * Description:     Track only successful Divi contact form submits through the dataLayer.
 * Author:          Kuba Serafinowski
 * Author URI:      https://kuba.wtf/
 * Text Domain:     divi-form-tracking
 * Domain Path:     /languages
 * Version:         0.1.0
 */

namespace DigitallStudio\DiviFormTracking;

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use Kucrut\Vite;

/**
 * Main function to avoid global variables
 */
function add_divi_form_tracking_code(): void {
	$theme = wp_get_theme();

	if ( 'Divi' === $theme->name || 'Divi' === $theme->parent_theme ) {
		add_action(
			'wp_enqueue_scripts',
			function (): void {
				Vite\enqueue_asset(
					__DIR__ . '/js/dist',
					'js/src/main.ts',
					array(
						'handle'    => 'divi-form-tracking',
						'in-footer' => true,
					)
				);
			}
		);
	}
}

add_divi_form_tracking_code();
