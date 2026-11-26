# Setup: HubSpot Workflow + Webhook + Magento Storage

Your website form does NOT change. Keep your original HubSpot embed exactly as
it was. HubSpot pushes submissions to Magento server-side via the workflow.

Data flow:
  Visitor submits HubSpot form
    -> HubSpot records the contact
    -> Workflow (trigger = that form submission) fires "Send a webhook"
    -> POSTs JSON to https://YOUR-DOMAIN/productenquiry/webhook/index?token=SECRET
    -> Magento stores it in hubspot_product_enquiry
    -> Visible under Customers > Product Enquiries


## PART A — Magento

1. Copy the module to app/code/Hubspot/ProductEnquiry

2. Open Controller/Webhook/Index.php and set the secret:

       const SHARED_SECRET = 'a-long-random-string-you-pick';

   Generate one, e.g.:  php -r "echo bin2hex(random_bytes(24));"

3. Install / enable:

       php bin/magento module:enable Hubspot_ProductEnquiry
       php bin/magento setup:upgrade
       php bin/magento setup:di:compile
       php bin/magento cache:flush

4. Your endpoint is now:

       https://YOUR-DOMAIN/productenquiry/webhook/index?token=YOUR_SECRET

   It MUST be HTTPS (HubSpot rejects non-HTTPS webhook URLs).

5. Quick sanity check from a terminal (replace domain + secret):

       curl -i -X POST "https://YOUR-DOMAIN/productenquiry/webhook/index?token=YOUR_SECRET" \
            -H "Content-Type: application/json" \
            -d '{"properties":{"firstname":"Test","email":"test@example.com","message":"hello"}}'

   You should get {"success":true,...} and a new row in the admin grid.


## PART B — HubSpot

1. Automation > Workflows > Create workflow > From scratch > Contact-based.

2. TRIGGER: choose "Met filter criteria".
   - Filter type: Form submission
   - Pick your product-enquiry form (ID 3cf797bc-ce5c-435e-bd62-9787503890fb)
   - Condition: has filled out [the form] on any page
   Click Apply / Next. This enrolls a contact ONLY when they submit that form.

3. On the canvas, click the + between the trigger and "End" to add an action.
   In the action list, open the "Data ops" section and choose "Send a webhook".

4. Configure the webhook action:
   - Method: POST
   - Webhook URL:
       https://YOUR-DOMAIN/productenquiry/webhook/index?token=YOUR_SECRET
   - Authentication type: None
     (the ?token= in the URL is your authentication)
   Save the action.

5. Top right: "Review and turn on".
   - Enrollment: "Only enroll contacts who meet the trigger after this is turned on"
     (recommended) — or enroll existing if you want to backfill.
   - Turn on workflow.

6. TEST: submit your form once (or use HubSpot's "Test" with a real contact).
   Confirm a new row appears in Magento under Customers > Product Enquiries.
   If nothing arrives, check the workflow's action logs in HubSpot and
   Magento var/log/system.log / exception.log.


## Notes

- The webhook sends the contact's properties. Known fields land in their own
  columns; the complete payload is saved in `raw_data` (JSON), so once you see
  a real submission you can adjust the field mapping in Index.php if your
  "message" field uses a different internal property name.
- Webhooks can occasionally be delivered more than once. If duplicate rows
  matter to you, add a de-dupe check (e.g. on email + created minute) — ask and
  I'll add it.
- Optional stronger auth: instead of the token, HubSpot can sign requests
  ("Include request signature in header"), validated with your HubSpot App
  client secret. The token method is simpler and fine for this use case.
