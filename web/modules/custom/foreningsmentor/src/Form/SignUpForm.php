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
      '#markup' => '<div class="mt-3 mb-3 small">' . t('Når du tilmelder dig, behandler vi de personoplysninger, du indtaster, for at kunne håndtere din tilmelding og kommunikere med dig. Vi behandler dine oplysninger fortroligt og i overensstemmelse med gældende databeskyttelseslovgivning. Du kan læse mere her: [link]. </br>//</br> When you register, we process the personal information you enter in order to handle your registration and communicate with you. We process your information confidentially and in accordance with applicable data protection legislation. You can read more here: [link].') . '</div>',
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
      '#options' => ['Dreng' => t('Boy'), 'Pige' => t('Girl')],
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Barnets køn // Child's sex"),
    ];
    $form['wrapper']['child']['school'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Barnets skole // Child's school"),
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
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Barnets klassetrin // Child's grade"),
    ];

    $form['wrapper']['referer_check'] = [
      '#prefix' => '<div class="border-bottom mb-3 pb-3">',
      '#suffix' => '</div>',
      '#type' => 'checkbox',
      '#title' => $this->t('Jeg henviser et barn på vegne af en anden. // I am a referring a child on behalf of someone else.'),
      '#description' => $this->t("<small>Sæt kryds her, hvis du ikke er barnets forælder. Dette vil gøre det muligt for dig at angive henviserens kontaktoplysninger. </br>//<br> Check this box if you are not the childs parent. This will allow you to provide the referer's contact information.</small>"),
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

    $form['wrapper']['referer']['text'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t("<small>Before filling out the form, you must obtain consent from the child's parents/guardians to share information with Aarhus Municipality and the recreational facility where the child will start a leisure activity. After that, you are ready to fill out the form.</small>"),
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
      '#title' => $this->t('Aktivitet // Activity'),
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
    $form['wrapper']['other']['consent_contact'] = [
      '#prefix' => '<div class="mb-3 col-md-6"><h5>' . $this->t('Samtykke til kontakt // Contact consent') . '</h5><small class="d-block mb-3">' . $this->t(
        'Jeg giver hermed tilladelse til at ForeningsMentor må kontakte mig i forbindelse med tilmelding til ForeningsMentor, samt at ForeningsMentor må kontakte mig senere med henblik på opfølgning på foreningsdeltagelse og deltagelse i ForeningsMentor. Jeg giver også samtykke til at ForeningsMentor må videregive personoplysninger til Aarhus Kommune og til det fritidstilbud, hvor mit barn skal begynde. <br>//</br> I hereby consent that ForeningsMentor may contact me in order to find a leisure activity for my child, and that ForeningsMentor may contact me at a later point in time to know whether my child is participating in the activity or whether we need help finding another activity. I also consent to ForeningsMentor sharing personal data with the Aarhus Kommune and with the leisure activity where my child will begin.'
      ) . '</small>',
      '#suffix' => '</div>',
      '#description' => '',
      '#type' => 'checkbox',
      '#required' => TRUE,
      '#title' => $this->t('Giv samtykke til kontakt // Give contact consent'),
    ];

    $form['wrapper']['other']['consent_data'] = [
      '#prefix' => '<div class="mb-3 col-md-12"><h5>' . $this->t('Samtykke til data // Data consent') . '</h5><small class="d-block mb-3">' . $this->t(
          'Når du henviser dit barn til Aarhus Kommune behandler og opbevarer Aarhus Kommune de indtastede personoplysninger med det formål at få barnet i gang med en fritidsaktivitet. Når du udfylder denne formular, giver du samtykke til at videregive personoplysningerne til Aarhus Kommune og til det fritidstilbud, hvor barnet skal begynde. <br>//</br> When you refer your child to Aarhus Municipality, Aarhus Municipality processes and stores the entered personal information for the purpose of getting the child started with a leisure activity. When you fill out this form, you consent to sharing the personal information with Aarhus Municipality and with the recreational facility where the child will start.'
      ) . '</small>',
      '#suffix' => '</div>',
      '#type' => 'checkbox',
      '#required' => TRUE,
      '#title' => $this->t('Jeg giver tilladelse til at gemme og behandle mine data // I give permission to store and process my data'),
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
            'or' => 'or',
            ':input[name="consent_data"]' => ['checked' => TRUE],
          ],
        ],
      ],
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
    // Limit areas to the desired list. The Taxonomy may hold additional terms
    // for historical reasons. To remove old terms we would have to migrate
    // data.
    $allowedAreas = [
      'Tilst',
      'Gellerup',
      'Skovgårdsparken',
      'Bispehaven',
      'Trige',
      'Vandtårnsområdet – Vorrevangen - Vejlby Vest',
      'Bydækkende – resten af Aarhus',
      'International',
    ];

    foreach ($terms as $term) {
      if (in_array($term->getName(), $allowedAreas)) {
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
