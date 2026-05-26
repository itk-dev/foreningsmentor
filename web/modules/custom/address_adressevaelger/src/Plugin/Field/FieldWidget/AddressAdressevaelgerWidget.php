<?php

declare(strict_types=1);

namespace Drupal\address_adressevaelger\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\address_adressevaelger\Plugin\Validation\Constraint\AddressAdressevaelgerConstraint;

/**
 * Plugin implementation of the 'address_adressevaelger' widget.
 *
 * @FieldWidget(
 *   id = "address_adressevaelger",
 *   label = @Translation("Adressevælger autocomplete"),
 *   field_types = {
 *     "address_adressevaelger"
 *   }
 * )
 */
final class AddressAdressevaelgerWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'size' => 60,
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
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
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    $summary[] = $this->t('Size: @size', ['@size' => $this->getSetting('size')]);
    $placeholder = $this->getSetting('placeholder');
    if ($placeholder !== '') {
      $summary[] = $this->t('Placeholder: @placeholder', ['@placeholder' => $placeholder]);
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $item = $items[$delta] ?? NULL;
    $token = (string) \Drupal::config('address_adressevaelger.settings')->get('api_token');

    $element += [
      '#type' => 'fieldset',
      '#attributes' => ['class' => ['address-adressevaelger-widget']],
    ];

    $element['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Address'),
      '#default_value' => $item?->get('value')->getValue() ?? '',
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => 255,
      '#attributes' => [
        'class' => ['js-adressevaelger-element'],
        'autocomplete' => 'off',
      ],
    ];

    $hidden_props = ['id', 'street', 'postal_code', 'city', 'lat', 'lng'];
    foreach ($hidden_props as $prop) {
      $element[$prop] = [
        '#type' => 'hidden',
        '#default_value' => (string) ($item?->get($prop)->getValue() ?? ''),
        '#attributes' => ['class' => ['js-adressevaelger-' . str_replace('_', '-', $prop)]],
      ];
    }

    $element['data'] = [
      '#type' => 'hidden',
      '#default_value' => $item && !empty($item->getData()) ? json_encode($item->getData()) : '',
      '#attributes' => ['class' => ['js-adressevaelger-data']],
    ];

    $element['#attached']['library'][] = 'address_adressevaelger/adressevaelger';
    $element['#attached']['drupalSettings']['adressevaelger']['token'] = $token;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    foreach ($values as &$value) {
      $address_text = trim((string) ($value['value'] ?? ''));
      if ($address_text === '') {
        $value = [
          'id' => NULL,
          'value' => NULL,
          'street' => NULL,
          'postal_code' => NULL,
          'city' => NULL,
          'lat' => NULL,
          'lng' => NULL,
          'data' => [],
        ];
        continue;
      }

      $id = trim((string) ($value['id'] ?? ''));
      if ($id === '') {
        $id = AddressAdressevaelgerConstraint::NON_RESOLVED_ID_PREFIX . Crypt::hashBase64($address_text);
      }

      $raw = trim((string) ($value['data'] ?? ''));
      $data = [];
      if ($raw !== '') {
        $decoded = json_decode($raw, TRUE);
        if (is_array($decoded)) {
          $data = $decoded;
        }
      }

      $value = [
        'id' => $id,
        'value' => $address_text,
        'street' => $this->cleanString($value['street'] ?? NULL),
        'postal_code' => $this->cleanString($value['postal_code'] ?? NULL),
        'city' => $this->cleanString($value['city'] ?? NULL),
        'lat' => $this->cleanFloat($value['lat'] ?? NULL),
        'lng' => $this->cleanFloat($value['lng'] ?? NULL),
        'data' => $data,
      ];
    }
    return $values;
  }

  /**
   * Normalize a string input to a trimmed string or NULL.
   */
  private function cleanString(?string $value): ?string {
    if ($value === NULL) {
      return NULL;
    }
    $trimmed = trim($value);
    return $trimmed === '' ? NULL : $trimmed;
  }

  /**
   * Normalize a float input.
   */
  private function cleanFloat(mixed $value): ?float {
    if ($value === NULL || $value === '') {
      return NULL;
    }
    return is_numeric($value) ? (float) $value : NULL;
  }

}
