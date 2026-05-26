<?php

declare(strict_types=1);

namespace Drupal\address_adressevaelger\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates Adressevælger address field items.
 */
class AddressAdressevaelgerConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint): void {
    if ($value->isEmpty()) {
      return;
    }

    $field_definition = $value->getFieldDefinition();
    $allow_non_danish = (bool) $field_definition->getSetting('allow_non_danish_address');

    $id = $value->get('id')->getValue();
    if (!$allow_non_danish && (empty($id) || str_starts_with((string) $id, AddressAdressevaelgerConstraint::NON_RESOLVED_ID_PREFIX))) {
      $this->context
        ->buildViolation(AddressAdressevaelgerConstraint::ADDRESS_CAN_NOT_BE_FOUND['message'])
        ->atPath('value')
        ->setParameter('@address', (string) $value->get('value')->getValue())
        ->addViolation();
    }
  }

}
