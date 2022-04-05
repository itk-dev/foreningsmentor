<?php

namespace Drupal\foreningsmentor\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Implements signup form.
 */
class SignUpForm extends FormBase {

  /**
   * The mail manager.
   *
   * @var MailManagerInterface $mailManager
   */
  protected MailManagerInterface $mailManager;

  /**
   * The language manager.
   *
   * @var LanguageManagerInterface $languageManager
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface $entityTypeManager
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param MailManagerInterface $mailManager
   * @param LanguageManagerInterface $languageManager
   * @param EntityTypeManagerInterface $entityTypeManager
   */
  public function __construct(MailManagerInterface $mailManager, LanguageManagerInterface $languageManager, EntityTypeManagerInterface $entityTypeManager) {
    $this->mailManager = $mailManager;
    $this->languageManager = $languageManager;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.mail'),
      $container->get('language_manager'),
      $container->get('entity_type.manager'),
    );
  }

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
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree('neighborhood', 0, NULL, TRUE);

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
  public function submitForm(array &$form, FormStateInterface $form_state){
    $params['form_values'] = $form_state->getValues();
    $area = $params['form_values']['area'];
    $neighborhoodUsers =  $this->entityTypeManager->getStorage('user')->loadByProperties(['field_neighborhood' => $area]);
    $areaTerm = $this->entityTypeManager->getStorage('taxonomy_term')->load($area);
    // Overwrite $params area id with actual term name for display in mail.
    $params['form_values']['area'] = $areaTerm->getName();
    // Send mail to coordinators assigned to the neighborhood.
    $params['site_name'] = $this->configFactory->get('system.site')->get('name');
    foreach ($neighborhoodUsers as $user) {
      if ($user->hasRole('coordinator')) {
        $this->mailManager->mail('foreningsmentor', 'signup', $user->get('mail')->value, $this->languageManager->getDefaultLanguage()->getName(), $params);
      }
    }

    $this->messenger->addStatus($this->t('Thank you for signing up. You will be contacted by @site_name shortly.', ['@site_name' => $params['site_name']]));
  }
}
