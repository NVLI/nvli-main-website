<?php

namespace Drupal\nvli_custom;

/**
 * Class ResourceCount.
 *
 * @package Drupal\nvli_custom
 */
class ResourceCount {
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
    // Query to fetch resource entity count.
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1)
      ->condition('type', 'resource');
    
    return $query->count()->execute();
  }
}
