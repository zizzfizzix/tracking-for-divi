<?php
/**
 * Plugin Name:     Divi Form Tracking
 * Plugin URI:      https://kuba.wtf/
 * Description:     Track only successful Divi contact form submits through the dataLayer.
 * Author:          Kuba Serafinowski
 * Author URI:      https://kuba.wtf/
 * Text Domain:     divi-form-tracking
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Divi_Form_Tracking
 */

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

$theme = wp_get_theme();

use Kucrut\Vite;

if ($theme->name == 'Divi' || $theme->parent_theme == 'Divi') {
  add_action( 'wp_enqueue_scripts', function (): void {
    Vite\enqueue_asset(
      __DIR__ . '/js/dist',
      'js/src/main.ts',
      [
        'handle' => 'divi-form-tracking',
        'in-footer' => true,
      ]
    );
  });
}
