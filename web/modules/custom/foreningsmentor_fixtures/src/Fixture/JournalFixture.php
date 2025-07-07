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
class JournalFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load(): void {
    $node = Node::create([
      'type' => 'journal_entry',
      'title' => 'course - Journal Overskrift',
      'status' => NodeInterface::PUBLISHED,
      'body' => 'Dette er en Journal, og her står der en masse spændene',
      'field_only_for_coordinators' => FALSE,
    ]);
    $this->addReference('journal_entry:fixture-1', $node);
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
