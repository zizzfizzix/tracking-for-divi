# Divi Form Tracking

**Contributors:** kubawtf  
**Tags:** divi, dataLayer, tracking, tag manager  
**Requires at least:** 4.7  
**Tested up to:** 6.2.2  
**Requires PHP:** 7.1  
**Stable tag:** 0.1.0  
**License:** Apache 2.0    
**License URI:** <https://www.apache.org/licenses/LICENSE-2.0>

If you want to track your completed form submissions using the Divi theme look no further.

## Description

Ever wanted to track the built-in contact form from Divi? If you tried using Google Tag Manager's built-in form submit events to do it you probably noticed an inflated number of events flowing.

This is because the form submission event is trigger even if there are form validation errors or the server returns another error.

Since Divi doesn't provide any JavaScript events to hook into there is no straightforward way to find out if a message has actually been sent.

To get around this, this plugin listens to the network communication that is happening underneath. It's based on the same mechanism Divi itself uses.

The structure of the object pushed to the dataLayer is the following:

```typescript
{
  event: "contact_form_submit",
  formData: {
    name: string,
    email: string,
    message: string,
  }
}
```

**Note:** This plugin hasn't been tested with Divi forms that redirect to a success page. It should work just as well, however the redirect could interrupt the tracking flow.

## Installation

All you need is to install it by uploading the zip file or using the plugin directory from within your WordPress website.

There is no configuration necessary or available. Activate and you're ready to go. However, it will only work if your current theme is *Divi* or its child theme.

## Frequently Asked Questions

### How do I track form submissions with Google Analytics?

You can use Google Tag Manager to set up a trigger on a `contact_form_submit` event and set up a Google Analytics event tag that will be sent on the trigger.

### Can I use a renamed dataLayer variable?

No, not right now. This plugin only supports a variable named `dataLayer`, however let me know if you'd need this functionality.

## Screenshots

## Changelog

### 0.1.0

- Initial release, supporting only a dataLayer push
