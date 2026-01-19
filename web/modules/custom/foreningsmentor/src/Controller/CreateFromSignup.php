<?php

namespace Drupal\foreningsmentor\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for creating something from a signup node.
 */
class CreateFromSignup extends ControllerBase {

  /**
   * Create something from a signup node.
   *
   * @param NodeInterface $node
   *   The signup node.
   *
   * @return RedirectResponse
   *   The redirect response.
   */
  public function add(NodeInterface $node) {
    $signup = $node;

    $url = Url::fromRoute('system.admin_content')->toString();
    try {
      $nodeStorage = $this->entityTypeManager()->getStorage('node');

      // Use exising parent entity or create a new one.
      if ($signup->field_existing_parent?->target_id) {
        $parent = $signup->field_existing_parent->entity;
      } else {
        $parent = $nodeStorage->create([
          'title' => $signup->field_parent_name->value,
          'type' => 'parent',
          'field_email' => $signup->field_email->value,
          'field_phone' => $signup->field_phone->value,
          'field_address' => $signup->field_address->getValue()[0],
        ]);

        $parent->save();
      }

      // Create the child entity.
      $newChild = $nodeStorage->create([
        'title' => $signup->field_name->value,
        'type' => 'child',
        'field_birthday' => $signup->field_birthday->value,
        'field_sex' => $signup->field_sex->value,
        'field_school' => $signup->field_school->value,
        'field_shool_class' => $signup->field_shool_class->value,
        'field_parents' => $parent,
        'field_referer' => $signup->field_referer->value,
        'field_referer_phone' => $signup->field_referer_phone->value,
        'field_referer_email' => $signup->field_referer_email->value,
        'field_activity_wishes' => $signup->field_activity_wishes->value,
        'field_neighborhood' => $signup->field_neighborhood->entity,
      ]);

      $newChild->save();

      // Mark signup as archived.
      $signup->field_archived = TRUE;
      $signup->save();

      $this->messenger()->addStatus($this->t('Added @title', [
        '@title' => $signup->label(),
      ]));
    }
    catch (\Exception $e) {
      $this->messenger()->addError($this->t('Error creating child from signup: @error', [
        '@error' => $e->getMessage(),
      ]));
    }

    return new RedirectResponse($url);
  }

}
