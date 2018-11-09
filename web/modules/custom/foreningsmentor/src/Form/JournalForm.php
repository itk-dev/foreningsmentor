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
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Headline'),
      '#description' => $this->t('The journal entry headline'),
      '#weight' => '0',
    ];
    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('The journal entry'),
      '#description' => $this->t('The journal entry'),
      '#weight' => '1',
    ];
    $form['submit'] = [
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
      'field_child' => $parentNode,
    ]);
    $node->save();
  }
}
