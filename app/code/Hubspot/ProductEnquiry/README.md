# Hubspot_ProductEnquiry

Captures HubSpot product-enquiry form submissions into a custom Magento 2 table
and shows them in an admin grid (Customers → Product Enquiries).

## How it works

A HubSpot embedded form submits to HubSpot's servers, not to Magento. So we
hook HubSpot's `onFormSubmit` callback, read the field values client-side, and
POST them to a custom Magento controller (`productenquiry/index/save`), which
stores them in the `hubspot_product_enquiry` table. The admin grid reads that
table.

> Note: This README describes the optional CLIENT-SIDE intake (Save.php). If you
> are using the HubSpot Workflow + Webhook approach, follow SETUP_WEBHOOK.md
> instead — that is the recommended path and needs no website code change.

Module name: `Hubspot_ProductEnquiry` (vendor `Hubspot`, module `ProductEnquiry`).

## Install

Copy the folder to `app/code/Hubspot/ProductEnquiry`, then run:

```bash
php bin/magento module:enable Hubspot_ProductEnquiry
php bin/magento setup:upgrade
php bin/magento setup:di:compile        # production / if compiled
php bin/magento setup:static-content:deploy -f
php bin/magento cache:flush
```

## 3. Replace your HubSpot embed with this

Use the same portalId / formId / region as before. The only addition is the
`onFormSubmit` callback that forwards the data to Magento.

```html
<script charset="utf-8" type="text/javascript" src="//js-ap1.hsforms.net/forms/embed/v2.js"></script>
<script>
  hbspt.forms.create({
    portalId: "7713081",
    formId: "3cf797bc-ce5c-435e-bd62-9787503890fb",
    region: "ap1",
    onFormSubmit: function ($form) {
      var data = {};
      $form.serializeArray().forEach(function (field) {
        data[field.name] = field.value;
      });
      fetch("/productenquiry/index/save", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data).toString()
      }).catch(function (e) { console.error("Enquiry save failed", e); });
    }
  });
</script>
```

`onFormSubmit($form)` fires when the visitor submits. `$form` is the jQuery
form object that HubSpot loads, so `serializeArray()` gives every field by its
HubSpot internal name (firstname, lastname, email, phone, etc.). The full
payload is also stored as JSON in the `raw_data` column, so no field is ever
lost even if you add/rename fields in HubSpot later.

> If you prefer to record submissions that completed successfully on HubSpot's
> side, use `onFormSubmitted` instead of `onFormSubmit`. `onFormSubmit` fires
> on submit attempt; `onFormSubmitted` fires after HubSpot confirms.

## 4. View submissions

Admin → **Customers → Product Enquiries**. The grid has filters, sorting,
paging, CSV/XML export and a per-row Delete action.

## Security note

The save controller bypasses Magento's CSRF form-key check, because the request
originates from HubSpot's form (which has no Magento form key). This means the
endpoint is publicly POST-able. To harden it you can:
- add a server-side check (e.g. a shared secret token sent in the body and
  validated in `Save::execute`),
- rate-limit by IP,
- and/or validate the email/required fields before saving.
The full raw payload is stored, so review/sanitize before using it elsewhere.

## Table

`hubspot_product_enquiry`: entity_id, firstname, lastname, email, phone,
company, message, raw_data (JSON of everything submitted), created_at.
