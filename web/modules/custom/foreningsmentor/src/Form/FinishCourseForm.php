<?php

namespace Drupal\foreningsmentor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Class JournalForm.
 */
class FinishCourseForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'foreningsmentor_finish_course_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $form['finish_course'] = array(
      '#type' => 'details',
      '#title' => t('Finish course'),
      '#weight' => 5,
      '#open' => FALSE,
    );

    $vid = 'mentors_end_status';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

    $options = [];
    foreach ($terms as $term) {
      $options[$term->tid] = $term->name;
    }

    $form['finish_course']['mentor_end_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Mentor end status'),
      '#description' => $this->t('The mentor\'s end status for the course.'),
      '#weight' => '0',
      '#required' => TRUE,
      '#options' => $options,
    ];
    $form['finish_course']['mentor_end_status_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Mentor end status description'),
      '#description' => $this->t('The reasons for the given end status.'),
      '#required' => TRUE,
      '#weight' => '1',
    ];
    $form['finish_course']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => '2',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $parentNode = $form_state->getBuildInfo()["args"][0];

    // Bail out if parent is not a child.
    if (isset($parentNode) && $parentNode->getType() != 'course') {
      return;
    }

    $mentor_end_status = $form_state->getValue('mentor_end_status');
    $mentor_end_status_text = $form_state->getValue('mentor_end_status_text');

    $parentNode->set('field_mentors_end_status', $mentor_end_status);
    $parentNode->set('field_mentors_end_status_text', $mentor_end_status_text);
    $parentNode->set('field_finished', TRUE);

    $parentNode->save();
  }
}
