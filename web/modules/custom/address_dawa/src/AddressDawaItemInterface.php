<?php

namespace Drupal\address_dawa;

use Drupal\Core\Field\FieldItemInterface;

/**
 * Defines the interface for an Adressevælger-backed address field item.
 */
interface AddressDawaItemInterface extends FieldItemInterface {

  /**
   * Get address type.
   *
   * @return string
   *   Address type.
   */
  public function getType();

  /**
   * Get address identifier.
   *
   * @return string
   *   The DAR id_lokalid for selections from Adressevælger, or a
   *   `non_dawa_*` synthetic id for free-text non-Danish entries.
   */
  public function getId();

  /**
   * Get address status.
   *
   * @return string
   *   Address status.
   */
  public function getStatus();

  /**
   * Get address textual representation.
   *
   * @return string
   *   Address text.
   */
  public function getTextValue();

  /**
   * Get address latitude coordinate (WGS84).
   *
   * @return string
   *   Latitude geo-coordinate.
   */
  public function getLat();

  /**
   * Get address longitude coordinate (WGS84).
   *
   * @return string
   *   Longitude geo-coordinate.
   */
  public function getLng();

  /**
   * Get raw address data from the lookup service.
   *
   * @return array
   *   Data.
   */
  public function getData();

}
