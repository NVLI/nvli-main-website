<?php

namespace Drupal\annotation_store\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 * Controller routines for annotation_store routes.
 */
class AnnotationStoreController extends ControllerBase {

  /**
   * Routing callback - annotation create.
   */
  public function annotationStoreCreate($id, Request $request) {

    $response = array();
    // Get the request data.
    $received = $request->getContent();
    $annotation_data = json_decode($received);
    // Create the annotation entity.
    $entity->content['data']['annotations'] = $annotation_data;
    \Drupal::moduleHandler()->invokeAll('annotation_store_create_endpoint_output_alter', array(&$entity, $id));
    $annotation_data = $entity->content['data']['annotations'];
    $response = $this->annotationApiCreate($id, $annotation_data);
    // Add watchdog.
    //\Drupal::logger('Annotation Store')->info('Created entity %type with ID %id.', array('%type' => $entity->getEntityTypeId(), '%id' => $entity->id()));
    //$response['id'] = $entity->id();
    return  new JsonResponse($response);
  }

  /**
   * Routing callback - annotation update and delete.
   */
  public function annotationStoreApi($id, Request $request) {
    $response = array();
    // Fetch the request method.
    $request_method = $request->getMethod();
    // Depending on the request method, perform update/delete/search operations.
    switch ($request_method) {

      case 'GET':
        $response = $this->annotationApiSearch($id, $request);
        break;

      case 'PUT':
        $response = $this->annotationApiUpdate($id, $request);
        break;

      case 'DELETE':
        $response = $this->annotationApiDelete($id);
        break;
    }
    return new JsonResponse($response);
  }

  /**
   * Annotation search - Returns list of annotations.
   */
  public function annotationApiSearch($id, $request) {

    $output = array();
    $resource_entity_id = $id;
    $media = \Drupal::request()->query->get('media');
    // Load the Entity.
    $entity = \Drupal::entityTypeManager()->getStorage('annotation_store')->load($resource_entity_id);
    // get annotations from annotation store
    $annotations = $this->getSearchAnnotation($resource_entity_id);
    $entity->content['data']['annotations'] = $annotations;
    $entity->content['data']['resource_media_type'] = $media;
    // Providing hook_annotation_store_search_endpoint_output_alter(&$entity).
    \Drupal::moduleHandler()->invokeAll('annotation_store_search_endpoint_output_alter', array(&$entity));
    $output = $entity->content['data']['annotations'];
    return $output;
  }

  /**
   * Gathers annotations added against an entity.
   */
  public function getSearchAnnotation($resource_entity_id) {
    $annotations = array();
    $ids = \Drupal::entityQuery('annotation_store')->condition('resource_entity_id', $resource_entity_id)->execute();
      foreach ($ids as $key => $value) {
        $records = \Drupal::entityTypeManager()->getStorage('annotation_store')->load($value);
        $annotation_object = json_decode($records->data->value);
        $annotations[] = array(
          'data' => $annotation_object,
          'id' => $value,
          'text' => $records->text->value,
        );
      }
     return $annotations;
  }

  /**
   * Annotation create as entity.
   */
  public function annotationApiCreate($id, $annotation_data) {
    $response = array();
    if($id) {
      // Get the site default language.
      $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
      // Save only if annotation data is present.
      if ($annotation_data->text) {
        $entity = \Drupal::entityManager()->getStorage('annotation_store')->create(array(
          'type' => $annotation_data->media,
          'language' => $language,
          'data' => json_encode($annotation_data->data),
          'uri' => $annotation_data->uri,
          'text' => $annotation_data->text,
          'resource_entity_id' => $annotation_data->id,
        ));
        $entity->save();
      }
      $response['id'] = $entity->id();
      return $response;
    }
  }

  /**
   * Annotation update - loads posted data, returns data as JSON object.
   */
  public function annotationApiUpdate($id, $request) {
    $response = array();
    $entity = array();
    // Get the request data.
    $received = $request->getContent();
    $annotation_data = json_decode($received);
    if ($id) {
      $entity = $this->updateAnnotation($id, $annotation_data, 'onUpdate');
    }
    $response['id'] = $entity->id();
    return $response;
  }

  /**
   * Annotation update - deletes the entity based on the id passed.
   */
  public function annotationApiDelete($id) {
    $response = array();
    if ($id) {
      $entity = \Drupal::entityTypeManager()->getStorage('annotation_store')->load($id);
      $entity->delete();
    }
    $response['id'] = $id;
    return $response;
  }

  /**
   * Annotation update callback.
   */
  public function updateAnnotation($id, $data, $flag) {
    $entity = \Drupal::entityTypeManager()->getStorage('annotation_store')->load($id);
    if ($flag == 'onUpdate') {
      $entity->text->value = $data->text;
      $entity->changed->value = time();
    }
    $entity->data->value = json_encode($data);
    $entity->save();
    return $entity;
  }

}
