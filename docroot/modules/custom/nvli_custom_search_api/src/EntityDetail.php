<?php

namespace Drupal\nvli_custom_search_api;

/**
 * Class EntityDetail.
 *
 * @package Drupal\nvli_custom_search_api
 */
class EntityDetail {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   *
   * @param type $doc_id
   *   Document Id.
   * @return array
   *   Entity Id.
   */
  public function get_nid($doc_id) {
    $nid = \Drupal::database()->select('node__field_solr_docid', 'n')
        ->fields('n', array('entity_id'))
        ->condition('field_doc_id_value' , $doc_id ,'=')
        ->execute()->fetchField();;
    $entity_id = !empty($nid) ? $nid : [];
    $results = array('entity_id' => $entity_id);
    return $results;
  }

}
