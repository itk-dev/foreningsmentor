# Address Adressevælger

Drupal field type, widget and formatters backed by [Klimadatastyrelsen
Adressevælger](https://adressevaelger.dk/) — the official Danish address
lookup service. Replaces the deprecated DAWA (`dawa.aws.dk`) integration
that the custom-forked `address_dawa` module provided.

## What you get

- A composite `address_adressevaelger` field type storing:
  `id`, `value`, `street`, `postal_code`, `city`, `lat`, `lng`, `data`.
- A widget that renders a textfield with an Adressevælger-powered
  autocomplete; on selection it populates hidden inputs so the
  structured columns are saved alongside the raw text.
- A formatter (`address_adressevaelger_clean`) that outputs `value` wrapped
  in `<span translate="no">`.
- A settings form at `/admin/config/services/adressevaelger` for the API
  token.

## Configuration

1. Request an API token from Klimadatastyrelsen.
2. Either save it via `/admin/config/services/adressevaelger`, or
   override per environment in `settings.local.php`:

   ```php
   $config['address_adressevaelger.settings']['api_token'] = getenv('ADRESSEVAELGER_TOKEN');
   ```

The token is sent to `drupalSettings` only on pages whose forms include
the widget, so it isn't broadcast site-wide.

## Migration from `address_dawa`

The `hook_install()` in `address_adressevaelger.install` performs a
SQL-only migration of any existing `field_address` of type
`address_dawa` to the new type. The legacy DAWA tables are snapshotted
to `*_dawa_backup`, the old field storage is dropped, the new storage
is created (with UUIDs and settings matching the committed config in
`config/sync`), and rows are copied back via `INSERT … SELECT`. The
backup tables are dropped before the hook returns; no data is held in
PHP memory.

Legacy rows keep their `id`/`value`/`lat`/`lng`/`data` columns. The new
`street`/`postal_code`/`city` columns are NULL until each row is
re-saved through the new widget.

The hook is idempotent: on a fresh install with no legacy field, it's
a no-op. There is also a `hook_post_update_N` that delegates to the
same function, for the edge case where the module is installed
manually without the migration.

## Coexistence with the legacy `address_dawa` module

`address_dawa` stays installed alongside this module for one release
cycle after deployment. Its field type plugin is hidden from the
"Add field" UI via `hook_field_info_alter` so admins don't accidentally
pick it for new bundles, but the classes remain loaded so anything
that happens to reference them at runtime keeps working. The follow-up
PR uninstalls `address_dawa`, deletes its directory, and removes the
`no_ui` alter — at which point this module no longer depends on the
legacy code in any way.

## Vendored JavaScript

`js/adressevaelger.iife.js` is a verbatim copy of the IIFE bundle from
<https://github.com/Klimadatastyrelsen/adressevaelger>, mirrored via
[itk-dev/deltag.aarhus.dk PR #658](https://github.com/itk-dev/deltag.aarhus.dk/pull/658).
Do not edit it; replace the whole file when picking up a new upstream
release. `js/apply-adressevaelger.js` is the Drupal behavior that wires
the bundle onto inputs carrying the `js-adressevaelger-element` class.
