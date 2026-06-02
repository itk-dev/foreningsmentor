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

## Frontend build

The widget's JavaScript is bundled by [Webpack](https://webpack.js.org/)
(configured in `webpack.config.js`).

- **Source**: `assets/js/apply-adressevaelger.js` — `import proj4 from 'proj4'`
  at the top brings in the [proj4](https://www.npmjs.com/package/proj4) npm
  package.
- **Output**: `js/widget.bundle.js` — single IIFE bundle with proj4 inlined.
  Committed to the repository so Drupal can serve it without a build step at
  runtime; the source of truth is `package.json` + `assets/js/`.
- **Out of scope**: `js/adressevaelger.iife.js` is the SDFI Adressevælger
  vendor IIFE. It is loaded as a separate `<script>` (exposes the
  `adressevaelger` global) and is not bundled.

Rebuild with the project's `node` compose service after bumping a dependency
or editing source. Run from the project root:

```sh
# Install (or refresh) deps; postinstall hooks fire, including `npm run build`.
docker compose run --rm --workdir /app/web/modules/custom/address_dawa --user "$(id -u):$(id -g)" node npm install

# Or, when deps are unchanged and you just edited source, build only.
docker compose run --rm --workdir /app/web/modules/custom/address_dawa --user "$(id -u):$(id -g)" node npm run build

# Commit the diff (package-lock.json + the rebuilt bundle).
git add package.json package-lock.json js/widget.bundle.js
```
