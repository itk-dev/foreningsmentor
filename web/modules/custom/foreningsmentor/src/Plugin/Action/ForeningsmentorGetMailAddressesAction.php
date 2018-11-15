<?php

namespace Drupal\foreningsmentor\Plugin\Action;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * Action description.
 *
 * @Action(
 *   id = "foreningsmentor_get_mail_action",
 *   label = @Translation("Get a list of all mails"),
 *   confirm = TRUE,
 *   type = ""
 * )
 */
class ForeningsmentorGetMailAddressesAction extends ViewsBulkOperationsActionBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $objects) {
    $result = '';

    $last_key = array_search(end($objects), $objects);
    foreach ($objects as $key => $object) {
      $name = $object->get('field_name')->value;

      $result = $result . '"' . ($name ?: $object->getEmail()) . '"' . '<' . $object->getEmail() . '>';
      if ($key != $last_key) {
        $result .= '; ';
      }
    }

    \Drupal::messenger()->addMessage($result);
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    return $this->t('');
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityType() === 'node') {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->status->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }

    // Other entity types may have different
    // access methods and properties.
    return TRUE;
  }

}
