<?php

/**
 * @file
 * Post-update hooks for the address_adressevaelger module.
 *
 * Fallback delegation: the migration normally runs from hook_install during
 * the same deploy that adds this module. This post_update covers the edge
 * case where the module was installed earlier without the migration; it
 * delegates to the idempotent function in .install (no-op if already done).
 */

declare(strict_types=1);

/**
 * Run the field_address migration if it hasn't happened yet.
 */
function address_adressevaelger_post_update_migrate_field_address(): string {
  \Drupal::moduleHandler()->loadInclude('address_adressevaelger', 'install');
  address_adressevaelger_migrate_field_address();
  return (string) t('Adressevælger migration applied (no-op if already done).');
}
