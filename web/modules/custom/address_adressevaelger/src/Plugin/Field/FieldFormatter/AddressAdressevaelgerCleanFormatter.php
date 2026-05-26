<?php

declare(strict_types=1);

namespace Drupal\address_adressevaelger\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Clean Adressevælger address formatter — outputs the address text only.
 *
 * @FieldFormatter(
 *   id = "address_adressevaelger_clean",
 *   label = @Translation("Clean Adressevælger address"),
 *   field_types = {
 *     "address_adressevaelger",
 *   },
 * )
 */
class AddressAdressevaelgerCleanFormatter extends FormatterBase {

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
        '#template' => '<span translate="no">{{ value }}</span>',
        '#context' => ['value' => $value],
      ];
    }
    return $elements;
  }

}
