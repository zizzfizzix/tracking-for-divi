# Tracking for Divi

**Contributors:** kubawtf  
**Tags:** divi, dataLayer, tracking, tag manager  
**Requires at least:** 5.3  
**Tested up to:** 6.2.2  
**Requires PHP:** 7.4  
**Stable tag:** 0.1.1  
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
  formData: {
    name: string,
    email: string,
    message: string,
  }
}
```

It's up to you to use or discard the form data.

### Google Analytics 4 reporting

The Google Analytics event is sent like so:

```typescript
gtag(
  "event",
  "contact_form_submit",
  {
    formId: "0",
  }
);
```

The form data isn't sent as that could seriously violate user privacy.

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

**Note:** This plugin hasn't been tested with Divi forms that redirect to a success page. It should work just as well, however the redirect could interrupt the tracking flow.

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

## Screenshots

## Changelog

### 0.2.0

- Added reporting directly to Google Analytics and Google Ads using gtag.js.
- Added a settings screen with ability to rename dataLayer, change the event names and choose which events to send.

### 0.1.1

- Fixed a rename omission that broke the plugin.

### 0.1.0

- Initial release, supporting only a dataLayer push
