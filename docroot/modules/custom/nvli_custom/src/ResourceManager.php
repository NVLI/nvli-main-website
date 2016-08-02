<?php

namespace Drupal\nvli_custom;

use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 * Class ResourceManager.
 *
 * @package Drupal\nvli_custom
 */
class ResourceManager {
  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * Fetch resource count of entity.
   *
   * @param string $resource_type
   *   Resource Type.
   *
   * @return int
   *   Resource entity count based on the filters.
   */
   public function resourceEntityCount($resource_type = NULL){
    
    // Entity Query to fetch resource entity count based on resource type 
    // filter.
    $query = \Drupal::entityQuery('node')
      ->condition('status', NODE_PUBLISHED)
      ->condition('type', 'resource');
    
    // If resource type exist then filter data with resource type condition.
    if (!empty($resource_type)) {
      
      $query->condition('field_resource_type.value', $resource_type, '=');
    }
    
    // Return resource entity count.
    return $query->count()->execute();
  }
  
  /**
   * Return thumbnail for resouce entity based on the node object.
   *
   * @param type $node
   *  Node object.
   * 
   * @return string
   *  Return the absolute image path.
   */
  public function resourceEntityThumbnailImage($node) {
    
    // Fetch resource type of current node.
    $resource_type = $node->get('field_resource_type')->getValue();
    
    // Return if resource type field is not exist in node.
    if (empty($resource_type)) {
      return '';
    }
    
    $nid = $node->id();
   
    // Fetch image data from default thubnail image field.
    // Thumbnail Image.
    $thumb_image = $node->get('field_thumb')->getValue();

    // Initalize variables.
    $thumbnail_image = '';    
    $image_link = '';
    $data = array();
    
    global $base_url;

    // Check if image exist in default thumbnail image.
    if (!empty($thumb_image)) {
      
      // Load image object.
      $file = File::load($thumb_image[0]['target_id']);
    
      // Fetch image uri from image object.
      $path = $file->getFileUri();
      
      // Create absolute image url.
      $thumbnail_image = file_create_url($path);
    }
    
    switch ($resource_type[0]['value']) {
      case 'audio_video' :
       
        // If thumbnail image dosn't exist in default thubnail image field then
        // fetch static default image for Audio Video.
        if (empty($thumbnail_image)) {
          $thumbnail_image = \Drupal::service('nvli_custom.resource_manager')->resourceTypeDefaultImage($node);
        }
      break;
      case 'books' :
        
        // Fetch details image for books.
        if (empty($thumbnail_image)) {
          $thumbnail_image = \Drupal::service('nvli_custom.resource_manager')->resourceTypeDefaultImage($node);
        }
      break;
      case 'govt_archives' :
        
        // Fetch details image for Govt Archives.
        if (empty($thumbnail_image)) {
          $thumbnail_image = \Drupal::service('nvli_custom.resource_manager')->resourceTypeDefaultImage($node);
        }
      break;
      case 'journal_and_thesis' :
        
        // Fetch details image for Journal and thesis.
        if (empty($thumbnail_image)) {
          $thumbnail_image = \Drupal::service('nvli_custom.resource_manager')->resourceTypeDefaultImage($node);
        }
      break;
      case 'manuscripts' :
        
        // Fetch details image for Manuscript.
        if (empty($thumbnail_image)) {
          $thumbnail_image = \Drupal::service('nvli_custom.resource_manager')->resourceTypeDefaultImage($node);
        }
      break;
      case 'maps' :
        
        // Fetch details image for Maps.
        if (empty($thumbnail_image)) {
          $thumbnail_image = \Drupal::service('nvli_custom.resource_manager')->resourceTypeDefaultImage($node);
       }
      break;
      case 'museum' :
        
        $image_link =  Url::fromUri('internal:/node/' . $nid . '/mirador')->toString();
        
        // Fetch image data from default thubnail image field.
        // Thumbnail Image.
        $image_file_path = $node->get('field_image_file_path')->getValue();
        
        $thumbnail_image = $base_url . '/iiif/' . $image_file_path[0]['value'] . 'full/500,/0/default.jpg';
        
        // Fetch details image for Museum.
        if (empty($thumbnail_image)) {
          $thumbnail_image = \Drupal::service('nvli_custom.resource_manager')->resourceTypeDefaultImage($node);
        }
      break;
      case 'newspaper_archives' :
        
        $image_link = Url::fromUri('internal:/node/' . $nid . '/mirador')->toString();
        
        // Fetch image data from default thubnail image field.
        // Thumbnail Image.
        $image_file_path = $node->get('field_image_file_path')->getValue();
        
        $thumbnail_image = $base_url . '/iiif/' . $image_file_path[0]['value'] . 'full/500,/0/default.jpg';
        
        // Fetch details image for Newspaper Acchives.
        if (empty($thumbnail_image)) {
          $thumbnail_image = \Drupal::service('nvli_custom.resource_manager')->resourceTypeDefaultImage($node);
        }
      break;
    }
    
    $data['image_path'] = $thumbnail_image;
    $data['image_link'] = $image_link;

    return $data;    
  }
  
  /**
   * Fetch default image of resource type.
   * 
   * @param type $resource_type
   *  Machine name of resource type.
   *
   * @return string
   *  Return absolute path of default image.
   */
  public function resourceTypeDefaultImage($resource_type) {
    
    global $base_url;

    switch ($resource_type) {
      case 'audio_video' :
       
        // Fetch default image for Audio Video.
        $default_image = $base_url . '/themes/nvli/images/default-book.png';
      break;
      case 'books' :
        
        // Fetch default image for books.
        $default_image = $base_url . '/themes/nvli/images/default-book.png';
      break;
      case 'govt_archives' :
        
        // Fetch default image for Govt Archives.
        $default_image = $base_url . '/themes/nvli/images/default-govt_archives.png';
      break;
      case 'journal_and_thesis' :
        
        // Fetch default image for Journal and thesis.
        $default_image = $base_url . '/themes/nvli/images/default-journal_and_thesis.png';
      break;
      case 'manuscripts' :
        
        // Fetch default image for Manuscript.
        $default_image = $base_url . '/themes/nvli/images/default-manuscripts.png';
      break;
      case 'maps' :
        
        // Fetch default image for Maps.
        $default_image = $base_url . '/themes/nvli/images/default-maps.png';
      break;
      case 'museum' :
        
        // Fetch default image for Museum.
        $default_image = $base_url . '/themes/nvli/images/default-museum.png';
      break;
      case 'newspaper_archives' :
        
        // Fetch default image for Newspaper Acchives.
        $default_image = $base_url . '/themes/nvli/images/default-newspaper_archives.png';
      break;
    }
    
    return $default_image;
  }

}
