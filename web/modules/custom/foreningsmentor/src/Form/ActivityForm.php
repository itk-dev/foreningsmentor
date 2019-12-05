<?php

namespace Drupal\foreningsmentor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

/**
 * Class ActivityForm.
 */
class ActivityForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'foreningsmentor_activity_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(
    array $form,
    FormStateInterface $form_state,
    $node = NULL
  ) {
    $form['add_activity'] = [
      '#type' => 'details',
      '#title' => t('Add activity'),
      '#weight' => 5,
      '#open' => FALSE,
    ];
    $form['add_activity']['title'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Activity headline'),
      '#description' => $this->t('The activity headline'),
      '#weight' => '0',
    ];
    $form['add_activity']['field_date_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Start date'),
      '#description' => $this->t('The day the activity starts'),
      '#weight' => '0',
    ];
    $form['add_activity']['field_club_contact_person'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Club contact name'),
      '#description' => $this->t('The name of the contact in the club'),
      '#weight' => '0',
    ];
    $form['add_activity']['field_club_contact_person_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Club contact email'),
      '#description' => $this->t('The email of the contact in the club'),
      '#weight' => '0',
    ];
    $form['add_activity']['field_club_contact_person_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Club contact phone number'),
      '#description' => $this->t('The phone number of the contact in the club'),
      '#weight' => '0',
    ];

    $current_user = \Drupal::currentUser();

    if (in_array('coordinator', $current_user->getRoles())
    ) {
      $form['add_activity']['field_mentor'] = [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Mentor'),
        '#description' => $this->t('The mentor for the activity.'),
        '#target_type' => 'user',
        '#selection_settings' => [
          'include_anonymous' => FALSE,
          'filter' => [
            'role' => ['mentor'],
          ],
        ],
      ];
    }

    // Find entity options for 'field_club'.
    $options = [];
    $ids = \Drupal::entityQuery('node')->condition('type','club')->execute();
    foreach ($ids as $id) {
      $node = Node::load($id);
      $options[$id] = $node->getTitle();
    }

    // Sort options by title.
    asort($options);

    $form['add_activity']['field_club'] = [
      '#type' => 'radios',
      '#title' => $this->t('Club'),
      '#options' => $options,
      '#required' => TRUE,
      '#description' => $this->t('The club where the activity is at'),
      '#ajax' => [
        'callback' => [$this, 'changeClub'],
        'event' => 'change',
        'wrapper' => 'field-activity--wrapper',
      ],
    ];

    $form['add_activity']['field_activity'] = [
      '#type' => 'radios',
      '#title' => $this->t('Activity'),
      '#required' => TRUE,
      '#options' => [],
      '#prefix' => '<div id="field-activity--wrapper">',
      '#suffix' => '</div>',
    ];

    // Set activity according to the activities supplied by the club.
    $club = $form_state->getValue('field_club');
    if ($club) {
      // Load activities for club, supply as selectable activities.
      $club = Node::load($club);

      $available_activities = $club->get('field_available_activities');

      $options = [];

      foreach ($available_activities as $activity) {
        $term = Term::load($activity->target_id);

        $options[$activity->target_id] = $term->getName();
      }

      $form['add_activity']['field_activity']['#options'] = $options;
    }

    $form['add_activity']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#weight' => '2',
    ];

    return $form;
  }

  /**
   * Ajax callback from selecting club.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function changeClub(array &$form, FormStateInterface $form_state) : array {
    return $form['add_activity']['field_activity'];
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

    $mentor = $form_state->getValue('field_mentor');

    if (!isset($mentor)) {
      $current_user = \Drupal::currentUser();

      if (in_array('mentor', $current_user->getRoles()) &&
        !in_array('coordinator', $current_user->getRoles())
      ) {
        $mentor = $current_user->id();
      }
    }

    $node = Node::create([
      'type' => 'activity',
      'title' => $form_state->getValue('title'),
      'field_course' => $parentNode,
      'field_mentor' => $mentor,
      'field_date_start' => $form_state->getValue('field_date_start'),
      'field_club' => $form_state->getValue('field_club'),
      'field_activity' => $form_state->getValue('field_activity'),
      'field_club_contact_person' => $form_state->getValue('field_club_contact_person'),
      'field_club_contact_person_email' => $form_state->getValue('field_club_contact_person_email'),
      'field_club_contact_person_phone' => $form_state->getValue('field_club_contact_person_phone'),
    ]);
    $node->save();

    // Add activity to child.
    $parentNode->field_activities[] = $node;
    $parentNode->save();
  }
}
