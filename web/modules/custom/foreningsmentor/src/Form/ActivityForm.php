<?php

namespace Drupal\foreningsmentor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

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
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $form['add_activity'] = array(
      '#type' => 'details',
      '#title' => t('Add activity'),
      '#weight' => 5,
      '#open' => FALSE,
    );
    $form['add_activity']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
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
    $form['add_activity']['field_club'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Club'),
      '#required' => TRUE,
      '#description' => $this->t('The club where the activity is at'),
      '#target_type' => 'node',
      '#selection_settings' => [
        'target_bundles' => ['club'],
      ],
    ];
    $form['add_activity']['field_activity'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Activity'),
      '#required' => TRUE,
      '#description' => $this->t('The activity'),
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => [
        'target_bundles' => ['activities'],
      ],
      '#autocreate' => [
          'bundle' => 'activities',
      ],
    ];
    $form['add_activity']['submit'] = [
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
    if (isset($parentNode) && $parentNode->getType() != 'child') {
      return;
    }

    $node = Node::create([
      'type' => 'activity',
      'title' => $form_state->getValue('title'),
      'field_child' => $parentNode,
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
