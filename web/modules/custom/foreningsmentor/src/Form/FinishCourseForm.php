<?php

namespace Drupal\foreningsmentor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;

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
  public function buildForm(
    array $form,
    FormStateInterface $form_state,
    $node = NULL
  ) {
    $form['finish_course'] = [
      '#type' => 'details',
      '#title' => t('Finish course'),
      '#weight' => 5,
      '#open' => FALSE,
    ];

    // Extract options for mentor end status.
    $vid = 'mentors_end_status';
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);
    $options = [];
    foreach ($terms as $term) {
      $options[$term->tid] = $term->name;
    }

    $form['finish_course']['date_end'] = [
      '#title' => t('Finish date'),
      '#type' => 'date',
      '#description' => t('The date for the end of the course.'),
      '#required' => TRUE,
      '#weight' => '0',
    ];
    $form['finish_course']['mentor_end_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Mentor end status'),
      '#description' => $this->t('The mentor\'s end status for the course.'),
      '#weight' => '1',
      '#required' => TRUE,
      '#options' => $options,
    ];
    $form['finish_course']['mentor_end_status_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Mentor end status description'),
      '#description' => $this->t('The reasons for the given end status.'),
      '#required' => TRUE,
      '#weight' => '2',
    ];

    $form['finish_course']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => '3',
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

    $date_end = $form_state->getValue('date_end');
    $mentor_end_status = $form_state->getValue('mentor_end_status');
    $mentor_end_status_text = $form_state->getValue('mentor_end_status_text');

    $parentNode->set('field_date_end', $date_end);
    $parentNode->set('field_mentors_end_status', $mentor_end_status);
    $parentNode->set('field_mentors_end_status_text', $mentor_end_status_text);
    $parentNode->set('field_finished', TRUE);

    // If mentor end status == A3 remove the mentor from the course, to make the
    // child return to the waiting list.
    $mentor_status_term = Term::load($mentor_end_status);
    $term_name = $mentor_status_term->getName();
    if ($term_name == 'A3') {
      $mentor_id = $parentNode->get('field_mentor')->target_id;
      if (isset($mentor_id)) {
        $mentor = User::load($mentor_id);

        $journal_entry = Node::create([
          'type' => 'journal_entry',
          'title' => $this->t('System message: Mentor removed.'),
          'body' => $this->t('Mentor @name removed from the course due to A3 status.',
            ['@name' => $mentor->getDisplayName()]),
          'field_only_for_coordinators' => TRUE,
        ]);

        $journal_entry->save();

        $parentNode->get('field_diary')->appendItem($journal_entry);
        $parentNode->set('field_mentor', []);
      }
    }

    $parentNode->save();
  }
}
