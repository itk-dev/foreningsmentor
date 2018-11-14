<?php

namespace Drupal\foreningsmentor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

/**
 * Class JournalForm.
 */
class JournalForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'foreningsmentor_journal_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
    $currentUser = \Drupal::currentUser();

    $permissiveRole = count(array_intersect(['pm', 'coordinator'], $currentUser->getRoles())) > 0;

    $form['add_journal_entry'] = array(
      '#type' => 'details',
      '#title' => t('Add journal entry'),
      '#weight' => 5,
      '#open' => TRUE,
    );
    $form['add_journal_entry']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
      '#description' => $this->t('The journal entry headline'),
      '#weight' => '0',
    ];
    $form['add_journal_entry']['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('The journal entry'),
      '#description' => $this->t('The journal entry'),
      '#weight' => '1',
    ];
    $form['add_journal_entry']['only_for_coordinators'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show only to coordinators'),
      '#description' => $this->t('Whether or not this entry should be hidden from mentors'),
      '#weight' => '1',
      '#access' => $permissiveRole,
    ];
    $form['add_journal_entry']['submit'] = [
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

    $node = Node::create([
      'type' => 'journal_entry',
      'title' => $form_state->getValue('title'),
      'body' => $form_state->getValue('body'),
      'field_only_for_coordinators' => $form_state->getValue('only_for_coordinators'),
      'field_child' => $parentNode,
    ]);
    $node->save();
  }
}
