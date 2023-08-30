<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\DependentFixtureInterface;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Page fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class ChildFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load() {


    $node = Node::create([
      'type' => 'child',
      'title' => 'John Doe',
      'status' => NodeInterface::PUBLISHED,

      'field_anonymized' => '', // Hvad er det er det
//      'field_neighborhood' => 'Indsatsområde ?', Virker ikke
      'field_activity_wishes' => "Fodbold og Hockey",
      'field_birthday' => date("Y-m-d", 1283166912),
      'field_sex' => 'Mand',
      'field_school' => 'Old School',
      'field_shool_class' => '8',
      'field_date_registered' => date("Y-m-d", 1598786112),
      'field_family_subsidy' => false,
      'field_comments' => 'Der er ikke nogen kommentar',
      'field_referer' => 'Jane Doe',
      'field_referer_phone' => '+ 45 22 22 22 22',
      'field_referer_email' => 'John@Doe.dk',
//      'field_parents' => 'Johns Does DAD', Virker ikke
//      'field_siblings' => 'Albert', Virker ikke
//      'field_courses' => 'A great course' GIVER FEJl

    ]);
    $this->addReference('child:mmmmm', $node);
    $node->save();

  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies() {
    return [
      BrickFixture::class,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['nodes'];
  }

}
