# Tracking for Divi

**Contributors:** kubawtf  
**Tags:** divi, dataLayer, tracking, tag manager  
**Requires at least:** 5.3  
**Tested up to:** 6.9.1  
**Requires PHP:** 7.4  
**Stable tag:** 0.2.0  
**License:** Apache 2.0  
**License URI:** <https://www.apache.org/licenses/LICENSE-2.0>

If you want to track your completed form submissions using the Divi theme look no further.

## Description

Ever wanted to track the built-in contact form from Divi? If you tried using Google Tag Manager's built-in form submit events to do it you probably noticed an inflated number of events flowing.

This is because the form submission event is triggered even if there are form validation errors or the server returns another error.

Since Divi doesn't provide any JavaScript events to hook into there is no straightforward way to find out if a message has actually been sent.

To get around this, this plugin listens to the network communication that is happening underneath. It's based on the same mechanism Divi itself uses.

There are three options now:

1. a [dataLayer](https://developers.google.com/tag-platform/tag-manager/datalayer) event
2. a [Google Analytics 4 event](https://support.google.com/analytics/answer/12229021) (using gtag.js)
3. a [Google Ads conversion](https://support.google.com/google-ads/answer/1722022) (also using gtag.js)

You can use either one of them or event all of them together.

You'll be able to customize the dataLayer variable if you're [not using the standard naming](https://developers.google.com/tag-platform/tag-manager/datalayer#rename_the_data_layer), and the event names, separately for dataLayer and gtag.

### Data layer reporting

The structure of the object pushed to the dataLayer is the following, with the default naming:

```typescript
{
  event: "contact_form_submit",
  formId: string,
  postId: number,
  formData: {
    name: string,
    email: string,
    message: string,
  },
  allFormData?: Record<string, unknown>, // when "Include all form data" is enabled
}
```

It's up to you to use or discard the form data.

### Google Analytics 4 reporting

The Google Analytics event is sent with flattened parameters:

```typescript
gtag(
  "event",
  "contact_form_submit",
  {
    form_id: "divi/contact-form-0",
    name: "John",
    email: "john@example.com",
    message: "Hello",
    // additional fields when "Include all form data" is enabled
  }
);
```

Form data (name, email, message) is included by default. When "Include all form data" is enabled, all form fields are flattened into the event parameters.

_This will only work if you have a [Google Tag](https://support.google.com/google-ads/answer/11994839) already deployed on the website i.e. the `gtag()` function is available._ Otherwise, you will see a warning in your console like this:

>Tracking for Divi: gtag event failed, gtag is not defined

### Google Ads conversion reporting

The Google Ads conversion is sent like so:

```typescript
gtag(
  "event",
  "conversion",
  {
    send_to: "<conversion_id>/<conversion_label>",
  }
);
```

There is no default for `conversion_id` and `conversion_label` as they are always created individually in Google Ads.

_This will only work if you have a [Google Tag](https://support.google.com/google-ads/answer/11994839) already deployed on the website i.e. the `gtag()` function is available._ Otherwise, you will see a warning in your console like this:

>Tracking for Divi: Google Ads conversion event failed, gtag is not defined

---

**Note:** This plugin hasn't been thoroughly tested with Divi forms that redirect to a success page. The tracking works just as well, however the redirect could interrupt the tracking flow e.g. if Google Tag Manager has a lot of logic and doesn't trigger the tags in time. The tags themselves these days [use the beacon API](https://support.google.com/analytics/answer/9964640#event-batching) (see [MDN](https://developer.mozilla.org/en-US/docs/Web/API/Beacon_API) for reference) so the requests shouldn't be interrupted as long as they are fired before website unload.

## Installation

All you need is to install it by uploading the zip file or using the plugin directory from within your WordPress website when the plugin is approved.

There is no configuration necessary if you only plan to use the dataLayer. Activate and you're ready to go. However, it will only work if your current theme is _Divi_ or its child theme.

## Frequently Asked Questions

### How do I track form submissions with Google Analytics?

You can use Google Tag Manager to set up a trigger on the `contact_form_submit` event (default name) and set up a Google Analytics event tag that will be sent on the trigger.

Alternatively, if you have gtag.js deployed on your website you can opt for direct reporting on the Settings page.

### Can I use a renamed dataLayer variable?

Yes, by default this plugin uses a variable named `dataLayer`, however you can change that on the Settings page.

### Can I use a different event name?

Yes, by default this plugin uses a `contact_form_submit` event, however you can change that on the Settings page.
