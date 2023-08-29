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
class PageFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load() {
    $node = Node::create([
      'type' => 'page',
      'title' => 'The first page',
      'status' => NodeInterface::PUBLISHED,
      'field_content_block' => [
        ['target_id' => $this->getReference('brick:frontpage-hero-1')->id()],
        ['target_id' => $this->getReference('brick:teasers-area-a')->id()],
        ['target_id' => $this->getReference('brick:efforts-area-a')->id()],
        ['target_id' => $this->getReference('brick:testimonials-area-a')->id()],
      ],
    ]);
    $this->addReference('page:the-first-page', $node);
    $node->save();

    $node = Node::create([
      'type' => 'page',
      'title' => 'The second page',
      'status' => NodeInterface::PUBLISHED,
      'field_content_block' => [
        ['target_id' => $this->getReference('brick:frontpage-hero-2')->id()],
        ['target_id' => $this->getReference('brick:teasers-area-b')->id()],
        ['target_id' => $this->getReference('brick:efforts-area-b')->id()],

      ],
    ]);
    $this->addReference('page:the-second-page', $node);
    $node->save();

    $node = Node::create([
      'type' => 'page',
      'title' => 'The third page',
      'status' => NodeInterface::PUBLISHED,
      'field_content_block' => [
        ['target_id' => $this->getReference('brick:partners_area-a')->id()],


      ],
    ]);
    $this->addReference('page:the-third-page', $node);
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
