<?php

namespace Drupal\foreningsmentor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Implements signup form.
 */
class SignUpForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'sign_up_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['wrapper'] = [
      '#type' => 'container',
    ];
    $form['wrapper']['headline'] = [
      '#type' => 'item',
      '#markup' => t('<h2 class="mt-3 mb-3">Sign up</h2>'),
    ];
    $form['wrapper']['parent_name'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your name'),
    ];
    $form['wrapper']['child_name'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your child\'s name'),
    ];
    $form['wrapper']['phone_number'] = [
      '#type' => 'tel',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your phone number'),
    ];
    $form['wrapper']['mail'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your e-mail address'),
    ];

    $tids = \Drupal::entityQuery('taxonomy_term')->condition('vid', 'neighborhood')->execute();
    $terms = Term::loadMultiple($tids);

    $areaOptions = [];

    foreach ($terms as $term) {
      $areaOptions[$term->id()] = $term->getName();
    }

    $form['area'] = [
      '#type' => 'radios',
      '#title' => $this->t('Area'),
      '#required' => true,
      '#attributes' => ['class' => ['mb-3']],
      '#options' => $areaOptions,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sign up'),
      '#attributes' => ['class' => ['btn', 'btn-primary']],
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    if (strlen($form_state->getValue('phone_number')) < 8) {
      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
    }
    $email = $form_state->getValue('email');
    if ($email !== '' && !\Drupal::service('email.validator')->isValid($email)) {
      $form_state->setErrorByName('email', $this->t('The email address %mail is not valid.', ['%mail' => $email,]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // @TODO: Send notification mail to coordinator for the given area.
  }

}
