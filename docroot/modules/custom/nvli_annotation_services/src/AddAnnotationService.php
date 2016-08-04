<?php

namespace Drupal\nvli_annotation_services;

use Solarium\Client;


/**
 * Class AddAnnotationService.
 *
 * @package Drupal\nvli_annotation_services
 */
class AddAnnotationService implements AddAnnotationServiceInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  public function addAnnotation($server, $id, $fields) {
    error_log(print_r($server, TRUE));
    $backend_config = \Drupal::config('search_api.server.' . $server)
      ->get('backend_config');
    $client = new Client();
    $client->createEndpoint($backend_config + ['key' => 'core'], TRUE);
    // get an update query instance
    $update = $client->createUpdate();
    // create a new document for the data
    $doc = $update->createDocument();
    $doc->setKey('id');
    $doc->setField('id', $id);

    foreach ($fields as $key => $field) {
      $doc->setField($key, $field, NULL, 'set');
    }

    $update->addDocuments(array($doc));
    $update->addCommit(TRUE);
    $result = $client->update($update);
    return $result;
  }

}
