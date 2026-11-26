# Digital_Elasticsearch

Detects an Elasticsearch cluster with no alive nodes and resets (invalidates)
indexers, writing a flag file and a dedicated log. Target: Magento 2.4.5-p1,
Elasticsearch 7.

## v3 - IMPORTANT design change (why the catalog page is now safe)

Earlier versions hooked the Elasticsearch query path (via a `<preference>` on the
ES7 client adapter in v1, then a plugin on its `query()` method in v2). That path
is dangerous: `Magento\Elasticsearch\SearchAdapter\Adapter::query()` catches ANY
exception from `$client->query()`, logs it as `critical`, and returns an EMPTY
result set. The category page uses this exact path, so any imperfection in our
interceptor silently produced "We can't find products matching the selection."

v3 removes ALL request/query-path code. The module no longer overrides or plugs
into any Elasticsearch, search, collection, layered-navigation, filter, visibility
or stock class. Detection now runs only in cron.

## How it works (v3)

A cron job (`*/5 * * * *`, `Cron\EsHealthCheck`) pings Elasticsearch using
Magento's own configured connection:

- No alive nodes + no flag yet -> write `var/elasticsearch_node_error.flag`
  ("No alive nodes found in your cluster"), invalidate all indexers
  (programmatic `indexer:reset`), log to `var/log/digital_elasticsearch.log`.
- No alive nodes + flag already present -> nothing (already handled).
- Healthy + flag present -> remove flag, log recovery.
- Healthy + no flag -> nothing.
- Non-Elasticsearch engine configured -> job is inert.

No indexer command runs during any storefront/admin request.

## Files

```
app/code/Digital/Elasticsearch/
├── registration.php
├── README.md
├── etc/
│   ├── module.xml
│   ├── di.xml          (dedicated logger ONLY - no preference, no plugin)
│   └── crontab.xml
├── Cron/
│   └── EsHealthCheck.php
├── Service/
│   └── FlagFileService.php
└── Logger/
    ├── Handler.php
    └── Logger.php
```

## Upgrading from v1/v2 (this is what fixes the empty listing)

Replace the whole module folder with this one, then remove the old compiled code
and the old files so nothing from v1/v2 can still load:

```bash
# from Magento root
rm -rf app/code/Digital/Elasticsearch/Plugin
rm -rf app/code/Digital/Elasticsearch/Model
rm -rf app/code/Digital/Elasticsearch/ConnectionPool
rm -f  app/code/Digital/Elasticsearch/Cron/ResetIndexer.php

rm -rf generated/code generated/metadata var/cache var/page_cache
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

Then reload a category page - products return immediately, because the module no
longer participates in the request at all.

## Notes
- OpenSearch (2.4.6+ default): not applicable to 2.4.5-p1, which ships ES7.
- Artifacts: flag `var/elasticsearch_node_error.flag`, log
  `var/log/digital_elasticsearch.log`.

## v3.1 - ReflectionException fix

The engine resolver was injected from the WRONG namespace
(`Magento\Search\Model\EngineResolverInterface`, which does not exist).
Corrected to the real framework interface
`Magento\Framework\Search\EngineResolverInterface` (getCurrentSearchEngine()),
preference-mapped to `Magento\Search\Model\EngineResolver`. The health check now
uses `ClientInterface::testConnection()` (interface-declared) instead of `ping()`.

## v3.2 - Email alert on first detection

When ES is detected down for the FIRST time (same branch that creates the flag
and resets indexers), the module now emails an alert via Magento's Mail
framework (TransportBuilder). It is not re-sent while the flag persists.

- Recipient: configured in etc/di.xml (Service\EmailNotifier `recipientEmail`).
- Subject: "Elasticsearch Health Check Alert".
- Body: connection-failure notice, the actual error message, indexer-reset
  confirmation + list, detection date/time, and the current search engine.
- Email failures are caught and logged; the cron never fails because of email.

Existing flag, indexer-reset, and recovery logic are unchanged.
New files: Service/EmailNotifier.php, etc/email_templates.xml,
view/frontend/email/es_health_alert.html.

## v3.2 - email alert on first detection

Added `Service/AlertMailer.php` (Magento Mail TransportBuilder), an email
template (`etc/email_templates.xml` + `view/frontend/email/es_health_alert.html`),
and one call in `Cron\EsHealthCheck::handleDown()` after the flag is created and
indexers are invalidated. The email fires ONCE per outage (handleDown returns
early while the flag exists). All email failures are caught and logged; the cron
never fails because of email. No existing flag/reset/recovery logic changed.

To: nikunj.vaghasiya@yodep.co.in | Cc: dhrumil@23digital.com.au,
divyesh.nimavat@yodep.co.in, rohit.ranpara@yodep.co.in
