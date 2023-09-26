<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;



/**
 * Area term fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class TermMentorFixture extends TaxonomyTermFixture
{
  /**
   * {@inheritdoc}
   */
  protected static $vocabularyId = 'mentors_end_status';

  /**
   * {@inheritdoc}
   */
  protected static $terms = [
    'A1',
    'A2',
    'A3',
    'A4'
  ];
}
