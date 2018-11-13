<?php

namespace Drupal\foreningsmentor\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\address_dawa\AddressDawaItemInterface;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation for the 'address_dawa' formatter.
 *
 * @FieldFormatter(
 *   id = "address_dawa_clean",
 *   label = @Translation("Clean Address DAWA"),
 *   field_types = {
 *     "address_dawa",
 *   },
 * )
 */
class CleanAddressDawaFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    /** @var \Drupal\address_dawa\AddressDawaItemInterface $item */
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#prefix' => '<span translate="no">',
        '#suffix' => '</span>',
      ];
      $elements[$delta] += $this->viewElement($item);
    }

    return $elements;
  }

  /**
   * Builds a renderable array for a single dawa address item.
   *
   * @param \Drupal\address_dawa\AddressDawaItemInterface $item
   *   The address.
   *
   * @return array
   *   A renderable array.
   */
  protected function viewElement(AddressDawaItemInterface $item) {
    $value = [
      $item->getData()["adressebetegnelse"],
    ];
    $element = [
      '#type' => 'markup',
      '#markup' => implode('', $value),
    ];
    return $element;
  }
}
