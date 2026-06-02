# Adressevælger

Drupal field type, widget, and formatter for Danish postal addresses, backed by
[SDFI Adressevælger](https://confluence.sdfi.dk/display/ADV/).

This module replaces the deprecated `address_dawa` module. It keeps the same
field schema and the same plugin ids (`address_dawa` for the field type, widget,
and formatter), so existing field configuration and data continue to work
without migration. Only the underlying address-lookup service has changed:
from DAWA (`dawa.aws.dk`) to Adressevælger (`adressevaelger.dk`).

## Vendored assets

| File | Source | Version |
|------|--------|---------|
| `js/adressevaelger.iife.js` | [Klimadatastyrelsen/adressevaelger](https://github.com/Klimadatastyrelsen/adressevaelger/blob/main/dist/adressevaelger.iife.js) | 5.0.0 |
| `css/adressevaelger.css`    | [Klimadatastyrelsen/adressevaelger](https://github.com/Klimadatastyrelsen/adressevaelger/blob/main/dist/adressevaelger.css)    | 5.0.0 |
| `js/proj4.js`               | [proj4js/proj4js](https://github.com/proj4js/proj4js/releases) `dist/proj4-src.js` | 2.20.8 |

To upgrade, download the matching file from the linked release and overwrite
the vendored copy.

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
