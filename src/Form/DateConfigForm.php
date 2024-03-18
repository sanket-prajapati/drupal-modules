<?php

namespace Drupal\wpweb_infotech\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure wpweb infotech practical settings for this site.
 */
class DateConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wpweb_infotech_date_config';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['wpweb_infotech.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['wpweb_date'] = [
      '#type' => 'date',
      '#title' => t('Choose date'),
      '#default_value' => $this->config('wpweb_infotech.settings')->get('wpweb_date')
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('wpweb_infotech.settings')
      ->set('wpweb_date', $form_state->getValue('wpweb_date'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
