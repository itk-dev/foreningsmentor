<?php

namespace Drupal\foreningsmentor\Form;

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
      '#markup' => '<h1 class="mt-3 mb-3">' . t('Sign up') . '</h1>',
    ];

    $form['wrapper']['top_text'] = [
      '#type' => 'item',
      '#markup' => '<div class="mt-3 mb-3">' . t('Would you like to be signed up as mentor? Fill out the form below and we will contact you as soon as possible.') . '</div>',
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
        ->t('Child info'),
    ];
    $form['wrapper']['child']['child_name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Child's name"),
    ];
    $form['wrapper']['child']['birth_date'] = [
      '#type' => 'date',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Child's birth date"),
    ];
    $form['wrapper']['child']['sex'] = [
      '#type' => 'select',
      '#options' => ['Dreng' => t('Boy'), 'Pige' => t('Girl')],
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Child's sex"),
    ];
    $form['wrapper']['child']['school'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t("Child's school"),
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
      '#title' => $this->t("Child's grade"),
    ];

    $form['wrapper']['referer_check'] = [
      '#prefix' => '<div class="border-bottom mb-3 pb-3">',
      '#suffix' => '</div>',
      '#type' => 'checkbox',
      '#title' => $this->t('I am a referring a child on behalf of someone else.'),
      '#description' => $this->t('<small>Check this box if you are not the childs parent. This will allow you to provide the referer\'s contact information.</small>'),
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
        ->t('Parent info'),
    ];
    $form['wrapper']['parent']['parent_name'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Parent name'),
    ];
    $form['wrapper']['parent']['phone_number'] = [
      '#type' => 'tel',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Parent phone number'),
    ];
    $form['wrapper']['parent']['mail'] = [
      '#type' => 'email',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Parent e-mail address'),
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
      '#prefix' => '<div class="col-md-12"><p class="dawa-address" translate="no">',
      '#suffix' => '</p></div>',
      '#type' => 'textfield',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Address'),
    ];
    $form['wrapper']['other']['activity'] = [
      '#prefix' => '<div class="col-md-12">',
      '#suffix' => '</div>',
      '#type' => 'textarea',
      '#required' => TRUE,
      '#attributes' => ['class' => ['form-control', 'mb-3']],
      '#title' => $this->t('Activity'),
    ];
    $form['wrapper']['other']['area'] = [
      '#prefix' => '<div class="col-md-12">',
      '#suffix' => '</div>',
      '#type' => 'radios',
      '#title' => $this->t('Area'),
      '#required' => TRUE,
      '#attributes' => ['class' => ['mb-3']],
      '#options' => $areaOptions,
      '#description' => $this->t('<small>Select the area where you live. If in doubt select city covering.</small>'),
    ];
    $form['wrapper']['other']['consent_contact'] = [
      '#prefix' => '<div class="mb-3 col-md-6"><h5>' . $this->t('Contact consent') . '</h5><small>' . $this->t('I hereby consent that ForeningsMentor International may contact me in order to find a leisure activity for my child, and that ForeningsMentor International may contact me at a later point in time to know whether my child is participating in the activity or whether we need help finding another activity.') . '</small>',
      '#suffix' => '</div>',
      '#description' => '',
      '#type' => 'checkbox',
      '#required' => TRUE,
      '#title' => $this->t('Give contact consent'),
    ];

    $form['wrapper']['other']['consent_data'] = [
      '#prefix' => '<div class="mb-3 col-md-12"><h5>' . $this->t('Data consent') . '</h5>',
      '#suffix' => '</div>',
      '#type' => 'checkbox',
      '#required' => TRUE,
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
    $params['site_url'] = Url::fromUserInput('/',  array('absolute' => 'true'))->toString();

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
      'field_referer' => $params['form_values']['referer_name'],
      'field_referer_phone' => $params['form_values']['referer_phone'],
      'field_referer_email' => $params['form_values']['referer_mail'],
      'field_activity_wishes' => $params['form_values']['activity'],
      'field_neighborhood' => $area,
      'field_archived' => FALSE,
    ];

    $node =$this->entityTypeManager->getStorage('node')->create($node);
    $node->save();

    $params['node_id'] = $node->id();

    // Notify coordinators assigned to the neighborhood.
    foreach ($neighborhoodUsers as $user) {
      if ($user->hasRole('coordinator')) {
        $this->mailManager->mail('foreningsmentor', 'foreningsmentor_signup', $user->get('mail')->value, $this->languageManager->getDefaultLanguage()->getName(), $params);
      }
    }

    // Notify parent.
    $this->mailManager->mail('foreningsmentor', 'foreningsmentor_parent_signup', $params['form_values']['mail'], $this->languageManager->getDefaultLanguage()->getName(), $params);

    // Notify referer.
    if ($params['form_values']['referer_mail']) {
      $this->mailManager->mail('foreningsmentor', 'foreningsmentor_referer_signup', $params['form_values']['referer_mail'], $this->languageManager->getDefaultLanguage()->getName(), $params);
    }

    $this->messenger()->addStatus($this->t('Thank you for signing up. You will be contacted by @site_name shortly.', ['@site_name' => $params['site_name']]));
  }

}
