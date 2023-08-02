<?php

namespace Drupal\custom_new\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure custom new settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_new_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['custom_new.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('subject'),
      '#default_value' => $this->config('custom_new.settings')->get('subject'),
    ];
    $form['text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('text'),
      '#default_value' => $this->config('custom_new.settings')->get('text'),
    ];

    if (\Drupal::moduleHandler()->moduleExists('token')) {
      $form['tokens'] = [
        '#title' => $this->t('Tokens'),
        '#type' => 'container',
      ];
      $form['tokens']['help'] = [
        '#theme' => 'token_tree_link',
        '#token_types' => [
          'node',
        ],
            // '#token_types' => 'all'
        '#global_types' => FALSE,
        '#dialog' => TRUE,
      ];
    }
    return parent::buildForm($form, $form_state);
  }

  // /**
  //  * {@inheritdoc}
  //  */
  // public function validateForm(array &$form, FormStateInterface $form_state) {
  //   if ($form_state->getValue('example') != 'example') {
  //     $form_state->setErrorByName('example', $this->t('The value is not correct.'));
  //   }
  //   parent::validateForm($form, $form_state);
  // }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('custom_new.settings')
      ->set('subject', $form_state->getValue('subject'))
      ->set('text', $form_state->getValue('text'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
