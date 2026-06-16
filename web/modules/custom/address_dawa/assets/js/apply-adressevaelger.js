import proj4 from 'proj4';

/**
 * @file
 * Drupal behavior that attaches the SDFI Adressevælger widget to address
 * textfields rendered by the `address_dawa` widget plugin.
 *
 * Required markup (built by AddressDawaWidget::formElement):
 *   <fieldset>
 *     <input type="text" class="js-adressevaelger-element" />
 *     <input type="hidden" class="js-adressevaelger-payload" />
 *   </fieldset>
 *
 * On selection the JSON-encoded address payload (with reprojected WGS84
 * coordinates under `_wgs84`) is written into the hidden field for the
 * server-side `massageFormValues` to consume. The payload is cleared on any
 * subsequent keystroke so editors who change the text without picking a new
 * suggestion can be detected as "not selected from list".
 *
 * Optional sibling: if the same form contains an input with the class
 * `js-adressevaelger-postalcode`, the address field is watched and the
 * first 4-digit Danish postal code parsed from its current text is mirrored
 * into that input — both while the user types and after picking a
 * suggestion (which fills the field with "Street 1, 8000 Aarhus C" or
 * similar). This is opt-in: only forms that add the class get the sync.
 *
 * This file is bundled by esbuild into `js/widget.bundle.js`. proj4 is
 * inlined into that bundle via the `import` above; the IIFE bundle
 * `js/adressevaelger.iife.js` is loaded separately and exposes the
 * `adressevaelger` global.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  // Define EPSG:25832 once. proj4 already knows EPSG:4326 (WGS84) by default.
  proj4.defs(
    'EPSG:25832',
    '+proj=utm +zone=32 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs'
  );

  /**
   * Reproject Adressevælger coordinates to WGS84.
   *
   * @param {object} koords
   *   `{x, y}` in EPSG:25832 (UTM zone 32N).
   * @return {{lng: number, lat: number}|null}
   *   Reprojected coordinates, or null if input was missing.
   */
  function reproject(koords) {
    if (!koords) {
      return null;
    }
    if (typeof koords.x !== 'number' || typeof koords.y !== 'number') {
      return null;
    }
    const out = proj4('EPSG:25832', 'EPSG:4326', [koords.x, koords.y]);
    return { lng: out[0], lat: out[1] };
  }

  /**
   * Initialize Adressevælger on a single widget input.
   */
  function initElement(input) {
    const token = drupalSettings?.adressevaelger?.token;
    if (!token) {
      console.warn('Adressevaelger: no API token configured.');
      return;
    }
    // `adressevaelger` is exposed as a global by the IIFE bundle
    // `js/adressevaelger.iife.js` (loaded by this same library).
    if (typeof adressevaelger === 'undefined' || !adressevaelger.adressevaelger) {
      console.warn('Adressevaelger bundle not loaded.');
      return;
    }

    // Wrap the input so the suggestions list anchors correctly.
    if (!input.closest('.autocomplete-container')) {
      const wrapper = document.createElement('div');
      wrapper.className = 'autocomplete-container';
      input.parentNode.replaceChild(wrapper, input);
      wrapper.appendChild(input);
    }

    const fieldset = input.closest('fieldset');
    const payload = fieldset?.querySelector('.js-adressevaelger-payload');
    const form = input.closest('form');
    const postalCodeInput = form?.querySelector('.js-adressevaelger-postalcode');

    // Mirror a postal code into the sibling postal-code input. Either
    // call with an explicit `postnr` (e.g. straight from the SDFI
    // selection object) or with no argument to parse a 4-digit Danish
    // code out of the address textfield's current value.
    //
    // Only writes when a postal code is present so users editing other
    // parts of the line don't lose their existing value; clearing back
    // to empty is intentional only when the address itself is cleared.
    function syncPostalCode(postnr) {
      if (!postalCodeInput) {
        return;
      }
      if (postnr) {
        postnr = String(postnr);
        if (postalCodeInput.value !== postnr) {
          postalCodeInput.value = postnr;
          postalCodeInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        return;
      }
      const text = input.value;
      if (!text) {
        if (postalCodeInput.value !== '') {
          postalCodeInput.value = '';
          postalCodeInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        return;
      }
      const match = text.match(/\b\d{4}\b/);
      if (!match) {
        return;
      }
      if (postalCodeInput.value === match[0]) {
        return;
      }
      postalCodeInput.value = match[0];
      postalCodeInput.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Extract the postal code from an SDFI suggestion. The shape differs
    // between `adresse` and `adgangsadresse` responses, hence the cascade.
    function postalFromSelection(selected) {
      return selected?.adresse?.postnummer?.nr
        ?? selected?.postnummer?.nr
        ?? selected?.adgangsadresse?.postnummer?.nr
        ?? null;
    }

    adressevaelger.adressevaelger(input, {
      token: token,
      select: function (selected) {
        if (!selected) {
          return;
        }

        input.value = selected.adressebetegnelse ?? selected.tekst ?? input.value;

        let koords = selected.adresse?.husnummer?.adgangspunkt?.koordinater;
        if (!koords && selected.adgangspunkt) {
          // adgangsadresse responses put coords at the root.
          koords = selected.adgangspunkt.koordinater;
        }
        const wgs84 = reproject(koords);
        const enriched = Object.assign({}, selected);
        if (wgs84) {
          enriched._wgs84 = wgs84;
        }

        if (payload) {
          payload.value = JSON.stringify(enriched);
        }

        // Read postal code straight from the structured selection so
        // mouse picks work regardless of when SDFI internally writes
        // back to `input.value`.
        const postnr = postalFromSelection(selected);
        if (postnr) {
          syncPostalCode(postnr);
        }
        else {
          // Fall back to parsing whatever ended up in the textfield.
          syncPostalCode();
        }
      }
    });

    // Clearing the hidden payload on user edit forces the user to re-pick
    // a suggestion. Without this, an edited textfield value combined with a
    // stale payload would be persisted as an unrelated selection.
    input.addEventListener('input', function () {
      if (payload) {
        payload.value = '';
      }
      syncPostalCode();
    });
  }

  Drupal.behaviors.addressDawa = {
    attach: function (context) {
      once('address-dawa', '.js-adressevaelger-element', context)
        .forEach(initElement);
    }
  };
})(Drupal, drupalSettings, once);
