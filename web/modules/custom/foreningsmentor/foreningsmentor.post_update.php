<?php

/**
 * Contains data updates for foreningsmentor.
 */

use Drupal\taxonomy\Entity\Term;

/**
 * Adds taxonomy terms for activity content type.
 */
function foreningsmentor_post_update_0001(&$sandbox) {
  // Add activity_type taxonomy.
  $vocab = 'activity_type';

  // Term names to be added.
  $items = [
    'A' => '12 ugers forløb',
    'B' => 'Foreningsmatch + rådgivning fra frivillighedskoordinator',
    'C' => 'Nej',
  ];
  foreach ($items as $key => $value) {
    Term::create([
      'parent' => [],
      'name' => $key,
      'description' => $value,
      'vid' => $vocab,
    ])->save();
  }

  // Add coordinators_end_status taxonomy.
  $vocab = 'coordinators_end_status';

  // Term names to be added.
  $items = [
    'A11' => 'Barnet er fortsat aktiv',
    'A12' => 'Barnet er ikke længere aktiv i foreningen',
    'B11' => 'Barnet er fortsat aktiv',
    'B12' => 'Barnet er ikke længere aktiv i foreningen',
    'A13' => 'Status ukendt'
  ];
  foreach ($items as $key => $value) {
    Term::create([
      'parent' => [],
      'name' => $key,
      'description' => $value,
      'vid' => $vocab,
    ])->save();
  }

  // Add coordinators_end_status taxonomy.
  $vocab = 'mentors_end_status';

  // Term names to be added.
  $items = [
    'A1' => 'Barnet er fortsat aktiv',
    'A2' => 'Barnet er stoppet i foreningen under mentorforløbet',
    'A3' => 'Mentor stopper undervejs',
    'A4' => 'Status ukendt',
  ];
  foreach ($items as $key => $value) {
    Term::create([
      'parent' => [],
      'name' => $key,
      'description' => $value,
      'vid' => $vocab,
    ])->save();
  }
}
