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
class CourseFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface
{
  /**
   * {@inheritdoc}
   */
  public function load() {


    $node = Node::create([
      'type' => 'course',
      'title' => 'course - TEST FORLØB',
      'status' => NodeInterface::PUBLISHED,

//      'field_address' => 'Foreningsvej 1', FEJL
//      "field_available_activities" => ['test'], FEJL
      "field_email" => ['value' => 'Forening@mail.com'],
      "field_homepage" => ['value' => 'https://www.google.com/'],
      "field_phone" => ['value' => '+45 22 22 22 22']

    ]);
    $this->addReference('course:fixture-1', $node);
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
