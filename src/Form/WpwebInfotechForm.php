<?php

namespace Drupal\wpweb_infotech\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class WpwebInfotechForm extends FormBase{
  /**
   * getFormId()
   */

  public function getFormId(){
    return 'wpweb_infotech_form';
  }

  public function buildForm($form, FormStateInterface $form_state){
    $config = \Drupal::configFactory()->getEditable('wpweb_infotech.settings');
    if($config->get('wpweb_date')){
      $form['wpweb']['config_date'] = [
        '#type' =>'markup',
        '#markup' => 'DOB must be from '. $config->get('wpweb_date')
      ];
    }

    $form['wpweb']['name'] = [
      "#type" => 'textfield',
      "#title" => t('Name'),
      "#required" => TRUE,
    ];

    $form['wpweb']['dob'] = [
      "#type" => 'date',
      "#title" => t('DOB'),
      "#required" => FALSE,
    ];

    $form['wpweb']['gender'] = [
      '#type' => 'radios',
      '#title' => t('Gender'),
      '#options' => [ 
        'male'=> t('Male'), 
        'female' => t('Female'), 
        'other' => t('Other') 
      ],
    ];

    $form['wpweb']['profile_photo'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Profile photo'),
      // '#upload_location' => 'private://profile', //Giving error bcz we not set this in setting.php
      // '#upload_location' => 'public://sites/default/files/profile_photo', //It will set whole path inside files directory, resulting dublicating path
      '#upload_location' => 'public://profile_photo',
      '#upload_validators' => [
        'file_validate_extensions' => ['jpg jpeg gif png webp'],
      ],
    ];
    
    
    $form['wpweb']['submit'] = [
      "#type" => 'submit',
      "#value" => t('Submit')
    ];

    return $form;
  }

  public function validateForm(&$form, FormStateInterface $form_state){

  }

  public function submitForm(&$form, FormStateInterface $form_state){

    $form_file = $form_state->getValue('profile_photo', 0);
    // dump($form_file);//file id array
    if (isset($form_file[0]) && !empty($form_file[0])) {
      $file = File::load($form_file[0]);
      // dump($file);//file with all file detials
      $file->setPermanent();
      $file->save();
    }
    
    \Drupal::database()->insert('wpweb_infotech')
    ->fields([
      'name' => $form_state->getValue('name'),
      'gender' => $form_state->getValue('gender'),
      'dob' => $form_state->getValue('dob'),
      'profile_photo_fid' => $form_state->getValue('profile_photo', 0)[0],
      ])
      ->execute();
    
    // Redirect to list page
    $form_state->setRedirect('wpweb_infotech.form_data_list');
    \Drupal::messenger()->addMessage(t('@name Your form is submited successfully', ['@name' => $form_state->getValue('name')]));
  }
}