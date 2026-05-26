/**
 * @file
 * Wire the Adressevaelger autocomplete onto address widget inputs.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  /**
   * Write a value into every matching element in the scope. Targets both the
   * widget's hidden inputs (inside .address-adressevaelger-widget) AND any
   * sibling fields tagged via hook_form_alter on the same form.
   */
  function writeAll(scope, className, value) {
    if (!scope) {
      return;
    }
    var v = value === undefined || value === null ? '' : String(value);
    scope.querySelectorAll('.' + className).forEach(function (el) {
      el.value = v;
    });
  }

  /**
   * Whether at least one element in the scope is non-empty.
   */
  function anyHasValue(scope, className) {
    if (!scope) {
      return false;
    }
    var els = scope.querySelectorAll('.' + className);
    for (var i = 0; i < els.length; i++) {
      if (els[i].value) {
        return true;
      }
    }
    return false;
  }

  /**
   * Extract structured fields from an Adressevaelger /adresser/{id} or
   * /husnumre/{id} response. Both responses wrap the address-detail object
   * one or two levels deep; normalize to a single shape first.
   */
  function extract(record) {
    if (!record) {
      return {};
    }
    var adresse = record.adresse || null;
    var husnummer = (adresse && adresse.husnummer) || record.husnummer || null;
    var id = (adresse && adresse.id_lokalid)
      || (husnummer && husnummer.id_lokalid)
      || record.id
      || '';
    var text = (adresse && adresse.adressebetegnelse)
      || (husnummer && husnummer.adgangsadressebetegnelse)
      || record.titel
      || '';
    var vejnavn = (husnummer && husnummer.vejnavn) || '';
    var husnr = (husnummer && husnummer.husnummertekst) || '';
    var street = [vejnavn, husnr].filter(Boolean).join(' ').trim();
    var postnummer = husnummer && husnummer.postnummer;
    var postal = (postnummer && postnummer.postnr) || '';
    var city = (postnummer && postnummer.navn) || '';
    // Coordinates from adressevaelger are EPSG:25832 (UTM). We don't convert
    // to WGS84 here — leave lat/lng blank for new rows. (Legacy DAWA rows
    // keep their WGS84 values from before the migration.)
    return {
      id: id,
      text: text,
      street: street,
      postal_code: postal,
      city: city,
      lat: '',
      lng: ''
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

    // Broadcast updates to every matching element in the enclosing form.
    // That hits the widget's hidden inputs *and* any sibling fields that
    // hook_form_alter has tagged (e.g. a separate `field_postal_code`).
    var scope = input.closest('form') || input.parentNode;

    adressevaelger.adressevaelger(input, {
      token: token,
      select: function (selected) {
        var data = extract(selected);
        input.value = data.text || input.value;
        writeAll(scope, 'js-adressevaelger-id', data.id);
        writeAll(scope, 'js-adressevaelger-street', data.street);
        writeAll(scope, 'js-adressevaelger-postal-code', data.postal_code);
        writeAll(scope, 'js-adressevaelger-city', data.city);
        writeAll(scope, 'js-adressevaelger-lat', data.lat);
        writeAll(scope, 'js-adressevaelger-lng', data.lng);
        var dataJson = '';
        try { dataJson = JSON.stringify(selected); } catch (e) { dataJson = ''; }
        writeAll(scope, 'js-adressevaelger-data', dataJson);
      }
    });

    // If the address widget owns a hidden id input and that id is populated
    // (i.e. an address was previously resolved here), wipe the structured
    // fields when the user edits the address text — keeping stale ids/
    // coordinates would be misleading. On forms without a hidden id input
    // (e.g. /signup), this is a no-op and the user's manually-typed postal
    // code survives.
    input.addEventListener('input', function () {
      if (!anyHasValue(scope, 'js-adressevaelger-id')) {
        return;
      }
      ['id', 'street', 'postal-code', 'city', 'lat', 'lng', 'data'].forEach(function (suffix) {
        writeAll(scope, 'js-adressevaelger-' + suffix, '');
      });
    });
  }

  Drupal.behaviors.addressAdressevaelger = {
    attach: function (context) {
      once('address-adressevaelger', '.js-adressevaelger-element', context)
        .forEach(initWidget);
    }
  };
})(Drupal, drupalSettings, once);
