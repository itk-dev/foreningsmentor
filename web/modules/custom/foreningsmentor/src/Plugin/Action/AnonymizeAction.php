<?php

namespace Drupal\foreningsmentor\Plugin\Action;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;

/**
 * Anonymize entity.
 *
 * @Action(
 *   id = "foreningsmentor_anonymize",
 *   label = @Translation("Anonymize entity"),
 *   confirm = TRUE,
 *   type = ""
 * )
 */
class AnonymizeAction extends ViewsBulkOperationsActionBase {
  use StringTranslationTrait;

  private function anonymizeUser(User $entity) {
    $entity->set('field_address', NULL);
    $entity->set('field_active', FALSE);
    $entity->set('field_end_interview', '-');
    $entity->set('field_date_child_cert', NULL);
    $entity->set('field_own_mobile', NULL);
    $entity->set('field_start_interview', '-');
    $entity->set('field_neighborhood', NULL);
    $entity->set('field_comment', '-');
    $entity->set('field_name', uniqid());
    $entity->set('field_public_description', '');
    $entity->set('field_profile_image', NULL);

    $entity->setPassword(uniqid() . uniqid());
    $entity->setUsername(uniqid());
    $entity->setEmail(NULL);

    $entity->set('status', 0);
    $entity->set('field_anonymized', TRUE);

    $entity->save();
  }

  private function anonymizeJournalEntry($entity) {
    $entity->setTitle(uniqid());
    $entity->set('body', '-');
    $entity->set('status', 0);
    $entity->save();
  }

  private function anonymizeCourse($entity) {
    $entity->setTitle(uniqid());
    $entity->set('field_course_type_text', '-');
    $entity->set('field_field_coor_end_status_text', '-');
    $entity->set('field_mentors_end_status_text', '-');
    $entity->set('field_anonymized', TRUE);
    $entity->set('status', 0);

    $journal_entries = $entity->get('field_diary');
    foreach ($journal_entries as $journal_entry) {
      $target_id = $journal_entry->target_id;

      $entry = Node::load($target_id);
      $this->anonymizeJournalEntry($entry);
    }

    $entity->save();
  }

  private function anonymizeChild($entity) {
    $entity->setTitle(uniqid());
    $entity->set('field_activity_wishes', '-');
    $entity->set('field_comments', '-');
    $entity->set('field_birthday', '0');
    $entity->set('field_referer', '-');
    $entity->set('field_referer_email', '');
    $entity->set('field_referer_phone', '');
    $entity->set('field_school', '-');
    $entity->set('field_shool_class', '-');
    $entity->set('field_sex', '-');
    $entity->set('field_family_subsidy', NULL);
    $entity->set('field_parents', []);
    $entity->set('field_siblings', []);

    $courses = $entity->get('field_courses');

    foreach ($courses as $course) {
      $target_id = $course->target_id;

      $course = Node::load($target_id);
      $this->anonymizeCourse($course);
    }

    $entity->set('field_anonymized', TRUE);
    $entity->set('status', FALSE);
    $entity->save();
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    if ($entity instanceof User) {
      $this->anonymizeUser($entity);
    }
    else if ($entity instanceof Node && $entity->getType() == 'child') {
      $this->anonymizeChild($entity);
    }
    else if ($entity instanceof Node && $entity->getType() == 'course') {
      $this->anonymizeCourse($entity);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if ($object->getEntityType() === 'node') {
      $access = $object->access('update', $account, TRUE)
        ->andIf($object->status->access('edit', $account, TRUE));
      return $return_as_object ? $access : $access->isAllowed();
    }

    return TRUE;
  }

}
