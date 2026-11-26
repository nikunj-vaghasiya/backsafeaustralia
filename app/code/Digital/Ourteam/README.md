# Digital_Ourteam — Hyvä compatible

## What changed vs. the original (Luma) version

| File | Change |
|---|---|
| `view/frontend/templates/about-ourteam.phtml` | Rewritten in Tailwind for Hyvä. Popup/modal removed, `require([...])` block removed (Hyvä has no jQuery/RequireJS). |
| `Block/Ourteam.php` | Sorts by `sort_order` (numeric cast) then `team_id`, collection memoised, `heading` / `subheading` / `limit` arguments, placeholder image fallback, `IdentityInterface` for FPC invalidation. |
| `etc/frontend/routes.xml` *(new)* | Frontend route → `/our-team`. |
| `Controller/Index/Index.php` *(new)* | Renders the page. |
| `view/frontend/layout/ourteam_index_index.xml` *(new)* | Places the block in `content`, 1column layout. |

Admin side is untouched — Hyvä only replaces the frontend.

## Install

```bash
bin/magento module:enable Digital_Ourteam
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

Page is then live at `/our-team`.

## IMPORTANT — Tailwind must see the template

Hyvä purges any class it can't find at build time. Pick one:

**Option A (recommended) — override the template in your Hyvä theme**

```
app/design/frontend/<Vendor>/<theme>/Digital_Ourteam/templates/about-ourteam.phtml
```

The theme's default `content` glob (`../../**/*.phtml`) already covers this. Nothing to configure.

**Option B — add the module to the theme's Tailwind config**

`app/design/frontend/<Vendor>/<theme>/web/tailwind/tailwind.config.js`

```js
content: [
    '../../**/*.phtml',
    '../../*/layout/*.xml',
    '../../../../../../app/code/Digital/Ourteam/view/frontend/templates/**/*.phtml',
],
```

Then rebuild:

```bash
cd app/design/frontend/<Vendor>/<theme>/web/tailwind
npm ci && npm run build-prod
```

Symptom if you skip this: correct HTML, zero styling (white page, stacked images).

## Placing it somewhere else

CMS page / block content:

```
{{block class="Digital\Ourteam\Block\Ourteam" template="Digital_Ourteam::about-ourteam.phtml" heading="Our Team"}}
```

CMS page Layout Update XML:

```xml
<referenceContainer name="content">
    <block class="Digital\Ourteam\Block\Ourteam" name="digital.ourteam.list"
           template="Digital_Ourteam::about-ourteam.phtml">
        <arguments>
            <argument name="heading" xsi:type="string"></argument>
        </arguments>
    </block>
</referenceContainer>
```

Pass `heading=""` to hide the heading, `limit="8"` to cap the number of members.

## Brand colours (from the mockup)

| Token | Hex | Class in template |
|---|---|---|
| Section background | `#002548` | `bg-[#002548]` |
| Name / accent | `#A3E061` | `text-[#A3E061]` |
| Photo background | `#F3F3F5` | `bg-[#F3F3F5]` |
| Divider | white 25% | `border-white/25` |

## Checklist

- `sort_order` in the admin grid must be numeric (1, 2, 3…) — it drives the display order.
- Upload all headshots at the same crop (square, head near the top) — the grid uses `object-cover object-top`.
- `Digital_Base` must be installed (used by `Helper/Data.php` and the admin menu parent `Digital_Base::parent`).
