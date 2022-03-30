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
      '#attributes' => array(
        'class' => 'container mt-5 pt-5',
      ),
    ];
    $form['wrapper']['headline'] = [
      '#type' => 'item',
      '#markup' => t('<h1 class="mt-3 mb-3">Sign up</h1>'),
    ];

    $form['wrapper']['child'] = [
      '#type' => 'container',
      '#attributes' => array(
        'class' => 'border-bottom mb-3 pb-3',
      ),
    ];
    $form['wrapper']['child']['child_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'h4',
      '#value' => $this
        ->t('Child info'),
    ];
    $form['wrapper']['child']['child_name'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your child\'s name'),
    ];
    $form['wrapper']['child']['birth_date'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your child\'s birth date'),
    ];
    $form['wrapper']['child']['sex'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your child\'s sex'),
    ];
    $form['wrapper']['child']['school'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your child\'s school'),
    ];
    $form['wrapper']['child']['grade'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your child\'s grade'),
    ];

    $form['wrapper']['parent'] = [
      '#type' => 'container',
      '#attributes' => array(
        'class' => 'border-bottom mb-3 pb-3',
      ),
    ];
    $form['wrapper']['parent']['parent_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'h4',
      '#value' => $this
        ->t('Parent info'),
    ];
    $form['wrapper']['parent']['parent_name'] = [
      '#type' => 'textfield',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your name '),
    ];
    $form['wrapper']['parent']['phone_number'] = [
      '#type' => 'tel',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your phone number'),
    ];
    $form['wrapper']['parent']['mail'] = [
      '#type' => 'email',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Your e-mail address'),
    ];
    $form['wrapper']['other'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['row', 'no-gutters']],
    ];
    $tids = \Drupal::entityQuery('taxonomy_term')->condition('vid', 'neighborhood')->execute();
    $terms = Term::loadMultiple($tids);

    $areaOptions = [];

    foreach ($terms as $term) {
      $areaOptions[$term->id()] = $term->getName();
    }

    $form['wrapper']['other']['address'] = [
      '#prefix' => '<div class="col-md-12">',
      '#suffix' => '</div>',
      '#type' => 'textarea',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Address'),
    ];
    $form['wrapper']['other']['activity'] = [
      '#prefix' => '<div class="col-md-12">',
      '#suffix' => '</div>',
      '#type' => 'textarea',
      '#required' => true,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Activity'),
    ];
    $form['wrapper']['other']['area'] = [
      '#prefix' => '<div class="col-md-12">',
      '#suffix' => '</div>',
      '#type' => 'radios',
      '#title' => $this->t('Area'),
      '#required' => true,
      '#attributes' => ['class' => ['mb-3']],
      '#options' => $areaOptions,
    ];
    $form['wrapper']['other']['consent_contact'] = [
      '#prefix' => '<div class="mb-3 col-md-6"><h5>' . $this->t('Contact consent') .'</h5><small>I hereby consent that ForeningsMentor International may contact me in order to find a leisure activity for my child, and that ForeningsMentor International may contact me at a later point in time to know whether my child is participating in the activity or whether we need help finding another activity.</small>',
      '#suffix' => '</div>',
      '#description' => '',
      '#type' => 'checkbox',
      '#required' => true,
      '#title' => $this->t('Give contact consent'),
    ];

    $form['wrapper']['other']['consent_data'] = [
      '#prefix' => '<div class="mb-3 col-md-12"><h5>' . $this->t('Data consent') .'</h5>',
      '#suffix' => '</div>',
      '#type' => 'checkbox',
      '#required' => true,
      '#title' => $this->t('I give permission to store and process my data'),
    ];

    $form['wrapper']['actions']['#type'] = 'actions';
    $form['wrapper']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sign up'),
      '#attributes' => ['class' => ['btn', 'btn-primary']],
      '#button_type' => 'primary',
      '#states' => [
        'enabled' => [
          [
            ':input[name="consent_contact"]' => ['checked' => TRUE],
            'or',
            ':input[name="consent_data"]' => ['checked' => TRUE],
          ],
        ]
      ]
    ];

    honeypot_add_form_protection($form, $form_state, ['honeypot', 'time_restriction']);

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
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // @TODO: Send notification mail to coordinator for the given area.
  }

}
