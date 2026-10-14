<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\file\Entity\File;
use Drupal\foreningsmentor_fixtures\Helper\Helper;

/**
 * Page fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class ImageFixture extends AbstractFixture implements FixtureGroupInterface {

  /**
   * The fixtures helper service.
   *
   * @var \Drupal\foreningsmentor_fixtures\Helper\Helper
   */
  protected Helper $helper;

  /**
   * Constructor.
   */
  public function __construct(Helper $helper) {
    $this->helper = $helper;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function load() {
    $imagesFiles = $this->helper->createImagesFromAssets();
    foreach ($imagesFiles as $publicFilePath) {
      $file = File::create([
        'filename' => basename($publicFilePath),
        'uri' => $publicFilePath,
        'status' => 1,
        'uid' => 1,
      ]);
      $file->save();
      $this->addReference('file:' . basename($publicFilePath), $file);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['images'];
  }

}
