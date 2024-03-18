<?php

namespace Drupal\wpweb_infotech\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

class ListController extends ControllerBase{

  public function formDataList(){

    // Something missing in query builder for date_formate expression
    // $result = \Drupal::database()->select('wpweb_infotech', 'wp')
    //   ->fields('wp',['name', 'gender', 'dob', 'profile_photo_fid'])
    //   // ->addExpression('DATE_FORMAT(wp.dob, "%Y-%m-%d")', 'dob')
    //   ->execute()
    //   ->fetchAll();
    
    $result = \Drupal::database()->query("select name, gender, DATE_FORMAT(dob, '%Y-%m-%d') as dob, profile_photo_fid from wpweb_infotech")->fetchAll();

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
        $file_uri = $file->uri->value;//or $file->getFileUri();//"public://profile_photo/IMG_20211127_174339_1-min.jpg"
        $file_absolute_url = file_create_url($file_uri);//"http://localhost/web/d8-int-practice/web/sites/default/files/profile_photo/IMG_20211127_174339_1-min.jpg"
      }
      else{
        $module_path = \Drupal::service('module_handler')->getModule('wpweb_infotech')->getPath();
        $file_absolute_url = '../' . $module_path . '/image/profile.jpg';
      }
      $rows[] = [
        'name' => $record->name,
        'gender' => $record->gender,
        'dob' => $record->dob,
        'profile_photo' => t("<img src='$file_absolute_url' alt='profile' width='60', height='60'>"),
      ];
    }

    // $build['add_user'] = [
    //   '#markup' => t("<button class='btn btn-primary'><a href='/wpweb-infotech/custom-form'>Add user</a></button>")
    // ];

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