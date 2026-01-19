<?php

namespace Drupal\foreningsmentor\Helper;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\Entity\Node;

/**
 * Helper class for foreningsmentor.
 */
class Helper {
  use StringTranslationTrait;

  public function __construct(
    protected MessengerInterface $messenger,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Show warning if signup matches existing content.
   *
   * @param \Drupal\node\Entity\Node $node
   *   The signup node.
   */
  public function showWarningOnSignupMatch(Node $node): void {
    try {
      $nodeStorage = $this->entityTypeManager->getStorage('node');

      // Get children with matching name and birthday.
      $childMatches = $nodeStorage->getQuery()->accessCheck(FALSE)
        ->condition('type', 'child')
        ->condition('title', $node->field_name?->value ?? '')
        ->condition('field_birthday', $node->field_birthday?->value ?? '')
        ->execute();

      $children = $nodeStorage->loadMultiple($childMatches);

      // Get parents with matching name and email or phone number.
      $parentQuery = $nodeStorage->getQuery();
      $parentQuery->accessCheck(FALSE);

      $orGroup = $parentQuery->orConditionGroup()
        ->condition('field_email', $node->field_email?->value ?? '')
        ->condition('field_phone', $node->field_phone?->value ?? '');

      $parentMatches = $parentQuery
        ->condition('type', 'parent')
        ->condition('title', $node->field_parent_name?->value ?? '')
        ->condition($orGroup)
        ->execute();

      $parents = $nodeStorage->loadMultiple($parentMatches);

      $matches = array_merge($parents, $children);

      foreach ($matches as $match) {
        $this->messenger->addWarning($this->t('Found content very similar to this signup. Check to prevent duplicates: @matchtype - <a href="/node/@nid">@name</a>', [
          '@matchtype' => $match->type->entity->label(),
          '@nid' => $match->id(),
          '@name' => $match->getTitle(),
        ]));
      }
    }
    catch (\Exception $e) {
      $this->messenger->addWarning($this->t('An error occurred while checking for existing content: @error', ['@error' => $e->getMessage()]));
      return;
    }
  }

}
