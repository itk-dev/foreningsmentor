<?php

namespace Drupal\address_dawa\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates an Adressevælger-backed address field item.
 */
class AddressDawaConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    $field_value = $value->getValue();
    if (($field_value['type'] ?? NULL) === AddressDawaConstraint::ADDRESS_CAN_NOT_BE_FOUND['error_code']) {
      $this->context
        ->buildViolation(AddressDawaConstraint::ADDRESS_CAN_NOT_BE_FOUND['message'])
        ->atPath('value')
        ->setParameter('@address', $value->getTextValue())
        ->addViolation();
      return;
    }

    $address_type = $value->getFieldDefinition()->getSetting('address_type');
    if (!empty($field_value['type']) && $address_type !== $field_value['type']) {
      $this->context
        ->buildViolation(AddressDawaConstraint::ADDRESS_INVALID_TYPE['message'])
        ->atPath('value')
        ->setParameters([
          '@correct_type' => ucwords($address_type),
          '@wrong_type' => ucwords($field_value['type']),
        ])
        ->addViolation();
    }
  }

}
