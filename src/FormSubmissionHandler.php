<?php
/**
 * Handles form submission tracking via Divi's server-side hook.
 *
 * @package tracking-for-divi
 */

namespace DigitallStudio\TrackingForDivi;

/**
 * Captures Divi contact form submissions and injects tracking data into the response.
 */
class FormSubmissionHandler {
	/**
	 * Tracking data to inject into the response.
	 *
	 * @var array|null
	 */
	private static $tracking_data = null;

	/**
	 * Constructor - registers hooks.
	 */
	public function __construct() {
		// Hook into Divi's form submission.
		add_action( 'et_pb_contact_form_submit', array( $this, 'capture_submission' ), 10, 3 );

		// Start output buffer for AJAX requests to inject tracking data.
		if ( $this->is_contact_form_request() ) {
			add_action( 'init', array( $this, 'start_output_buffer' ), 1 );
		}
	}

	/**
	 * Check if the current request is a Divi contact form submission.
	 *
	 * @return bool
	 */
	private function is_contact_form_request(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Just checking for form presence, not processing.
		foreach ( $_POST as $key => $value ) {
			if ( strpos( $key, 'et_pb_contactform_submit' ) === 0 ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Start output buffering to capture and modify the response.
	 */
	public function start_output_buffer(): void {
		ob_start( array( $this, 'inject_tracking_data' ) );
	}

	/**
	 * Capture form submission data from Divi's hook.
	 *
	 * @param array $processed_fields Processed field values with labels.
	 * @param bool  $has_error        Whether the form had validation errors.
	 * @param array $form_info        Form metadata (id, number, post_id, etc).
	 */
	public function capture_submission( array $processed_fields, bool $has_error, array $form_info ): void {
		if ( $has_error ) {
			return;
		}

		self::$tracking_data = array(
			'formId'   => $form_info['contact_form_unique_id'] ?? (string) ( $form_info['contact_form_number'] ?? '0' ),
			'postId'   => $form_info['post_id'] ?? 0,
			'formData' => $this->extract_form_data( $processed_fields ),
		);
	}

	/**
	 * Extract name, email, and message from form fields.
	 *
	 * @param array $fields Processed form fields.
	 * @return array Extracted form data.
	 */
	private function extract_form_data( array $fields ): array {
		$data = array();
		foreach ( $fields as $field ) {
			$label = strtolower( $field['label'] ?? '' );
			if ( strpos( $label, 'name' ) !== false && ! isset( $data['name'] ) ) {
				$data['name'] = $field['value'];
			} elseif ( strpos( $label, 'email' ) !== false && ! isset( $data['email'] ) ) {
				$data['email'] = $field['value'];
			} elseif ( strpos( $label, 'message' ) !== false && ! isset( $data['message'] ) ) {
				$data['message'] = $field['value'];
			}
		}
		return $data;
	}

	/**
	 * Inject tracking data into the output buffer.
	 *
	 * @param string $output The buffered output.
	 * @return string Modified output with tracking data.
	 */
	public function inject_tracking_data( string $output ): string {
		if ( self::$tracking_data === null ) {
			return $output;
		}

		$tracking_element = sprintf(
			'<div id="tracking-for-divi-data" data-tracking="%s" style="display:none;"></div>',
			esc_attr( wp_json_encode( self::$tracking_data ) )
		);

		// Inject before closing body tag to ensure proper DOM placement.
		if ( strpos( $output, '</body>' ) !== false ) {
			return str_replace( '</body>', $tracking_element . '</body>', $output );
		}

		// Fallback: append to end.
		return $output . $tracking_element;
	}
}
