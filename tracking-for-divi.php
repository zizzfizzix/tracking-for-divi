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
 * Description:     Track only successful Divi contact form submits through the dataLayer.
 * Author:          Kuba Serafinowski
 * Author URI:      https://kuba.wtf/
 * Text Domain:     tracking-for-divi
 * Domain Path:     /languages
 * Version:         0.1.1
 */

namespace DigitallStudio\TrackingForDivi;

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

use Kucrut\Vite;

/**
 * Main function to avoid global variables
 */
function add_divi_form_tracking_script(): void {
	$theme = wp_get_theme();

	if ( 'Divi' === $theme->name || 'Divi' === $theme->parent_theme ) {
		add_action(
			'wp_enqueue_scripts',
			function (): void {
				Vite\enqueue_asset(
					__DIR__ . '/js/dist',
					'js/src/main.ts',
					array(
						'handle'    => 'tracking-for-divi',
						'in-footer' => true,
					)
				);
			}
		);
	}
}

add_divi_form_tracking_script();
