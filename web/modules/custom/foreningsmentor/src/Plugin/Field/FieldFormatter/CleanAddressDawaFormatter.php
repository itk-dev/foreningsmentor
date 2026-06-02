<?php

namespace Drupal\foreningsmentor\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\adressevaelger\AddressDawaItemInterface;
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
    /** @var \Drupal\adressevaelger\AddressDawaItemInterface $item */
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
   * Builds a renderable array for a single address item.
   *
   * @param \Drupal\adressevaelger\AddressDawaItemInterface $item
   *   The address.
   *
   * @return array
   *   A renderable array.
   */
  protected function viewElement(AddressDawaItemInterface $item) {
    $data = $item->getData();

    if (isset($data['adressebetegnelse'])) {
      $value = [
        $data['adressebetegnelse'],
      ];
    }
    else {
      $value = $data;
    }

    $element = [
      '#type' => 'markup',
      '#markup' => implode('', $value),
    ];
    return $element;
  }

}
