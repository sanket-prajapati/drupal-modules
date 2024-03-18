<?php

namespace Drupal\wpweb_infotech\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class WpwebDateConfigForm extends ConfigFormBase{
  public function getFormId(){
    return 'wpweb_date_form';
  }
  
  public function getEditableConfigNames(){
    return [
      'wpweb_infotech.settings'
    ];
  }


  public function buildForm($form, FormStateInterface $form_state){
    // dump('dmeo');
    // exit;
    $form['wpweb_date'] = [
      '#type' => 'date',
      '#title' => t('Choose date'),
      '#default_value' => $this->config('wpweb_infotech.settings')->get('wpweb_date')
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(&$form, FormStateInterface $form_state){
    $this->config('wpweb_infotech.settings')
      ->set('wpweb_date', $form_state->getValue('wpweb_date'))
      ->save();
    parent::submitForm($form, $form_state);
    // dump()
  }
}

// /**
//    * {@inheritDoc}
//    */
//   protected function getEditableConfigNames() {
//     return [
//       'admin_toolbar.settings',
//     ];
//   }

//   /**
//    * {@inheritDoc}
//    */
//   public function getFormId() {
//     return 'admin_toolbar_settings';
//   }

//   /**
//    * {@inheritdoc}
//    */
//   public function buildForm(array $form, FormStateInterface $form_state) {
//     $config = $this->config('admin_toolbar.settings');
//     $depth_values = range(1, 9);
//     $form['menu_depth'] = [
//       '#type' => 'select',
//       '#title' => $this->t('Menu depth'),
//       '#description' => $this->t('Maximal depth of displayed menu.'),
//       '#default_value' => $config->get('menu_depth'),
//       '#options' => array_combine($depth_values, $depth_values),
//     ];

//     return parent::buildForm($form, $form_state);
//   }

//   /**
//    * {@inheritdoc}
//    */
//   public function submitForm(array &$form, FormStateInterface $form_state) {
//     $this->config('admin_toolbar.settings')
//       ->set('menu_depth', $form_state->getValue('menu_depth'))
//       ->save();
//     parent::submitForm($form, $form_state);
//     $this->cacheMenu->invalidateAll();
//     $this->menuLinkManager->rebuild();
//   }