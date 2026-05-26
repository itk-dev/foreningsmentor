<?php

declare(strict_types=1);

namespace Drupal\address_adressevaelger\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Adressevælger field constraint.
 *
 * @Constraint(
 *   id = "AddressAdressevaelger",
 *   label = @Translation("Adressevælger address validation", context = "Validation"),
 *   type = { "address_adressevaelger" }
 * )
 */
class AddressAdressevaelgerConstraint extends Constraint {

  /**
   * Address could not be resolved.
   */
  public const ADDRESS_CAN_NOT_BE_FOUND = [
    'error_code' => 'address_not_found',
    'message' => 'Address can not be found: @address.',
  ];

  /**
   * Address resolved to multiple locations.
   */
  public const ADDRESS_MULTIPLE_LOCATION = [
    'error_code' => 'address_multiple',
    'message' => 'Address resolved to multiple locations: @address.',
  ];

  /**
   * Free-form (non-resolved) address marker stored in the `id` column.
   */
  public const NON_RESOLVED_ID_PREFIX = 'non_resolved_';

}
