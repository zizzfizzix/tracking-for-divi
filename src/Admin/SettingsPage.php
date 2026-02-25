<?php
/**
 * Crate a settings page
 *
 * @package         trackingfordivi
 */

namespace DigitallStudio\TrackingForDivi\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use DigitallStudio\TrackingForDivi;
use Kucrut\Vite;

/**
 * Tracking for Divi settings page.
 */
class SettingsPage {
	/**
	 * Tracking for Divi default settings.
	 *
	 * @var $option_defaults
	 */
	private $option_defaults = array(
		'send_datalayer_event'                     => 'on',
		'datalayer_variable_name'                  => 'dataLayer',
		'contact_form_submit_datalayer_event_name' => 'contact_form_submit',
		'include_all_form_data'                    => null,
		'send_gtag_event'                          => null,
		'contact_form_submit_gtag_event_name'      => 'contact_form_submit',
		'send_gads_conversion'                     => null,
		'gads_conversion_id'                       => '',
		'gads_conversion_label'                    => '',
	);
	/**
	 * Variable to hold options read from the database.
	 *
	 * @var $tracking_for_divi_options
	 */
	private $tracking_for_divi_options;

	/**
	 * Bootstrap the class.
	 */
	public function __construct() {
		add_option( 'tracking_for_divi_options', $this->option_defaults );
		$this->tracking_for_divi_options = get_option( 'tracking_for_divi_options' );
		add_action( 'admin_menu', array( $this, 'tracking_for_divi_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'tracking_for_divi_page_init' ) );
	}

	/**
	 * Add plugin settings page to the menu.
	 */
	public function tracking_for_divi_add_plugin_page() {
		add_options_page(
			__( 'Tracking for Divi', 'tracking-for-divi' ),
			__( 'Tracking for Divi', 'tracking-for-divi' ),
			'manage_options',
			'tracking-for-divi',
			array( $this, 'tracking_for_divi_create_admin_page' )
		);
	}

	/**
	 * Define the settings page skeleton.
	 */
	public function tracking_for_divi_create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php print( esc_html__( 'Tracking for Divi', 'tracking-for-divi' ) ); ?></h1>

			<form method="post" action="options.php" id="settingsForm">
				<?php
					settings_fields( 'tracking_for_divi_option_group' );
					do_settings_sections( 'tracking-for-divi-admin' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Put together the settings page parts.
	 */
	public function tracking_for_divi_page_init() {
		register_setting(
			'tracking_for_divi_option_group',
			'tracking_for_divi_options',
			array( $this, 'tracking_for_divi_sanitize' )
		);

		add_settings_section(
			'tracking_for_divi_setting_section',
			__( 'Form submission tracking', 'tracking-for-divi' ),
			array( $this, 'tracking_for_divi_section_info' ),
			'tracking-for-divi-admin'
		);

		add_settings_field(
			'send_datalayer_event',
			__( 'Send a dataLayer event', 'tracking-for-divi' ),
			array( $this, 'send_datalayer_event_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section'
		);

		add_settings_field(
			'datalayer_variable_name',
			__( 'dataLayer variable name', 'tracking-for-divi' ),
			array( $this, 'datalayer_variable_name_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section',
			array(
				'class' => ! isset( $this->tracking_for_divi_options['send_datalayer_event'] ) ? 'hidden' : '',
			)
		);

		add_settings_field(
			'contact_form_submit_datalayer_event_name',
			__( 'dataLayer event name', 'tracking-for-divi' ),
			array( $this, 'contact_form_submit_datalayer_event_name_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section',
			array(
				'class' => ! isset( $this->tracking_for_divi_options['send_datalayer_event'] ) ? 'hidden' : '',
			)
		);

		add_settings_field(
			'include_all_form_data',
			__( 'Include all form data', 'tracking-for-divi' ),
			array( $this, 'include_all_form_data_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section'
		);

		add_settings_field(
			'send_gtag_event',
			__( 'Send a gtag event', 'tracking-for-divi' ),
			array( $this, 'send_gtag_event_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section'
		);

		add_settings_field(
			'contact_form_submit_gtag_event_name',
			__( 'gtag event name', 'tracking-for-divi' ),
			array( $this, 'contact_form_submit_gtag_event_name_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section',
			array(
				'class' => ! isset( $this->tracking_for_divi_options['send_gtag_event'] ) ? 'hidden' : '',
			)
		);

		add_settings_field(
			'send_gads_conversion',
			__( 'Send a Google Ads conversion', 'tracking-for-divi' ),
			array( $this, 'send_gads_conversion_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section'
		);

		add_settings_field(
			'gads_conversion_id',
			__( 'Conversion ID', 'tracking-for-divi' ),
			array( $this, 'gads_conversion_id_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section',
			array(
				'class' => ! isset( $this->tracking_for_divi_options['send_gads_conversion'] ) ? 'hidden' : '',
			)
		);

		add_settings_field(
			'gads_conversion_label',
			__( 'Conversion Label', 'tracking-for-divi' ),
			array( $this, 'gads_conversion_label_callback' ),
			'tracking-for-divi-admin',
			'tracking_for_divi_setting_section',
			array(
				'class' => ! isset( $this->tracking_for_divi_options['send_gads_conversion'] ) ? 'hidden' : '',
			)
		);
	}

	/**
	 * Sanitize all input fields before using them.
	 *
	 * @param array $input Data from the user to sanitize.
	 */
	private function tracking_for_divi_sanitize( $input ) {
		$sanitary_values = array();
		if ( isset( $input['send_datalayer_event'] ) ) {
			$sanitary_values['send_datalayer_event'] = $input['send_datalayer_event'];
		}

		if ( isset( $input['datalayer_variable_name'] ) ) {
			$sanitary_values['datalayer_variable_name'] = sanitize_text_field( $input['datalayer_variable_name'] );
		}

		if ( isset( $input['contact_form_submit_datalayer_event_name'] ) ) {
			$sanitary_values['contact_form_submit_datalayer_event_name'] = sanitize_text_field( $input['contact_form_submit_datalayer_event_name'] );
		}

		if ( isset( $input['include_all_form_data'] ) ) {
			$sanitary_values['include_all_form_data'] = $input['include_all_form_data'];
		}

		if ( isset( $input['send_gtag_event'] ) ) {
			$sanitary_values['send_gtag_event'] = $input['send_gtag_event'];
		}

		if ( isset( $input['contact_form_submit_gtag_event_name'] ) ) {
			$sanitary_values['contact_form_submit_gtag_event_name'] = sanitize_text_field( $input['contact_form_submit_gtag_event_name'] );
		}

		if ( isset( $input['send_gads_conversion'] ) ) {
			$sanitary_values['send_gads_conversion'] = $input['send_gads_conversion'];
		}

		if ( isset( $input['gads_conversion_id'] ) ) {
			$sanitary_values['gads_conversion_id'] = sanitize_text_field( $input['gads_conversion_id'] );
		}

		if ( isset( $input['gads_conversion_label'] ) ) {
			$sanitary_values['gads_conversion_label'] = sanitize_text_field( $input['gads_conversion_label'] );
		}

		return $sanitary_values;
	}

	/**
	 * Print the section info.
	 */
	public function tracking_for_divi_section_info() {
		printf(
			wp_kses(
			// translators: This paragraph contains three links with anchors that are translated separately.
				__( 'On successful form submissions, you can send an event to the %1$s, directly to Google Analytics (via %2$s), or %3$s in Google Ads.', 'tracking-for-divi' ),
				array(
					'a' => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			),
			sprintf(
				'<a href="https://support.google.com/tagmanager/answer/6164391" target="_blank">%s</a>',
				// translators: This is link one.
				esc_html__(
					'dataLayer',
					'tracking-for-divi'
				)
			),
			sprintf(
				'<a href="https://support.google.com/analytics/answer/12229021" target="_blank">%s</a>',
				// translators: This is link two.
				esc_html__(
					'custom events',
					'tracking-for-divi'
				)
			),
			sprintf(
				'<a href="https://support.google.com/google-ads/answer/1722022" target="_blank">%s</a>',
				// translators: This is link three.
				esc_html__( 'track a conversion', 'tracking-for-divi' )
			)
		);
		printf( '<p>%s</p>', esc_html__( 'You can do all three at the same time but if you use the dataLayer with Google Tag Manager you probably want to orchestrate the rest there rather than send directly with the other options.', 'tracking-for-divi' ) );
		printf( '<p>%s</p>', esc_html__( 'dataLayer event is enabled by default as it has no direct impact unless picked up.', 'tracking-for-divi' ) );
	}

	/**
	 * Print the send_datalayer_event input.
	 */
	public function send_datalayer_event_callback() {
		printf(
			'<input type="checkbox" name="tracking_for_divi_options[send_datalayer_event]" id="send_datalayer_event" %s> <label for="send_datalayer_event">%s</label>',
			isset( $this->tracking_for_divi_options['send_datalayer_event'] ) && 'on' === $this->tracking_for_divi_options['send_datalayer_event'] ? 'checked' : '',
			esc_html__( 'Send an event to dataLayer for use by Google Tag Manager', 'tracking-for-divi' )
		);
	}

	/**
	 * Print the datalayer_variable_name input.
	 */
	public function datalayer_variable_name_callback() {
		printf(
			'<input class="regular-text" type="text" name="tracking_for_divi_options[datalayer_variable_name]" id="datalayer_variable_name" placeholder="dataLayer" value="%s" %s>',
			isset( $this->tracking_for_divi_options['datalayer_variable_name'] ) ? esc_attr( $this->tracking_for_divi_options['datalayer_variable_name'] ) : '',
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			( isset( $this->tracking_for_divi_options['send_datalayer_event'] ) && 'on' === $this->tracking_for_divi_options['send_datalayer_event'] ) ? 'required' : ''
		);
	}

	/**
	 * Print the contact_form_submit_datalayer_event_name input.
	 */
	public function contact_form_submit_datalayer_event_name_callback() {
		printf(
			'<input class="regular-text" type="text" name="tracking_for_divi_options[contact_form_submit_datalayer_event_name]" id="contact_form_submit_datalayer_event_name" placeholder="contact_form_submit" value="%s" %s>',
			isset( $this->tracking_for_divi_options['contact_form_submit_datalayer_event_name'] ) ? esc_attr( $this->tracking_for_divi_options['contact_form_submit_datalayer_event_name'] ) : '',
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			( isset( $this->tracking_for_divi_options['send_datalayer_event'] ) && 'on' === $this->tracking_for_divi_options['send_datalayer_event'] ) ? 'required' : ''
		);
	}

	/**
	 * Print the include_all_form_data input.
	 */
	public function include_all_form_data_callback() {
		printf(
			'<input type="checkbox" name="tracking_for_divi_options[include_all_form_data]" id="include_all_form_data" %s> <label for="include_all_form_data">%s</label>',
			( isset( $this->tracking_for_divi_options['include_all_form_data'] ) && 'on' === $this->tracking_for_divi_options['include_all_form_data'] ) ? 'checked' : '',
			esc_html__( 'Include all form fields with their full metadata in tracking events', 'tracking-for-divi' )
		);
	}

	/**
	 * Print the send_gtag_event input.
	 */
	public function send_gtag_event_callback() {
		printf(
			'<input type="checkbox" name="tracking_for_divi_options[send_gtag_event]" id="send_gtag_event" %s> <label for="send_gtag_event">%s</label>',
			( isset( $this->tracking_for_divi_options['send_gtag_event'] ) && 'on' === $this->tracking_for_divi_options['send_gtag_event'] ) ? 'checked' : '',
			esc_html__( 'Send an event directly to Google Analytics', 'tracking-for-divi' )
		);
	}

	/**
	 * Print the contact_form_submit_gtag_event_name input.
	 */
	public function contact_form_submit_gtag_event_name_callback() {
		printf(
			'<input class="regular-text" type="text" name="tracking_for_divi_options[contact_form_submit_gtag_event_name]" id="contact_form_submit_gtag_event_name" placeholder="contact_form_submit" value="%s" %s>',
			isset( $this->tracking_for_divi_options['contact_form_submit_gtag_event_name'] ) ? esc_attr( $this->tracking_for_divi_options['contact_form_submit_gtag_event_name'] ) : '',
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			( isset( $this->tracking_for_divi_options['send_gtag_event'] ) && 'on' === $this->tracking_for_divi_options['send_gtag_event'] ) ? 'required' : ''
		);
	}

	/**
	 * Print the send_gads_conversion input.
	 */
	public function send_gads_conversion_callback() {
		printf(
			'<input type="checkbox" name="tracking_for_divi_options[send_gads_conversion]" id="send_gads_conversion" %s> <label for="send_gads_conversion">%s</label>',
			( isset( $this->tracking_for_divi_options['send_gads_conversion'] ) && 'on' === $this->tracking_for_divi_options['send_gads_conversion'] ) ? 'checked' : '',
			esc_html__( 'Send a conversion event directly to Google Ads', 'tracking-for-divi' )
		);
	}

	/**
	 * Print the gads_conversion_id input.
	 */
	public function gads_conversion_id_callback() {
		printf(
			'<input class="regular-text" type="text" name="tracking_for_divi_options[gads_conversion_id]" id="gads_conversion_id" value="%s" %s>',
			isset( $this->tracking_for_divi_options['gads_conversion_id'] ) ? esc_attr( $this->tracking_for_divi_options['gads_conversion_id'] ) : '',
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			( isset( $this->tracking_for_divi_options['send_gads_conversion'] ) && 'on' === $this->tracking_for_divi_options['send_gads_conversion'] ) ? 'required' : ''
		);
	}

	/**
	 * Print the gads_conversion_label input.
	 */
	public function gads_conversion_label_callback() {
		printf(
			'<input class="regular-text" type="text" name="tracking_for_divi_options[gads_conversion_label]" id="gads_conversion_label" value="%s" %s>',
			isset( $this->tracking_for_divi_options['gads_conversion_label'] ) ? esc_attr( $this->tracking_for_divi_options['gads_conversion_label'] ) : '',
      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			( isset( $this->tracking_for_divi_options['send_gads_conversion'] ) && 'on' === $this->tracking_for_divi_options['send_gads_conversion'] ) ? 'required' : ''
		);
	}

	/**
	 * Inject scripts needed for the settings page
	 *
	 * @param string $hook The WordPress hook at which the injection should happen.
	 */
	public function enqueue_admin_script( $hook ) {
		if ( 'settings_page_tracking-for-divi' === $hook ) {
			Vite\enqueue_asset(
				\DigitallStudio\TrackingForDivi\PLUGIN_DIR . 'js/dist',
				'js/admin/main.ts',
				array(
					'handle'       => 'tracking-for-divi-admin',
					'dependencies' => array( 'jquery' ),
					'in-footer'    => true,
				)
			);
		}
	}

	/**
	 * Add a link to the settings page in the plugin list entry.
	 *
	 * @param array $links The links object from WordPress.
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf( '<a href="%s">%s</a>', esc_url( menu_page_url( 'tracking-for-divi', false ) ), esc_html__( 'Settings', 'tracking-for-divi' ) );
		array_unshift( $links, $settings_link );
		return $links;
	}
}
