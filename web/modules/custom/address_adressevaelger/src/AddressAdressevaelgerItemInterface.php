<?php

declare(strict_types=1);

namespace Drupal\address_adressevaelger;

use Drupal\Core\Field\FieldItemInterface;

/**
 * Defines the interface for Adressevælger address field items.
 */
interface AddressAdressevaelgerItemInterface extends FieldItemInterface {

  /**
   * Get address UUID (DAR id).
   */
  public function getAddressId(): ?string;

  /**
   * Get address textual representation (full betegnelse).
   */
  public function getTextValue(): ?string;

  /**
   * Get street (vejnavn + husnr).
   */
  public function getStreet(): ?string;

  /**
   * Get postal code.
   */
  public function getPostalCode(): ?string;

  /**
   * Get city.
   */
  public function getCity(): ?string;

  /**
   * Get latitude coordinate.
   */
  public function getLat(): ?float;

  /**
   * Get longitude coordinate.
   */
  public function getLng(): ?float;

  /**
   * Get raw payload from Adressevælger.
   *
   * @return array
   *   Decoded response data.
   */
  public function getData(): array;

}
