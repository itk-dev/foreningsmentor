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
      'title' => 'child - JOHN DOE',
      'status' => NodeInterface::PUBLISHED,

      'field_anonymized' => '', // Hvad er det er det
      'field_neighborhood' => ['target_id' => $this->getReference('neighborhood:Hasle')->id()],
      'field_activity_wishes' => ['value' => 'Dette er en test'],
      'field_birthday' => ['value' => date("Y-m-d", 1283166912)],
      'field_sex' => ['value' => 'Mand'],
      'field_school' => ['value' => 'Old School'],
      'field_shool_class' => ['value' => '8.klasse'],
      'field_date_registered' => ['value' => date("Y-m-d", 1598786112)],
      'field_family_subsidy' => false,
      'field_comments' => ['value' => 'Dette er en kommentar. HURRA !!!'],
      'field_referer' => ['value' => 'MENTOR HEST'],
      'field_referer_phone' => [ 'value' => '+ 45 22 22 22 22'],
      'field_referer_email' => ['value' => 'MENTOR@HEST.dk'],
      'field_parents' => ['target_id' => $this->getReference('parent:fixture-1')->id()]


    ]);
    $this->addReference('child:fixture-1', $node);
    $node->save();

    $node = Node::create([
      'type' => 'child',
      'title' => 'child - HEST DOE',
      'status' => NodeInterface::PUBLISHED,
      'field_anonymized' => '', // Hvad er det er det
      'field_neighborhood' => ['target_id' => $this->getReference('neighborhood:Hasle')->id()],
      'field_activity_wishes' => ['value' => 'Dette er en test'],
      'field_birthday' => ['value' => date("Y-m-d", 1283122912)],
      'field_sex' => ['value' => 'Mand'],
      'field_school' => ['value' => 'Old School'],
      'field_shool_class' => ['value' => '8.klasse'],
      'field_date_registered' => ['value' => date("Y-m-d", 1698786112)],
      'field_family_subsidy' => false,
      'field_comments' => ['value' => 'Dette er en kommentar. HURRA HURRA!!!'],
      'field_referer' => ['value' => 'MENTOR HEST'],
      'field_referer_phone' => [ 'value' => '+ 45 22 22 22 22'],
      'field_referer_email' => ['value' => 'MENTOR@HEST.dk'],
      'field_parents' => ['target_id' => $this->getReference('parent:fixture-1')->id()],
      'field_siblings' => ['target_id' => $this->getReference('child:fixture-1')->id()],
      'field_courses' => [
        ['title' => 'Et fodboldforløb'],
        ['field_activity_type' => ['target_id' => $this->getReference('activity_type:fodbold')->id()]],
        ['field_mentor' => ['target_id' => $this->getReference('user:mentor')->id()]],
        ['field_date_start' =>['value' => date("Y-m-d", 1283122912)]],
        ['field_date_end' =>['value' => date("Y-m-d", 1283122912)]],

      ],

    ]);
    $this->addReference('child:fixture-2', $node);
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
