<?php

namespace Drupal\address_dawa\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Address constraint.
 *
 * @Constraint(
 *   id = "AddressDawa",
 *   label = @Translation("Address validation", context = "Validation"),
 *   type = { "address_dawa" }
 * )
 */
class AddressDawaConstraint extends Constraint {

  /**
   * Address could not be resolved via the lookup service.
   *
   * @var array
   */
  const ADDRESS_CAN_NOT_BE_FOUND = [
    'error_code' => 1,
    'message' => 'Address can not be found @address.',
  ];

  /**
   * Address is of a different type than configured.
   *
   * @var array
   */
  const ADDRESS_INVALID_TYPE = [
    'error_code' => 3,
    'message' => 'Please provide "@correct_type" address. You have entered "@wrong_type" address.',
  ];

}
