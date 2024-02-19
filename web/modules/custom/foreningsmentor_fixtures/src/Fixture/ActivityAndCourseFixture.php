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
class ActivityAndCourseFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface
{

  /**
   * {@inheritdoc}
   */
  public function load()
  {
    /* *************   ***** */
    /* NODE ACTIVITY FIXTURE */
    /* **************  ***** */


    $node = Node::create([
      'type' => 'activity',
      'title' => 'activity - Dette er en aktivitet ',
      'status' => NodeInterface::PUBLISHED,
      'field_course' => '',
      'field_mentor'  => ['target_id' => $this->getReference('user:mentor')->id()],
      'field_date_start' => ['value' => date("Y-m-d", 1898786112)],
      'field_club' =>  $this->getReference('club:fixture-1'),
      'field_activity' =>  $this->getReference('activities:activity1'),
      'field_club_contact_person' => 'Club Person',
      'field_club_contact_person_email' => 'club@person.dk',
      'field_club_contact_person_phone' => '22 22 22 22',

    ]);

    $this->addReference('activityform:fixture-1', $node);
    $node->save();


    /* ******************* */
    /* NODE COURSE FIXTURE */
    /* ******************* */

    $nodeCourse = Node::create([
      'type' => 'course',
      'title' => 'course - TEST FORLØB',
      'status' => NodeInterface::PUBLISHED,

      "field_child" => ['target_id' => $this->getReference('child:fixture-1')->id()],
      'field_mentor' => ['target_id' => $this->getReference('user:mentor')->id()],
      "field_date_end" => ['value' => date("Y-m-d", 1698786112)],
      "field_date_start" => ['value' => date("Y-m-d", 1898786112)],
      'field_activity_type' => ['target_id' => $this->getReference('activity_type:fodbold')->id()],
      'field_course_type_text' => ['value' => 'Kan godt lide og spille fodbold'],
      'field_mentors_end_status' => ['target_id' => $this->getReference('mentors_end_status:A1')->id()],
      'field_mentors_end_status_text' => ['value' => 'Det gik godt så vi fortsætter '],
      'field_address' => ['value' => 'testvej 1', 'type' => '', 'id' => 's', 'status' => 1, 'lat' => '2', 'lng' => '2', 'data' => ['adressebetegnelse' => 'forengningsvej 1']],
      'field_activities' =>  ['target_id' => $this->getReference('activityform:fixture-1')->id()],
      "field_email" => ['value' => 'Forening@mail.com'],
      "field_homepage" => ['value' => 'https://www.google.com/'],
      "field_phone" => ['value' => '+45 22 22 22 22'],
      'field_diary' => ['target_id' => $this->getReference('journal_entry:fixture-1')->id()],

    ]);
    $this->addReference('course:fixture-1', $nodeCourse);
    $nodeCourse->save();



  /* UPDATES ACTIVITY NODE */
    $node->field_course = ['target_id' => $this->getReference('course:fixture-1')->id()];
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
