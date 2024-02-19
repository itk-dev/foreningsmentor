<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

/**
 * Area term fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class TermNeighborhoodFixture extends TaxonomyTermFixture {
  /**
   * {@inheritdoc}
   */
  protected static $vocabularyId = 'neighborhood';

  /**
   * {@inheritdoc}
   */
  protected static $terms = [
    'Hele kommunen',
    'Åby',
    'Åbyhøj',
    'Beder - Malling',
    'Brabrand - Gellerup',
    'Harlev - Framlev',
    'Hårup - Mejlby',
    'Hasle',
    'Hasselager - Kolt',
    'Hjortshøj',
    'Holme - Højbjerg - Skåde',
    'Lisbjerg',
    'Lystrup-Elsted-Nye',
    'Mårslet',
    'Midtbyen',
    'Sabro - Borum',
    'Skejby-Christiansbjerg',
    'Skæring - Egå',
    'Skødstrup - Løgten',
    'Solbjerg',
    'Stavtrup - Ormslev',
    'Tilst - Brabrand Nord',
    'Tranbjerg',
    'Trige - Spørring',
    'Vejlby-Risskov',
    'Viby',
  ];

}
