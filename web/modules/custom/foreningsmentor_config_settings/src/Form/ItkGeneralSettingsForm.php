<?php

namespace Drupal\foreningsmentor_config_settings\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;

/**
 * Class ItkGeneralSettingsForm.
 *
 * @package Drupal\itk_admin\Form
 */
class ItkGeneralSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_general_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   *   The base config.
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('itk_admin.itk_config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    $form['general_settings'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-footer',
    ];

    $form['footer'] = [
      '#title' => $this->t('Footer'),
      '#type' => 'details',
      '#open' => TRUE,
      '#weight' => '0',
      '#group' => 'general_settings',
    ];

    $form['footer']['footer_text'] = [
      '#title' => $this->t('Footer text first column'),
      '#type' => 'text_format',
      '#format' => 'filtered_html',
      '#default_value' => $config->get('footer_text'),
      '#weight' => '1',
    ];

    $form['footer']['footer_text_2nd'] = [
      '#title' => $this->t('Footer text second column'),
      '#type' => 'text_format',
      '#format' => 'filtered_html',
      '#default_value' => $config->get('footer_text_2nd'),
      '#weight' => '1',
    ];

    $form['misc'] = [
      '#title' => $this->t('Pages'),
      '#type' => 'details',
      '#open' => TRUE,
      '#weight' => '2',
      '#group' => 'general_settings',
    ];

    $node_reference = Node::load($config->get('frontpage_id'));
    $form['misc']['frontpage_id'] = [
      '#title' => $this->t('Front page'),
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#default_value' => $node_reference,
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save changes'),
      '#weight' => '6',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger()->addStatus('Settings saved');

    // Set the rest of the configuration values.
    $this->getBaseConfig()->setMultiple([
      'footer_text' => $form_state->getValue('footer_text')['value'],
      'footer_text_2nd' => $form_state->getValue('footer_text_2nd')['value'],
      'frontpage_id' => $form_state->getValue('frontpage_id'),
    ]);

    drupal_flush_all_caches();
  }

}
