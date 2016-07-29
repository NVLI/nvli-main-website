<?php

namespace Drupal\nvli_annotation_services;


class UpdateAnnotation {

  public static function updateAnnotation($type, $entities, &$context){
    $message = 'Adding annotation...';

    foreach ($entities as $entity) {
      $server = 'solr';//isset($entity->get('server')->value)?$entity->get('server')->value: 'solr';
      $id = $entity->get('solr_doc_id')->value;
      $annotation = array();
      $fields = array();
      foreach ($entity->toArray()['annotation'] as $val) {
        $annotation[] = $val['value'];
      }
      $fields['annotation'] = $annotation;

      $results[] = \Drupal::service('nvli_annotation_services.add_annotation')
        ->addAnnotation($server, $id, $fields);
    }

    $context['message'] = $message;
    $context['results'] = $results;
  }

  function addAnnotationFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One post processed.', '@count posts processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
//    ep($results);exit;
    drupal_set_message($message);
  }
}