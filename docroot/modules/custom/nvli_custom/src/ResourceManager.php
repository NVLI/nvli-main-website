<?php

namespace Drupal\nvli_custom;

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
}
