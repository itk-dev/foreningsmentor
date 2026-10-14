<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\user\Entity\User;

/**
 * User fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class UserFixture extends AbstractFixture implements FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load() {
    $user = User::create([
      'name' => 'coordinator',
      'mail' => 'coordinator@example.com',
      'pass' => 'coordinator-password',
      'status' => 1,
      'roles' => [
        'coordinator',
      ],
    ]);
    $user->save();
    $this->setReference('user:coordinator', $user);

    $user = User::create([
      'name' => 'blocked',
      'mail' => 'blocked@example.com',
      'pass' => 'blocked-password',
      'status' => 0,
      'roles' => [
        'author',
      ],
    ]);
    $user->save();
    $this->setReference('user:blocked', $user);

    $user = User::create([
      'name' => 'mentor',
      'mail' => 'mentor@example.com',
      'pass' => 'mentor-password',
      'status' => 1,
      'roles' => [
        'mentor',
      ],
    ]);
    $user->save();
    $this->setReference('user:mentor', $user);
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['user'];
  }

}
