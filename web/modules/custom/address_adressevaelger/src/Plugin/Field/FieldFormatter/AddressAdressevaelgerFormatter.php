<?php

declare(strict_types=1);

namespace Drupal\address_adressevaelger\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'address_adressevaelger' formatter.
 *
 * @FieldFormatter(
 *   id = "address_adressevaelger",
 *   label = @Translation("Adressevælger address"),
 *   field_types = {
 *     "address_adressevaelger",
 *   },
 * )
 */
class AddressAdressevaelgerFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    foreach ($items as $delta => $item) {
      $value = (string) $item->get('value')->getValue();
      if ($value === '') {
        continue;
      }
      $elements[$delta] = [
        '#type' => 'inline_template',
        '#template' => '<p class="dawa-address" translate="no">{{ value }}</p>',
        '#context' => ['value' => $value],
      ];
    }
    return $elements;
  }

}
