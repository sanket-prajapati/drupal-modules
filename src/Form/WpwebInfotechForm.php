<?php

namespace Drupal\wpweb_infotech\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Database\Connection;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

class WpwebInfotechForm extends FormBase{

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * @var \Drupal\Core\Messenger\MessengerInterface;
   */
  protected $messenger;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface;
   */
  protected $config;

  /**
   * The database service.
   * @param \Drupal\Core\Database\Connection $database
   * @param \Drupal\ $messenger
   * */

  public function __construct(Connection $database, MessengerInterface $messenger, ConfigFactoryInterface $config){
    $this->database = $database;
    $this->messenger =  $messenger;
    $this->config = $config;
  }

  public static function create(ContainerInterface $container){
    return new static (
      $container->get('database'),
      $container->get('messenger'),
      $container->get('config.factory'),
    );
  }
  
  /**
   * {@inheritdoc}
   */
  public function getFormId(){
    return 'wpweb_infotech_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm($form, FormStateInterface $form_state){
    // $config = \Drupal::config('wpweb_infotech.settings');
    $config = $this->config('wpweb_infotech.settings');
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

  /**
   * {@inheritdoc}
   */
  public function validateForm(&$form, FormStateInterface $form_state){
    // Validate Name field.
    $name_value = $form_state->getValue('name');
    if (empty($name_value)) {
      $form_state->setErrorByName('name', $this->t('Name field cannot be empty.'));
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name_value)) {
      $form_state->setErrorByName('name', $this->t('Name field should not contain number and special characters.'));
    }

    // Validate DOB field based on configuration.
    // $config = \Drupal::config('wpweb_infotech.settings');
    $config = $this->config('wpweb_infotech.settings');
    $wpweb_date = $config->get('wpweb_date');
    $dob_value = $form_state->getValue('dob');
    if (!empty($dob_value) && strtotime($dob_value) < strtotime($wpweb_date)) {
      $form_state->setErrorByName('dob', $this->t('DOB must be on or after @date', ['@date' => $wpweb_date]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(&$form, FormStateInterface $form_state){

    $form_file = $form_state->getValue('profile_photo', 0);
    if (isset($form_file[0]) && !empty($form_file[0])) {
      $file = File::load($form_file[0]);
      $file->setPermanent();
      $file->save();
    }
    
    // \Drupal::database()->insert('wpweb_infotech')
    $this->database->insert('wpweb_infotech')
    ->fields([
      'name' => $form_state->getValue('name'),
      'gender' => $form_state->getValue('gender'),
      'dob' => $form_state->getValue('dob'),
      'profile_photo_fid' => $form_state->getValue('profile_photo', 0)[0],
      ])
      ->execute();
    
    // Redirect to list page
    $form_state->setRedirect('wpweb_infotech.form_data_list');
    // \Drupal::messenger()->addMessage(t('@name Your form is submited successfully', ['@name' => $form_state->getValue('name')]));
    $this->messenger->addMessage(t('@name Your form is submited successfully', ['@name' => $form_state->getValue('name')]));
  }
}