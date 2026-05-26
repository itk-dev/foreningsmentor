<?php

declare(strict_types=1);

namespace Drupal\address_adressevaelger\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;
use Drupal\address_adressevaelger\AddressAdressevaelgerItemInterface;

/**
 * Plugin implementation of the 'address_adressevaelger' field type.
 *
 * @FieldType(
 *   id = "address_adressevaelger",
 *   label = @Translation("Address (Adressevælger)"),
 *   description = @Translation("Stores a Danish address resolved via Klimadatastyrelsen Adressevælger."),
 *   default_widget = "address_adressevaelger",
 *   default_formatter = "address_adressevaelger",
 *   constraints = {"AddressAdressevaelger" = {}}
 * )
 */
class AddressAdressevaelgerItem extends FieldItemBase implements AddressAdressevaelgerItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    return [
      'columns' => [
        'id' => [
          'type' => 'varchar',
          'description' => 'Address UUID (DAR id)',
          'length' => 64,
        ],
        'value' => [
          'type' => 'varchar',
          'description' => 'Full address text (betegnelse)',
          'length' => 255,
        ],
        'street' => [
          'type' => 'varchar',
          'description' => 'Street name and number',
          'length' => 255,
        ],
        'postal_code' => [
          'type' => 'varchar',
          'description' => 'Postal code',
          'length' => 16,
        ],
        'city' => [
          'type' => 'varchar',
          'description' => 'City',
          'length' => 128,
        ],
        'lat' => [
          'type' => 'float',
          'description' => 'Latitude (WGS84)',
        ],
        'lng' => [
          'type' => 'float',
          'description' => 'Longitude (WGS84)',
        ],
        'data' => [
          'type' => 'blob',
          'size' => 'big',
          'serialize' => TRUE,
        ],
      ],
      'indexes' => [
        'id' => ['id'],
        'postal_code' => ['postal_code'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    $properties['id'] = DataDefinition::create('string')
      ->setLabel(t('Address UUID'));

    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Address text'));

    $properties['street'] = DataDefinition::create('string')
      ->setLabel(t('Street'));

    $properties['postal_code'] = DataDefinition::create('string')
      ->setLabel(t('Postal code'));

    $properties['city'] = DataDefinition::create('string')
      ->setLabel(t('City'));

    $properties['lat'] = DataDefinition::create('float')
      ->setLabel(t('Latitude'));

    $properties['lng'] = DataDefinition::create('float')
      ->setLabel(t('Longitude'));

    $properties['data'] = MapDataDefinition::create()
      ->setLabel(t('Raw payload'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    return [
      'allow_non_unique_address' => TRUE,
      'allow_non_danish_address' => TRUE,
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $element = [];
    $element['allow_non_unique_address'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow non-unique addresses'),
      '#description' => $this->t('Permit storing an address even when the lookup returned multiple matches.'),
      '#default_value' => $this->getSetting('allow_non_unique_address'),
    ];
    $element['allow_non_danish_address'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow non-Danish addresses'),
      '#description' => $this->t('Permit free-form addresses that could not be resolved through Adressevælger.'),
      '#default_value' => $this->getSetting('allow_non_danish_address'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName(): string {
    return 'value';
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($values, $notify = TRUE): void {
    if (is_array($values)) {
      $values += ['data' => []];
      if (isset($values['data']) && is_string($values['data'])) {
        $decoded = @unserialize($values['data'], ['allowed_classes' => FALSE]);
        if ($decoded === FALSE && $values['data'] !== serialize(FALSE)) {
          $decoded = json_decode($values['data'], TRUE) ?? [];
        }
        $values['data'] = is_array($decoded) ? $decoded : [];
      }
    }
    parent::setValue($values, $notify);
  }

  /**
   * {@inheritdoc}
   */
  public function getAddressId(): ?string {
    $value = $this->get('id')->getValue();
    return $value !== '' ? $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getTextValue(): ?string {
    $value = $this->get('value')->getValue();
    return $value !== '' ? $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getStreet(): ?string {
    $value = $this->get('street')->getValue();
    return $value !== '' ? $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getPostalCode(): ?string {
    $value = $this->get('postal_code')->getValue();
    return $value !== '' ? $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getCity(): ?string {
    $value = $this->get('city')->getValue();
    return $value !== '' ? $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getLat(): ?float {
    $value = $this->get('lat')->getValue();
    return $value !== NULL && $value !== '' ? (float) $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getLng(): ?float {
    $value = $this->get('lng')->getValue();
    return $value !== NULL && $value !== '' ? (float) $value : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getData(): array {
    $value = $this->get('data')->getValue();
    return is_array($value) ? $value : [];
  }

}
