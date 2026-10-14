<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

/**
 * Area term fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class TermActivitytypeFixture extends TaxonomyTermFixture {
  /**
   * {@inheritdoc}
   */
  protected static $vocabularyId = 'activity_type';

  /**
   * {@inheritdoc}
   */
  protected static $terms = [
    'fodbold',
    'hockey',
  ];

}
