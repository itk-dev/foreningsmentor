<?php

namespace Drupal\foreningsmentor_fixtures\Fixture;

use Drupal\content_fixtures\Fixture\AbstractFixture;
use Drupal\content_fixtures\Fixture\DependentFixtureInterface;
use Drupal\content_fixtures\Fixture\FixtureGroupInterface;
use Drupal\eck\Entity\EckEntity;

/**
 * Referenced content fixture.
 *
 * @package Drupal\foreningsmentor_fixtures\Fixture
 */
class ReferencedContentFixture extends AbstractFixture implements DependentFixtureInterface, FixtureGroupInterface {

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'effort_areas',
      'title' => 'Bispehaven',
      'field_effort_area_description' => [
        'value' => <<<'BODY'
Vi har en lokal indsats i Bispehaven, hvor der er særligt fokus på lokalmiljøet og foreningerne i og omkring Bispehaven. Så her kan du som mentor komme tæt på lokalområdet.
BODY,
        'format' => 'filtered_html',
      ],
      'field_effort_area_image' => ['target_id' => $this->getReference('file:p1.jpg')->id()],
    ]);
    $this->addReference('referenced_content:effort-area-1', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'effort_areas',
      'title' => 'Gellerup',
      'field_effort_area_description' => [
        'value' => <<<'BODY'
I Gellerup har vi en samlet indsats. Vi holder til i det boligsociale hus, hvor vi også er tæt på de mange andre indsatser i området. Så hvis du har lyst til at være mentor i Gellerup og spille ind i et område i forandring, så kom og vær med!
BODY,
        'format' => 'filtered_html',
      ],
      'field_effort_area_image' => ['target_id' => $this->getReference('file:p2.jpg')->id()],
    ]);
    $this->addReference('referenced_content:effort-area-2', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'effort_areas',
      'title' => 'Tilst',
      'field_effort_area_description' => [
        'value' => <<<'BODY'
Du kan også blive mentor i vores indsats i Tilst. I Tilst holder vi til på Haurumsvej 31a – Det Boligsociale Hus, hvor vi er tæt på familierne og alle de forskellige aktiviteter i Tilst.
BODY,
        'format' => 'filtered_html',
      ],
      'field_effort_area_image' => ['target_id' => $this->getReference('file:p3.jpg')->id()],
    ]);
    $this->addReference('referenced_content:effort-area-3', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'effort_areas',
      'title' => 'Viby Syd',
      'field_effort_area_description' => [
        'value' => <<<'BODY'
Som noget helt nyt, har vi startet ForeningsMentor i Viby Syd.
Rebekka er den nye koordinator og vi glæder os til at komme i gang!
BODY,
        'format' => 'filtered_html',
      ],
      'field_effort_area_image' => ['target_id' => $this->getReference('file:p4.jpg')->id()],
    ]);
    $this->addReference('referenced_content:effort-area-4', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'effort_areas',
      'title' => 'Bydækkende',
      'field_effort_area_description' => [
        'value' => <<<'BODY'
Vil du gerne være mentor, men gerne i et område, der ligger udenfor vores fire lokale indsatser? Så er 'Bydækkende' stedet for dig. Her hjælper vi de familier, der ikke bor i et af vores fire lokale områder.
BODY,
        'format' => 'filtered_html',
      ],
      'field_effort_area_image' => ['target_id' => $this->getReference('file:p5.jpg')->id()],
    ]);
    $this->addReference('referenced_content:effort-area-5', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'teaser',
      'title' => 'Det er sjovt og sundt at bevæge sig efter skoletid.',
      'field_teaser_text' => [
        'value' => <<<'BODY'
Det er sjovt og sundt at bevæge sig efter skoletid.
BODY,
        'format' => 'filtered_html',
      ],
      'field_teaser_image' => ['target_id' => $this->getReference('file:1.jpg')->id()],
    ]);
    $this->addReference('referenced_content:teaser-1', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'teaser',
      'title' => 'Man kan gå til lige det man har lyst til.',
      'field_teaser_text' => [
        'value' => <<<'BODY'
Man kan gå til lige det man har lyst til.
BODY,
        'format' => 'filtered_html',
      ],
      'field_teaser_image' => ['target_id' => $this->getReference('file:2.jpg')->id()],
    ]);
    $this->addReference('referenced_content:teaser-2', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'teaser',
      'title' => 'Man kan gå til lige det man har lyst til.',
      'field_teaser_text' => [
        'value' => <<<'BODY'
Man kan gå til lige det man har lyst til.
BODY,
        'format' => 'filtered_html',
      ],
      'field_teaser_image' => ['target_id' => $this->getReference('file:3.jpg')->id()],
    ]);
    $this->addReference('referenced_content:teaser-3', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'testimonials',
      'title' => 'Fiona',
      'field_testimonial_text' => [
        'value' => <<<'BODY'
"Når jeg har været ude som foreningsmentor, er det også blevet tydeligt for mig, at når man gerne vil hjælpe andre mennesker, handler det ikke altid om at kunne gøre de helt store ting - men at videregive en viden og en hjælpende hånd, som for mig er indlysende og forholdsvist lige til. Det er nok den ”opdagelse”, der for mig har været helt fantastisk."
BODY,
        'format' => 'filtered_html',
      ],
      'field_testimonial_image' => ['target_id' => $this->getReference('file:p1.jpg')->id()],
      'field_testimonial_author' => '- Fiona, 25 år'
    ]);
    $this->addReference('referenced_content:testimonial-1', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'testimonials',
      'title' => 'Jesper',
      'field_testimonial_text' => [
        'value' => <<<'BODY'
"Et meget givende arbejde, der gælder begge veje. Det var en meget fin oplevelse, hvor jeg fra start oplevede en glæde fra børnene over at kunne deltage i aktivitet sammen med andre på egen alder. 
Det at det arbejder hen mod at de skal blive selvkørende gør projektet overskueligt."
BODY,
        'format' => 'filtered_html',
      ],
      'field_testimonial_image' => ['target_id' => $this->getReference('file:p2.jpg')->id()],
      'field_testimonial_author' => '- Jesper, 54 år'
    ]);
    $this->addReference('referenced_content:testimonial-2', $entity);
    $entity->save();

    $entity = EckEntity::create([
      'entity_type' => 'referenced_content',
      'type' => 'testimonials',
      'title' => 'Alexander',
      'field_testimonial_text' => [
        'value' => <<<'BODY'
"At være en del af en forening, er noget jeg synes alle børn burde. De oplevelser man som barn får, ved at være en del af en forening, er nogle som man kan mindes og glæde sig over i meget lang tid.  Så at kunne hjælpe nogle få i den retning er en fantastisk oplevelse."
BODY,
        'format' => 'filtered_html',
      ],
      'field_testimonial_image' => ['target_id' => $this->getReference('file:p3.jpg')->id()],
      'field_testimonial_author' => '- Alexander, 24 år'
    ]);
    $this->addReference('referenced_content:testimonial-3', $entity);
    $entity->save();
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies() {
    return [
      ImageFixture::class,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getGroups() {
    return ['referenced_content'];
  }

}
