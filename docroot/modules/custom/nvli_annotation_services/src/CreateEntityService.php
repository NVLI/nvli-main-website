<?php

namespace Drupal\nvli_annotation_services;


/**
 * Class CreateEntityService.
 *
 * @package Drupal\nvli_annotation_services
 */
class CreateEntityService implements CreateEntityServiceInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * @param $contentData
   * @param string $entity_type
   * @return array
   */
  public static function createContent($contentData, $entity_type = 'node') {
    $entity = \Drupal::entityTypeManager()
      ->getStorage($entity_type)
      ->create($contentData);
    // Save Content.
    $entity->save();

    // Return Content ID
    return array($entity->id());
  }
}
