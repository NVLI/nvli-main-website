<?php

namespace Drupal\nvli_annotation_services\EventSubscriber;

use Drupal\nvli_annotation_services\AnnotationStoreEvent;
use Drupal\Core\Database\Database;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Class AnnotationEventSubscriber.
 *
 * @package Drupal\nvli_annotation_services
 */
class AnnotationEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[AnnotationStoreEvent::SAVE][] = array('reactOnAnnotationSave', 100);
    return $events;

  }

  /**
   * Subscriber Callback for the event.
   * @param AnnotationStoreEvent $event
   */
  public function reactOnAnnotationSave(AnnotationStoreEvent $event) {

    $id = $event->getReferenceSolrDocId();
    $fields = $this->getAnnotationFields($id);
    $server = 'nvli';
    $results = \Drupal::service('nvli_annotation_services.add_annotation')
      ->addAnnotation($server, $id, $fields);

    if($results->getResponse()->getStatusMessage() == 'OK'){
      drupal_set_message("Saved annotation for solr doc:" . $event->getReferenceSolrDocId());
    }else{
      drupal_set_message($results->getResponse()->getStatusMessage());
    }

  }
  protected function getAnnotationFields($id){
    $connection = Database::getConnection();
    $query = $connection->select('annotation_store_entity', 'ae')
      ->fields('ae', array('id'));
    $query->condition('resource_ref', $id);
    $data = $query->execute()->fetchAll();
    $value = array();
    foreach ($data as $val){
      $value[] = $val->id;
    }

    $entities = \Drupal::entityTypeManager()
      ->getStorage('annotation_store_entity')
      ->loadMultiple($value);
    $fields = array();
    foreach ($entities as $entity){
      $fields['annotation_key_txt_mv'][] = $entity->id();
      $fields['annotation_txt_mv'][] = $entity->title->value;
      $fields['annotation_type_txt_mv'][] = $entity->type->value;
    }
    return $fields;
  }
}
