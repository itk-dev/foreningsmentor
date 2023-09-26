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
class ParentFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface
{
  /**
   * {@inheritdoc}
   */
  public function load() {


    $node = Node::create([
      'type' => 'parent',
      'title' => 'parent - JANE DOE',
      'status' => NodeInterface::PUBLISHED,
      "field_address" => ['value' => 'testvej 1', 'type' => '', 'id' => 's', 'status' => 1, 'lat' => '2', 'lng' => '2', 'data' => ['adressebetegnelse' => 'testvej 1'] ],
      "field_email" => 'parent@test.dk ',
      "field_phone" => '+ 45 22 22 22 22',

    ]);


    $this->addReference('parent:fixture-1', $node);
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
