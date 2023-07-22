// Inspired by https://gist.github.com/Maximoo/e42c0ac114d12873ab511b7a097e669e
import { findKeyInObject, findFormId } from "./lib";

// TODO: get this from plugin settings with a default
const dataLayerVar = "dataLayer";

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
    window[dataLayerVar] = window[dataLayerVar] || [];

    window[dataLayerVar].push({
      event: "contact_form_submit",
      formId: findFormId(findKeyInObject(reqData, "et_pb_contactform")),
      formData: {
        name: reqData[findKeyInObject(reqData, "et_pb_contact_name")],
        email: reqData[findKeyInObject(reqData, "et_pb_contact_email")],
        message: reqData[findKeyInObject(reqData, "et_pb_contact_message")],
      },
    });
  }
});
