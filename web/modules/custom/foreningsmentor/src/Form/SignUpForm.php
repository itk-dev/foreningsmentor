<?php

namespace Drupal\foreningsmentor\Form;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Implements signup form.
 */
final class SignUpForm extends FormBase {

  /**
   * The mail manager.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected MailManagerInterface $mailManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected LanguageManagerInterface $languageManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mailManager
   *   The mail manager service.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
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
  public function getFormId() {
    return 'sign_up_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'container mt-5 pt-5',
      ],
    ];
    $form['wrapper']['headline'] = [
      '#type' => 'item',
      '#markup' => '<h1 class="mt-3 mb-3">' . t('Tilmeld // Signup') . '</h1>',
    ];

    $form['wrapper']['data_text'] = [
      '#type' => 'item',
      '#markup' => '<div class="mt-3 mb-3 small">' . t('Når du tilmelder dit barn, behandler vi de personoplysninger, du indtaster, for at kunne håndtere din tilmelding og kommunikere med dig. Vi behandler dine oplysninger fortroligt og i overensstemmelse med gældende databeskyttelseslovgivning. Du kan læse mere her: <a href="/brug-af-personoplysninger">Brug af personoplysninger</a>. </br>//</br> When you register your child, we process the personal information you enter in order to handle your registration and communicate with you. We process your information confidentially and in accordance with applicable data protection legislation. You can read more here: <a href="/brug-af-personoplysninger">Use of personal data</a>.') . '</div>',
    ];

    $form['wrapper']['child'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'border-bottom mb-3 pb-3',
      ],
    ];
    $form['wrapper']['child']['child_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'h4',
      '#value' => $this
        ->t('Information om barnet // Child information'),
    ];
    $form['wrapper']['child']['child_name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Barnets navn // Child's name"),
    ];
    $form['wrapper']['child']['birth_date'] = [
      '#type' => 'date',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Barnets fødselsdato // Child's birth date"),
    ];
    $form['wrapper']['child']['sex'] = [
      '#type' => 'select',
      '#options' => ['Dreng' => t('Boy'), 'Pige' => t('Girl'), 'Andet' => t('Other')],
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Barnets køn // Child's sex"),
    ];
    $form['wrapper']['child']['school'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Barnets skole/børnehave // Child's school/childcare"),
    ];
    $form['wrapper']['child']['grade'] = [
      '#type' => 'select',
      '#options' => [
        '0. klasse' => '0. klasse',
        '1. klasse' => '1. klasse',
        '2. klasse' => '2. klasse',
        '3. klasse' => '3. klasse',
        '4. klasse' => '4. klasse',
        '5. klasse' => '5. klasse',
        '6. klasse' => '6. klasse',
        '7. klasse' => '7. klasse',
        '8. klasse' => '8. klasse',
        '9. klasse' => '9. klasse',
        '10. klasse' => '10. klasse',
        'Andet' => 'Andet',
      ],
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Barnets klassetrin // Child's grade"),
    ];

    $form['wrapper']['referer_check'] = [
      '#prefix' => '<div class="border-bottom mb-3 pb-3">',
      '#suffix' => '</div>',
      '#type' => 'checkbox',
      '#title' => $this->t('Jeg henviser et barn på vegne af en anden. // I am referring a child on behalf of someone else.'),
      '#description' => $this->t('<small>Jeg bekræfter hermed, at jeg har talt med forældrene/værge om at videregive barnets oplysninger til ForeningsMentor i Aarhus Kommune og til det fritidstilbud, hvor barnet skal begynde til en fritidsaktivitet. // I hereby confirm that I have spoken with the parents/guardian about sharing the child’s information with ForeningsMentor in Aarhus Kommune and with the leisure activity provider where the child will begin a leisure activity. </small>'),
    ];

    $form['wrapper']['referer'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'border-bottom mb-3 pb-3',
      ],
      '#states' => [
        'visible' => [
          ':input[name="referer_check"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['wrapper']['referer']['referer_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Referer name'),
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#states' => [
        'required' => [
          ':input[name="referer_check"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['wrapper']['referer']['referer_mail'] = [
      '#type' => 'email',
      '#title' => $this->t('Referer e-mail'),
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#states' => [
        'required' => [
          ':input[name="referer_check"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['wrapper']['referer']['referer_phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Referer phone number'),
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#states' => [
        'required' => [
          ':input[name="referer_check"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['wrapper']['referer']['referer_work_place'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Referer work place'),
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#states' => [
        'required' => [
          ':input[name="referer_check"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['wrapper']['parent'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'border-bottom mb-3 pb-3',
      ],
    ];
    $form['wrapper']['parent']['parent_label'] = [
      '#type' => 'html_tag',
      '#tag' => 'h4',
      '#value' => $this
        ->t('Information om forælder // Parent info'),
    ];
    $form['wrapper']['parent']['parent_name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Forælders navn // Parent name'),
    ];
    $form['wrapper']['parent']['phone_number'] = [
      '#type' => 'tel',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Forælders telefonnummer // Parent phone number'),
    ];
    $form['wrapper']['parent']['mail'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Forælders e-mailadresse // Parent e-mail address'),
    ];

    $form['wrapper']['other'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['row', 'no-gutters']],
    ];

    $form['wrapper']['other']['address'] = [
      '#prefix' => '<div class="col-md-12"><p class="dawa-address" translate="no">',
      '#suffix' => '</p></div>',
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Adresse // Address'),
    ];
    $form['wrapper']['other']['postal_code'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Postnr // Postal code'),
    ];
    $form['wrapper']['other']['activity'] = [
      '#prefix' => '<div class="col-md-12">',
      '#suffix' => '</div>',
      '#type' => 'textarea',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Ønsker til aktivitet // Activity wishes'),
    ];
    $form['wrapper']['other']['area'] = [
      '#prefix' => '<div class="col-md-12">',
      '#suffix' => '</div>',
      '#type' => 'radios',
      '#title' => $this->t('Område // Area'),
      '#required' => TRUE,
      '#attributes' => ['class' => ['mb-3 border-bottom pb-3']],
      '#options' => $this->getAreaOptions(),
      '#description' => $this->t('<small>Vælg det område, hvor du bor. Hvis du er i tvivl, vælg bydækkende. // Select the area where you live. If in doubt select city covering.</small>'),
    ];

    $form['wrapper']['actions']['#type'] = 'actions';
    $form['wrapper']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sign up'),
      '#attributes' => ['class' => ['btn', 'btn-primary']],
      '#button_type' => 'primary'
    ];

    \Drupal::service('honeypot')->addFormProtection($form, $form_state, ['honeypot', 'time_restriction']);

    return $form;
  }

  /**
   * Get filtered area options from neighborhood taxonomy.
   *
   * @return array
   *   Array of area options keyed by term ID.
   */
  private function getAreaOptions() {
    try {
      $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree('neighborhood', 0, NULL, TRUE);
    }
    catch (InvalidPluginDefinitionException | PluginNotFoundException $e) {
      return [];
    }

    $areaOptions = [];

    foreach ($terms as $term) {
      // Check if the term has the field_hide field set to true.
      if ($term->hasField('field_hide') && !$term->get('field_hide')->value) {
        $areaOptions[$term->id()] = $term->getName();
      }
    }

    return $areaOptions;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('phone_number')) < 8) {
      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $params['form_values'] = $form_state->getValues();
    $area = $params['form_values']['area'];
    $neighborhoodUsers = $this->entityTypeManager->getStorage('user')->loadByProperties(['field_neighborhood' => $area]);
    $areaTerm = $this->entityTypeManager->getStorage('taxonomy_term')->load($area);
    // Overwrite $params area id with actual term name for display in mail.
    $params['form_values']['area'] = $areaTerm->getName();
    $params['area_id'] = $areaTerm->id();
    // Send mail to coordinators assigned to the neighborhood.
    $params['site_name'] = $this->config('system.site')->get('name');
    $params['site_url'] = Url::fromUserInput('/', ['absolute' => 'true'])->toString();

    $node = [
      'title' => $params['form_values']['child_name'],
      'type' => 'signup',
      'field_name' => $params['form_values']['child_name'],
      'field_birthday' => $params['form_values']['birth_date'],
      'field_sex' => $params['form_values']['sex'],
      'field_school' => $params['form_values']['school'],
      'field_shool_class' => $params['form_values']['grade'],
      'field_parent_name' => $params['form_values']['parent_name'],
      'field_phone' => $params['form_values']['phone_number'],
      'field_email' => $params['form_values']['mail'],
      'field_address' => [
        'id' => '0',
        'value' => $params['form_values']['address'],
        'type' => 'adresse',
        'data' => [],
        'status' => 0,
        'lat' => 0,
        'lng' => 0,
      ],
      'field_postal_code' => $params['form_values']['postal_code'],
      'field_referer' => $params['form_values']['referer_name'],
      'field_referer_phone' => $params['form_values']['referer_phone'],
      'field_referer_email' => $params['form_values']['referer_mail'],
      'field_referer_work_place' => $params['form_values']['referer_work_place'],
      'field_activity_wishes' => $params['form_values']['activity'],
      'field_neighborhood' => $area,
      'field_archived' => FALSE,
    ];

    $node = $this->entityTypeManager->getStorage('node')->create($node);
    $node->save();

    $params['node_id'] = $node->id();

    // Notify coordinators assigned to the neighborhood.
    foreach ($neighborhoodUsers as $user) {
      if ($user->hasRole('coordinator')) {
        $this->mailManager->mail('foreningsmentor', 'foreningsmentor_signup', $user->get('mail')->value, $this->languageManager->getDefaultLanguage()->getName(), $params);
      }
    }

    if ($params['form_values']['referer_mail']) {
      // Notify referer.
      $this->mailManager->mail('foreningsmentor', 'foreningsmentor_referer_signup', $params['form_values']['referer_mail'], $this->languageManager->getDefaultLanguage()->getName(), $params);
    }
    else {
      // Notify parent.
      $this->mailManager->mail('foreningsmentor', 'foreningsmentor_parent_signup', $params['form_values']['mail'], $this->languageManager->getDefaultLanguage()->getName(), $params);
    }

    $this->messenger()->addStatus($this->t('Thank you for signing up. You will be contacted by @site_name shortly.', ['@site_name' => $params['site_name']]));
  }

}
