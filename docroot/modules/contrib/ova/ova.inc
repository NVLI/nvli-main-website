<?php

/**
 * @file
 * Open Video Annotation using template variables.
 */
use Drupal\Core\Url;
 /**
 *  Implements template_preprocess_hook().
 */
function template_preprocess_ovaAudio(&$variables) {
  $annotation_settings = array();
  $entity = $variables['entity'];
  $resource_entity_id = $entity->id();
  $variables['#attached']['drupalSettings']['ova_annotation']['annotation_settings'] = $resource_entity_id;
}

/**
 *  Implements template_preprocess_hook().
 */
function template_preprocess_ovaVideo(&$variables) {
  $user = \Drupal::currentUser();
  $perform_annotation = FALSE;
  $annotation_settings = array();
  $entity = $variables['entity'];
  $resource_entity_id = $entity->id();
  $variables['#attached']['drupalSettings']['ova_annotation']['annotation_settings'] = $resource_entity_id;
  $current_path = \Drupal::service('path.current')->getPath();
  if ($user->hasPermission('perform ova annotations')) {
    $perform_annotation = TRUE;
  }
  $variables['#attached']['drupalSettings']['ova_annotation']['perform_annotation'] = $perform_annotation;
  $variables['#attached']['drupalSettings']['ova_annotation']['login_url'] = $current_path;
}
