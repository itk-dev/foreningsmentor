# Address DAWA

Drupal field type, widget, and formatter for Danish postal addresses, backed by
[SDFI Adressevælger](https://confluence.sdfi.dk/display/ADV/).

The module is named `address_dawa` and exposes plugin ids `address_dawa` (field
type, widget, formatter) for historical reasons: it used to be backed by DAWA
(`dawa.aws.dk`), and keeping the names lets existing field configuration and
stored row data continue to work without migration. Only the underlying
address-lookup service has changed.

## API token

The widget uses the SDFI public token `adressevaelger123`. Per SDFI, real user
management is expected in late 2026 / early 2027; until then any 10+ character
token works. The token is shipped to the browser via `drupalSettings` and sent
directly from the browser to `https://adressevaelger.dk` — no server-side
proxy is required.

## Coordinate reprojection

Adressevælger returns coordinates in EPSG:25832 (ETRS89 / UTM32N). The widget
glue reprojects them to WGS84 in the browser (via proj4) before persisting,
preserving the meaning of the `lat` / `lng` columns.
