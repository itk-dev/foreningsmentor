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
 */
(function (Drupal, drupalSettings) {
  'use strict';

  // Define EPSG:25832 once. proj4 already knows EPSG:4326 (WGS84) by default.
  if (typeof proj4 !== 'undefined') {
    proj4.defs(
      'EPSG:25832',
      '+proj=utm +zone=32 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs'
    );
  }

  /**
   * Reproject Adressevælger coordinates to WGS84.
   *
   * @param {object} koords
   *   `{x, y}` in EPSG:25832 (UTM zone 32N).
   * @return {{lng: number, lat: number}|null}
   *   Reprojected coordinates, or null if input was missing or proj4 absent.
   */
  function reproject(koords) {
    if (!koords || typeof proj4 === 'undefined') {
      return null;
    }
    if (typeof koords.x !== 'number' || typeof koords.y !== 'number') {
      return null;
    }
    var out = proj4('EPSG:25832', 'EPSG:4326', [koords.x, koords.y]);
    return { lng: out[0], lat: out[1] };
  }

  function initElement(input) {
    if (input.closest('.autocomplete-container')) {
      return;
    }
    if (typeof window.adressevaelger === 'undefined') {
      return;
    }

    var wrapper = document.createElement('div');
    wrapper.className = 'autocomplete-container';
    input.parentNode.replaceChild(wrapper, input);
    wrapper.appendChild(input);

    var fieldset = wrapper.closest('fieldset');
    var payload = fieldset
      ? fieldset.querySelector('.js-adressevaelger-payload')
      : null;

    window.adressevaelger.adressevaelger(input, {
      token: drupalSettings.adressevaelger.token,
      select: function (selected) {
        if (!selected) {
          return;
        }

        input.value =
          selected.adressebetegnelse || selected.tekst || input.value;

        var koords =
          selected.adresse &&
          selected.adresse.husnummer &&
          selected.adresse.husnummer.adgangspunkt &&
          selected.adresse.husnummer.adgangspunkt.koordinater;
        if (!koords && selected.adgangspunkt) {
          // adgangsadresse responses put coords at the root.
          koords = selected.adgangspunkt.koordinater;
        }
        var wgs84 = reproject(koords);
        var enriched = Object.assign({}, selected);
        if (wgs84) {
          enriched._wgs84 = wgs84;
        }

        if (payload) {
          payload.value = JSON.stringify(enriched);
        }
      },
    });

    // Clearing the hidden payload on user edit forces the user to re-pick
    // a suggestion. Without this, an edited textfield value combined with a
    // stale payload would be persisted as an unrelated selection.
    input.addEventListener('input', function () {
      if (payload) {
        payload.value = '';
      }
    });
  }

  Drupal.behaviors.adressevaelger = {
    attach: function (context) {
      context
        .querySelectorAll('.js-adressevaelger-element')
        .forEach(initElement);
    },
  };
})(Drupal, drupalSettings);
