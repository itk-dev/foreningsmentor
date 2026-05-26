<?php

declare(strict_types=1);

namespace Drupal\address_adressevaelger\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure the Adressevælger API token.
 */
class AdressevaelgerSettingsForm extends ConfigFormBase {

  private const SETTINGS = 'address_adressevaelger.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'address_adressevaelger_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [self::SETTINGS];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config(self::SETTINGS);

    $form['api_token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Adressevælger API token'),
      '#description' => $this->t('Token issued by Klimadatastyrelsen for the Adressevælger service. Override per environment via settings.local.php if you prefer not to commit it.'),
      '#default_value' => (string) $config->get('api_token'),
      '#size' => 80,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config(self::SETTINGS)
      ->set('api_token', (string) $form_state->getValue('api_token'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
