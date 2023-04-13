<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\DependentFixtureInterface;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\eck\Entity\EckEntity;

/**
 * Page fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class BrickFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load() {
    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'frontpage_hero',
      'title' => 'Frontpage hero 1',
      'field_title_first_line' => 'Alle har ret',
      'field_title_second_line' => 'til en sjov fritid!',
      'field_text_1' => 'Kom i gang med en sjov og sund fritidsaktivitet',
      'field_blok_1_description' => [
        'value' => <<<'BODY'
ForeningsMentor hjælper børn og deres forældre i gang med en sjovere og mere aktiv fritid i foreninger og klubber.
BODY,
        'format' => 'filtered_html',
      ],
      'field_blok_1_btn' => 'https://example.com/',
      'field_text_2' => 'Bliv frivillig',
      'field_blok_2_description' => [
        'value' => <<<'BODY'
Som frivillig er du med til at give børn og unge fra udsatte boligområder en sund og sjov fritid.

Samtidig får du mulighed for at udvikle dig personligt i rollen som mentor.
BODY,
        'format' => 'filtered_html',
      ],
      'field_blok_2_btn' => 'https://example.com/',
      'field_background_image' =>  ['target_id' => $this->getReference('file:1.jpg')->id()]
    ]);
    $this->addReference('brick:frontpage-hero-1', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'frontpage_hero',
      'title' => 'Frontpage hero 2',
      'field_title_first_line' => 'Frontpage hero 1 subtext',
      'field_title_second_line' => 'Frontpage hero 1 subtext additional',
      'field_text_1' => 'This is a block',
      'field_blok_1_btn' => 'https://example.com/',
      'field_blok_1_description' => [
        'value' => <<<'BODY'
This is some description
BODY,
        'format' => 'filtered_html',
      ],
      'field_text_2' => 'This is another block',
      'field_blok_2_btn' => 'https://example.com/',
      'field_blok_2_description' => [
        'value' => <<<'BODY'
This is some other description
BODY,
        'format' => 'filtered_html',
      ],
      'field_background_image' => ['target_id' => $this->getReference('file:2.jpg')->id()]
    ]);
    $this->addReference('brick:frontpage-hero-2', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'image',
      'title' => 'Image A',
      'field_image' =>  ['target_id' => $this->getReference('file:3.jpg')->id()]
    ]);
    $this->addReference('brick:image-a', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'image',
      'title' => 'Image B',
      'field_image' =>  ['target_id' => $this->getReference('file:4.jpg')->id()]
    ]);
    $this->addReference('brick:image-b', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'efforts_area',
      'title' => 'Hvor kan man være ForeningsMentor?',
      'field_effort' =>  [
        ['target_id' => $this->getReference('referenced_content:effort-area-1')->id()],
        ['target_id' => $this->getReference('referenced_content:effort-area-2')->id()],
        ['target_id' => $this->getReference('referenced_content:effort-area-3')->id()],
        ['target_id' => $this->getReference('referenced_content:effort-area-4')->id()],
        ['target_id' => $this->getReference('referenced_content:effort-area-5')->id()],
      ],
    ]);
    $this->addReference('brick:efforts-area-a', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'efforts_area',
      'title' => 'Effort area B',
      'field_effort' =>  ['target_id' => $this->getReference('referenced_content:effort-area-2')->id()]
    ]);
    $this->addReference('brick:efforts-area-b', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'contacts_area',
      'title' => 'Contact area A',
    ]);
    $this->addReference('brick:contacts-area-a', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'contacts_area',
      'title' => 'Contact area B',
    ]);
    $this->addReference('brick:contacts-area-b', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'teasers',
      'title' => 'Gode grunde til at starte i en forening',
      'field_teasers' =>  [
        ['target_id' => $this->getReference('referenced_content:teaser-1')->id()],
        ['target_id' => $this->getReference('referenced_content:teaser-2')->id()],
        ['target_id' => $this->getReference('referenced_content:teaser-3')->id()],
      ],
    ]);
    $this->addReference('brick:teasers-area-a', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'teasers',
      'title' => 'Teasers area B',
      'field_teasers' =>  ['target_id' => $this->getReference('referenced_content:teaser-2')->id()]
    ]);
    $this->addReference('brick:teasers-area-b', $brick);
    $brick->save();

    $brick = EckEntity::create([
      'entity_type' => 'brick',
      'type' => 'testimonials_area',
      'title' => 'Hvad siger andre om at være ForeningsMentor?',
      'field_testimonial' =>  [
        ['target_id' => $this->getReference('referenced_content:testimonial-1')->id()],
        ['target_id' => $this->getReference('referenced_content:testimonial-2')->id()],
        ['target_id' => $this->getReference('referenced_content:testimonial-3')->id()],
      ],
    ]);
    $this->addReference('brick:testimonials-area-a', $brick);
    $brick->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies() {
    return [
      ImageFixture::class,
      ReferencedContentFixture::class,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['bricks'];
  }

}
