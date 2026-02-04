declare const TRACKING_FOR_DIVI_OPTIONS: {
  send_datalayer_event?: "on";
  datalayer_variable_name?: string;
  contact_form_submit_datalayer_event_name?: string;
  include_all_form_data?: "on";
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
  include_all_form_data: TRACKING_FOR_DIVI_OPTIONS.include_all_form_data,
  send_gtag_event: TRACKING_FOR_DIVI_OPTIONS.send_gtag_event,
  contact_form_submit_gtag_event_name:
    TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_gtag_event_name ||
    "contact_form_submit",
  send_gads_conversion: TRACKING_FOR_DIVI_OPTIONS.send_gads_conversion,
  gads_conversion_id: TRACKING_FOR_DIVI_OPTIONS.gads_conversion_id,
  gads_conversion_label: TRACKING_FOR_DIVI_OPTIONS.gads_conversion_label,
};

interface ContactFormData {
  name?: string;
  email?: string;
  message?: string;
}

interface TrackingData {
  formId: string;
  postId: number;
  formData: ContactFormData;
  allFormData?: Record<string, unknown>;
}

interface DataLayerEvent extends TrackingData {
  event: string;
  allFormData?: Record<string, unknown>;
}

interface GtagEventData extends ContactFormData {
  form_id: string;
  [key: string]: unknown;
}

function fireTrackingEvents(data: TrackingData): void {
  // dataLayer event
  if (options.send_datalayer_event === "on") {
    const dataLayerName = options.datalayer_variable_name;
    const windowRef = window as unknown as Record<string, unknown[]>;
    windowRef[dataLayerName] = windowRef[dataLayerName] || [];
    const eventData: DataLayerEvent = {
      event: options.contact_form_submit_datalayer_event_name,
      formId: data.formId,
      postId: data.postId,
      formData: data.formData,
    };
    if (options.include_all_form_data === "on" && data.allFormData) {
      eventData.allFormData = data.allFormData;
    }
    windowRef[dataLayerName].push(eventData);
  }

  // gtag event
  if (options.send_gtag_event === "on") {
    try {
      const gtagEventData: GtagEventData = {
        form_id: data.formId,
        name: data.formData.name,
        email: data.formData.email,
        message: data.formData.message,
      };
      if (options.include_all_form_data === "on" && data.allFormData) {
        Object.entries(data.allFormData).forEach(([key, field]) => {
          if (typeof field === "object" && field !== null && "value" in field) {
            gtagEventData[key] = (field as { value: unknown }).value;
          }
        });
      }
      gtag("event", options.contact_form_submit_gtag_event_name, gtagEventData);
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
