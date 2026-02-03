declare const TRACKING_FOR_DIVI_OPTIONS: {
  send_datalayer_event?: "on";
  datalayer_variable_name?: string;
  contact_form_submit_datalayer_event_name?: string;
  send_gtag_event?: "on";
  contact_form_submit_gtag_event_name?: string;
  send_gads_conversion?: "on";
  gads_conversion_id?: string;
  gads_conversion_label?: string;
};

// Apply defaults
const options = {
  send_datalayer_event: TRACKING_FOR_DIVI_OPTIONS.send_datalayer_event,
  datalayer_variable_name:
    TRACKING_FOR_DIVI_OPTIONS.datalayer_variable_name || "dataLayer",
  contact_form_submit_datalayer_event_name:
    TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_datalayer_event_name ||
    "contact_form_submit",
  send_gtag_event: TRACKING_FOR_DIVI_OPTIONS.send_gtag_event,
  contact_form_submit_gtag_event_name:
    TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_gtag_event_name ||
    "contact_form_submit",
  send_gads_conversion: TRACKING_FOR_DIVI_OPTIONS.send_gads_conversion,
  gads_conversion_id: TRACKING_FOR_DIVI_OPTIONS.gads_conversion_id,
  gads_conversion_label: TRACKING_FOR_DIVI_OPTIONS.gads_conversion_label,
};

interface TrackingData {
  formId: string;
  postId: number;
  formData: { name?: string; email?: string; message?: string };
}

function fireTrackingEvents(data: TrackingData): void {
  // dataLayer event
  if (options.send_datalayer_event === "on") {
    const dataLayerName = options.datalayer_variable_name;
    const windowRef = window as unknown as Record<string, unknown[]>;
    windowRef[dataLayerName] = windowRef[dataLayerName] || [];
    windowRef[dataLayerName].push({
      event: options.contact_form_submit_datalayer_event_name,
      formId: data.formId,
      formData: data.formData,
    });
  }

  // gtag event
  if (options.send_gtag_event === "on") {
    try {
      gtag("event", options.contact_form_submit_gtag_event_name, {
        formId: data.formId,
      });
    } catch (error) {
      console.warn(
        "Tracking for Divi: gtag event failed,",
        (error as Error).message
      );
    }
  }

  // Google Ads conversion
  if (
    options.send_gads_conversion === "on" &&
    options.gads_conversion_id &&
    options.gads_conversion_label
  ) {
    try {
      gtag("event", "conversion", {
        send_to: `${options.gads_conversion_id}/${options.gads_conversion_label}`,
      });
    } catch (error) {
      console.warn(
        "Tracking for Divi: Google Ads conversion event failed,",
        (error as Error).message
      );
    }
  }
}

// Listen for AJAX responses and extract server-injected tracking data
jQuery(document).on("ajaxSuccess", (_event, _xhr, _req, data) => {
  // Check if response contains our tracking data element
  const trackingEl = jQuery(data).filter("#tracking-for-divi-data");
  if (!trackingEl.length) {
    return;
  }

  const json = trackingEl.attr("data-tracking");
  if (!json) {
    return;
  }

  try {
    const trackingData: TrackingData = JSON.parse(json);
    fireTrackingEvents(trackingData);
  } catch (error) {
    console.warn(
      "Tracking for Divi: Failed to parse tracking data,",
      (error as Error).message
    );
  }
});
