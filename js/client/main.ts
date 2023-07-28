// Inspired by https://gist.github.com/Maximoo/e42c0ac114d12873ab511b7a097e669e
import { findKeyInObject, findFormId } from "./lib";

declare const TRACKING_FOR_DIVI_OPTIONS: {
  send_datalayer_event?: "on";
  datalayer_variable_name: string;
  contact_form_submit_datalayer_event_name: string;
  send_gtag_event?: "on";
  contact_form_submit_gtag_event_name: string;
  send_gads_conversion?: "on";
  gads_conversion_id?: string;
  gads_conversion_label?: string;
};

// Defaults
TRACKING_FOR_DIVI_OPTIONS.datalayer_variable_name =
  TRACKING_FOR_DIVI_OPTIONS.datalayer_variable_name || "dataLayer";
TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_datalayer_event_name =
  TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_datalayer_event_name ||
  "contact_form_submit";
TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_gtag_event_name =
  TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_gtag_event_name ||
  "contact_form_submit";

jQuery(document).on("ajaxSuccess", (_event, xhr, req, data) => {
  const reqData = Object.fromEntries(new URLSearchParams(req.data));

  if (
    // check if this is a divi request
    req.url === window.location.href &&
    req.type === "POST" &&
    // check if the data sent is for a contact form
    Object.keys(reqData).some((key) => key.startsWith("et_pb_contactform")) &&
    // check if the server response is valid
    xhr.status === 200 &&
    // check if the server didn't return an error message
    !jQuery(data).find(".et_pb_contact_error_text").length
  ) {
    if (TRACKING_FOR_DIVI_OPTIONS.send_datalayer_event === "on") {
      window[TRACKING_FOR_DIVI_OPTIONS.datalayer_variable_name] =
        window[TRACKING_FOR_DIVI_OPTIONS.datalayer_variable_name] || [];

      window[TRACKING_FOR_DIVI_OPTIONS.datalayer_variable_name].push({
        event:
          TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_datalayer_event_name,
        formId: findFormId(findKeyInObject(reqData, "et_pb_contactform")),
        formData: {
          name: reqData[findKeyInObject(reqData, "et_pb_contact_name")],
          email: reqData[findKeyInObject(reqData, "et_pb_contact_email")],
          message: reqData[findKeyInObject(reqData, "et_pb_contact_message")],
        },
      });
    }

    try {
      if (TRACKING_FOR_DIVI_OPTIONS.send_gtag_event === "on") {
        gtag(
          "event",
          TRACKING_FOR_DIVI_OPTIONS.contact_form_submit_gtag_event_name,
          {
            formId: findFormId(findKeyInObject(reqData, "et_pb_contactform")),
          }
        );
      }
    } catch (error) {
      console.warn("Tracking for Divi: gtag event failed,", error.message);
    }

    try {
      if (
        TRACKING_FOR_DIVI_OPTIONS.send_gads_conversion === "on" &&
        TRACKING_FOR_DIVI_OPTIONS.gads_conversion_id &&
        TRACKING_FOR_DIVI_OPTIONS.gads_conversion_label
      ) {
        gtag("event", "conversion", {
          send_to: `${TRACKING_FOR_DIVI_OPTIONS.gads_conversion_id}/${TRACKING_FOR_DIVI_OPTIONS.gads_conversion_label}`,
        });
      }
    } catch (error) {
      console.warn(
        "Tracking for Divi: Google Ads conversion event failed,",
        error.message
      );
    }
  }
});
