<?php

/**
 * @file
 * Contains \Drupal\custom_solr_search\FacetQueryConfig.
 */

namespace Drupal\custom_solr_search;


/**
 * Class FacetQueryConfig.
 *
 * @package Drupal\custom_solr_search
 */
class FacetQueryConfig {
  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   *
   */
  public function getFacetQuery($facetId){
    $connection = Database::getConnection();
    $query = $connection->select('config', 'c')
      ->fields('c', array('data'))
      ->condition('c.name', '%custom_solr_search.facet_fields.%', 'LIKE');
    $results = $query->execute();
    $results = $results->fetchAll(\PDO::FETCH_OBJ);
    foreach ($results as $result) {
      $id = unserialize($result->data)['id'];
      $settings[$id] = unserialize($result->data);
    }
    return $settings[$facetId];
  }
}
