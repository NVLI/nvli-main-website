<?php

namespace Drupal\nvli_annotation_services;
use Drupal\field\FieldConfigInterface;


/**
 * Class EntityTypeFieldService.
 *
 * @package Drupal\nvli_annotation_services
 */
class EntityTypeFieldService implements EntityTypeFieldServiceInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  function entityTypeFields($entityType) {
    $entityManager = \Drupal::service('entity.manager');
    $fields = [];
    if(!empty($entityType)) {
      $fields = array_filter(
        $entityManager->getFieldDefinitions('node', 'article'), function ($field_definition) {
        return $field_definition instanceof FieldConfigInterface;
      }
      );
    }

    return $fields;
  }
}
