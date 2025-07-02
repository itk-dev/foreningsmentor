<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

/**
 * Area term fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class TermActivityFixture extends TaxonomyTermFixture {
  /**
   * {@inheritdoc}
   */
  protected static $vocabularyId = 'activities';

  /**
   * {@inheritdoc}
   */
  protected static $terms = [
    'activity1',
    'activity2',
  ];

}
