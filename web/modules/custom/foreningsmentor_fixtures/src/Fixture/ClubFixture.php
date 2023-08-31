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
class ClubFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface
{
  /**
   * {@inheritdoc}
   */
  public function load() {


    $node = Node::create([
      'type' => 'club',
      'title' => 'club - TEST FORENING',
      'status' => NodeInterface::PUBLISHED,

//      "field_address" => 'Test foreningsvej 2', ERRO DAWA
//      "field_available_activities" => 'nej', Invalid datetime format: 1366 Incorrect integer value: 'nej' for column `db`.`taxonomy_index`.`tid` at row 1
      "field_email" => 'forening@gmail.com',
      "field_homepage" => 'https://www.google.com/',
      "field_phone" => '+45 22 22 22 22',

    ]);
    $this->addReference('club:fixture-1', $node);
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
