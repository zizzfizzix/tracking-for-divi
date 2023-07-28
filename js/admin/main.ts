jQuery("#send_datalayer_event").on("change", () => {
  if (jQuery("#send_datalayer_event").is(":checked")) {
    jQuery("#datalayer_variable_name").closest("tr").removeClass("hidden");
    jQuery("#datalayer_variable_name").prop("required", true);
    jQuery("#contact_form_submit_datalayer_event_name")
      .closest("tr")
      .removeClass("hidden");
    jQuery("#contact_form_submit_datalayer_event_name").prop("required", true);
  } else {
    jQuery("#datalayer_variable_name").closest("tr").addClass("hidden");
    jQuery("#datalayer_variable_name").prop("required", false);
    jQuery("#contact_form_submit_datalayer_event_name")
      .closest("tr")
      .addClass("hidden");
    jQuery("#contact_form_submit_datalayer_event_name").prop("required", false);
  }
});

jQuery("#send_gtag_event").on("change", () => {
  if (jQuery("#send_gtag_event").is(":checked")) {
    jQuery("#contact_form_submit_gtag_event_name")
      .closest("tr")
      .removeClass("hidden");
    jQuery("#contact_form_submit_gtag_event_name").prop("required", true);
  } else {
    jQuery("#contact_form_submit_gtag_event_name")
      .closest("tr")
      .addClass("hidden");
    jQuery("#contact_form_submit_gtag_event_name").prop("required", false);
  }
});

jQuery("#send_gads_conversion").on("change", () => {
  if (jQuery("#send_gads_conversion").is(":checked")) {
    jQuery("#gads_conversion_id").closest("tr").removeClass("hidden");
    jQuery("#gads_conversion_label").closest("tr").removeClass("hidden");
    jQuery("#gads_conversion_id").prop("required", true);
    jQuery("#gads_conversion_label").prop("required", true);
  } else {
    jQuery("#gads_conversion_id").closest("tr").addClass("hidden");
    jQuery("#gads_conversion_label").closest("tr").addClass("hidden");
    jQuery("#gads_conversion_id").prop("required", false);
    jQuery("#gads_conversion_label").prop("required", false);
  }
});
