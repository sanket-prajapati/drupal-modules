<?php

namespace Drupal\wpweb_infotech\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Extension\ModuleHandlerInterface;

class ListController extends ControllerBase{
  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * @var \Drupal\Core\Extension\ModuleHandlerInterface;
   */
  protected $module_handler;

  /**
   * The database service.
   * @param \Drupal\Core\Database\Connection $database
   * 
   * The module handler service.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   */
  public function __construct(Connection $database, ModuleHandlerInterface $module_handler){
    $this->database = $database;
    $this->module_handler =  $module_handler;
  }

  public static function create(ContainerInterface $container){
    return new static (
      $container->get('database'),
      $container->get('module_handler')
    );
  }

  public function formDataList(){

    // Fetch details of records submitted
    // $result = \Drupal::database()->select('wpweb_infotech', 'wp')
    //   ->fields('wp',['name', 'gender', 'dob', 'profile_photo_fid'])
    //   // ->addExpression('DATE_FORMAT(wp.dob, "%Y-%m-%d")', 'dob')
    //   ->execute()
    //   ->fetchAll();
    
    // $result = \Drupal::database()->query("select name, gender, DATE_FORMAT(dob, '%Y-%m-%d') as dob, profile_photo_fid from wpweb_infotech")->fetchAll();
    $result = $this->database->query("select name, gender, DATE_FORMAT(dob, '%Y-%m-%d') as dob, profile_photo_fid from wpweb_infotech")->fetchAll();
    $header = [
      'name' => 'Name',
      'gender' => 'Gender',
      'dob' => 'DOB',
      'profile_photo' => 'Profile Photo'
    ];

    $rows = [];
    foreach($result as $record){

      $profile_photo_fid = $record->profile_photo_fid;
      if($profile_photo_fid){
        $file = File::load($profile_photo_fid);
        $file_uri = $file->uri->value;
        $file_absolute_url = file_create_url($file_uri);
      }
      else{
        // $module_path = \Drupal::service('module_handler')->getModule('wpweb_infotech')->getPath();
        $module_path = $this->module_handler->getModule('wpweb_infotech')->getPath();
        $file_absolute_url = '../' . $module_path . '/image/profile.jpg';
      }
      $rows[] = [
        'name' => $record->name,
        'gender' => $record->gender,
        'dob' => $record->dob,
        'profile_photo' => t("<img src='$file_absolute_url' alt='profile' width='60', height='60'>"),
      ];
    }

    // Make links for add user
    $url_object = Url::fromRoute('wpweb_infotech.simple_form');
    $build['add_user'] = [
      '#type' => 'link',
      '#url' => $url_object,
      '#title' => t('Add user'),
      '#attributes' => [
        'class' => 'btn btn-primary',
      ],
    ];

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No entries available'),
    ];

    return $build;
  }

}