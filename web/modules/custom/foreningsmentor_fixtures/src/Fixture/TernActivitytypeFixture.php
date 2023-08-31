<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

/**
 * Area term fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class TernActivitytypeFixture extends TaxonomyTermFixture {
  /**
   * {@inheritdoc}
   */
  protected static $vocabularyId = 'activity_type';

  /**
   * {@inheritdoc}
   */
  protected static $terms = [
   'fodbold',
    'hockey'
  ];

}
