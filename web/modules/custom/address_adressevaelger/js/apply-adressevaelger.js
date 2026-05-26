/**
 * @file
 * Wire the Adressevaelger autocomplete onto address widget inputs.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Look up the first matching hidden input within the widget wrapper.
   */
  function pickInput(wrapper, className) {
    if (!wrapper) {
      return null;
    }
    return wrapper.querySelector('.' + className);
  }

  /**
   * Best-effort extraction of structured fields from an Adressevaelger record.
   * The upstream response shape isn't strictly versioned, so try several
   * known paths and fall through to empty.
   */
  function extract(record) {
    if (!record) {
      return {};
    }
    var vejnavn = record.vejnavn
      || record.vej && record.vej.navn
      || record.adgangsadresse && record.adgangsadresse.vejstykke && record.adgangsadresse.vejstykke.navn
      || '';
    var husnr = record.husnummer
      || record.husnr
      || record.adgangsadresse && record.adgangsadresse.husnr
      || '';
    var postal = (record.postnummer && record.postnummer.nr)
      || (record.adgangsadresse && record.adgangsadresse.postnummer && record.adgangsadresse.postnummer.nr)
      || '';
    var city = (record.postnummer && record.postnummer.navn)
      || (record.adgangsadresse && record.adgangsadresse.postnummer && record.adgangsadresse.postnummer.navn)
      || '';

    var lat = '', lng = '';
    var wgs = record.adgangspunkt && (record.adgangspunkt.koordinater_wgs84 || record.adgangspunkt.koordinaterWGS84);
    if (Array.isArray(wgs) && wgs.length >= 2) {
      lng = wgs[0];
      lat = wgs[1];
    }
    if (!lat && record.position) {
      lng = record.position.x !== undefined ? record.position.x : (record.position[0] || '');
      lat = record.position.y !== undefined ? record.position.y : (record.position[1] || '');
    }

    var street = [vejnavn, husnr].filter(Boolean).join(' ').trim();
    var text = record.tekst || record.betegnelse || record.adressebetegnelse || '';

    return {
      id: record.id || '',
      text: text,
      street: street,
      postal_code: postal,
      city: city,
      lat: lat === undefined || lat === null ? '' : String(lat),
      lng: lng === undefined || lng === null ? '' : String(lng)
    };
  }

  /**
   * Initialize Adressevaelger on a single widget input.
   */
  function initWidget(input) {
    var token = drupalSettings && drupalSettings.adressevaelger && drupalSettings.adressevaelger.token;
    if (!token) {
      console.warn('Adressevaelger: no API token configured at /admin/config/services/adressevaelger.');
      return;
    }
    if (typeof adressevaelger === 'undefined' || !adressevaelger.adressevaelger) {
      console.warn('Adressevaelger bundle not loaded.');
      return;
    }

    // Wrap the input so the suggestions list anchors correctly.
    if (!input.closest('.autocomplete-container')) {
      var wrapper = document.createElement('div');
      wrapper.className = 'autocomplete-container';
      input.parentNode.replaceChild(wrapper, input);
      wrapper.appendChild(input);
    }

    var widget = input.closest('.address-adressevaelger-widget')
      || input.closest('fieldset')
      || input.parentNode;

    adressevaelger.adressevaelger(input, {
      token: token,
      select: function (selected) {
        var data = extract(selected);
        input.value = data.text || input.value;
        var assign = function (cls, value) {
          var el = pickInput(widget, cls);
          if (el) {
            el.value = value || '';
          }
        };
        assign('js-adressevaelger-id', data.id);
        assign('js-adressevaelger-street', data.street);
        assign('js-adressevaelger-postal-code', data.postal_code);
        assign('js-adressevaelger-city', data.city);
        assign('js-adressevaelger-lat', data.lat);
        assign('js-adressevaelger-lng', data.lng);
        var dataField = pickInput(widget, 'js-adressevaelger-data');
        if (dataField) {
          try {
            dataField.value = JSON.stringify(selected);
          } catch (e) {
            dataField.value = '';
          }
        }
      }
    });

    // Clear structured fields if the user edits the text manually after
    // selecting — otherwise stale ids/coords stick around.
    input.addEventListener('input', function () {
      var idField = pickInput(widget, 'js-adressevaelger-id');
      if (idField && idField.value) {
        ['id', 'street', 'postal-code', 'city', 'lat', 'lng', 'data'].forEach(function (cls) {
          var el = pickInput(widget, 'js-adressevaelger-' + cls);
          if (el) {
            el.value = '';
          }
        });
      }
    });
  }

  Drupal.behaviors.addressAdressevaelger = {
    attach: function (context) {
      once('address-adressevaelger', '.js-adressevaelger-element', context)
        .forEach(initWidget);
    }
  };
})(Drupal, drupalSettings, once);
