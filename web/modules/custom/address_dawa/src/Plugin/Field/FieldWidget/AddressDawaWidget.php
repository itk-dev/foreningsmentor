<?php

namespace Drupal\address_dawa\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use Drupal\address_dawa\Plugin\Validation\Constraint\AddressDawaConstraint;

/**
 * Plugin implementation of the 'address_dawa' widget.
 *
 * @FieldWidget(
 *   id = "address_dawa",
 *   label = @Translation("Address"),
 *   field_types = {
 *     "address_dawa"
 *   },
 * )
 */
final class AddressDawaWidget extends WidgetBase {

  /**
   * SDFI Adressevælger public token (default).
   *
   * Per SDFI guidance, real user management arrives late 2026 / early 2027.
   * Until then any 10+ character string is accepted; the agency recommends
   * this exact value so applications can be swapped to a real token via a
   * simple config change later. Override per environment by setting
   * `$settings['address_dawa.public_token']` in settings.php (or
   * settings.local.php); see ::publicToken().
   */
  const PUBLIC_TOKEN = 'adressevaelger123';

  /**
   * Resolve the SDFI Adressevælger token to attach to the widget.
   *
   * Reads `$settings['address_dawa.public_token']` if defined, otherwise
   * falls back to the bundled default. Class constants can't call
   * `Settings::get()` (compile-time expression only), so the lookup lives
   * here.
   *
   * @return string
   *   The token to ship to the browser via drupalSettings.
   */
  public static function publicToken(): string {
    return Settings::get('address_dawa.public_token', self::PUBLIC_TOKEN);
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size of address textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $element['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => $this->t('Text shown inside the address field until a value is entered.'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('Address field size: @size', ['@size' => $this->getSetting('size')]);
    $placeholder = $this->getSetting('placeholder');
    if (!empty($placeholder)) {
      $summary[] = $this->t('Placeholder: @placeholder', ['@placeholder' => $placeholder]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item = $items[$delta] ?? NULL;
    $existing_payload = '';
    if ($item && !$item->isEmpty()) {
      $data = $item->get('data')->getValue();
      if (!empty($data)) {
        $existing_payload = json_encode($data);
      }
    }

    $element['#type'] = 'fieldset';
    $element['address'] = [
      '#type' => 'textfield',
      '#title' => $element['#title'] ?? $this->t('Address'),
      '#title_display' => 'invisible',
      '#default_value' => $item->value ?? NULL,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => 255,
      '#attributes' => ['class' => ['js-adressevaelger-element']],
    ];
    $element['payload'] = [
      '#type' => 'hidden',
      '#default_value' => $existing_payload,
      '#attributes' => ['class' => ['js-adressevaelger-payload']],
    ];

    $element['#attached']['library'][] = 'address_dawa/widget';
    $element['#attached']['drupalSettings']['adressevaelger']['token'] = self::publicToken();

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      if (empty($value['address'])) {
        continue;
      }

      $address_type = $this->getFieldSetting('address_type');
      $payload = !empty($value['payload']) ? json_decode($value['payload'], TRUE) : NULL;

      if (!is_array($payload)) {
        if ($this->getFieldSetting('allow_non_danish_address')) {
          $value += [
            'type' => $address_type,
            'id' => 'non_dawa_' . Crypt::hashBase64($value['address']),
            'status' => 1,
            'value' => $value['address'],
            'lat' => 0,
            'lng' => 0,
            'data' => [$value['address']],
          ];
        }
        else {
          $value += [
            'type' => AddressDawaConstraint::ADDRESS_CAN_NOT_BE_FOUND['error_code'],
            'value' => $value['address'],
          ];
        }
        continue;
      }

      $coords = $payload['_wgs84'] ?? [];
      $value += [
        'type' => $address_type,
        'id' => $payload['id_lokalid'] ?? $payload['id'] ?? '',
        'status' => (int) ($payload['status'] ?? 1),
        'value' => $value['address'],
        'lat' => (float) ($coords['lat'] ?? 0),
        'lng' => (float) ($coords['lng'] ?? 0),
        'data' => $payload,
      ];
    }
    return $values;
  }

}
