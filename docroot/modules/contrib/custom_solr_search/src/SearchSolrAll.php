<?php

/**
 * @file
 * Contains \Drupal\custom_solr_search\SearchSolrAll.
 */

namespace Drupal\custom_solr_search;


/**
 * Class SearchSolrAll.
 *
 * @package Drupal\custom_solr_search
 */
class SearchSolrAll {
  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * Searching in all Solr servers.
   *
   * @param string $keyword
   *   String to search.
   * @param array $options
   *   Array for filter.
   * @return array $results
   *   Array of search results.
   */
  public function seachAll($keyword, $options = NULL){
    $servers = \Drupal::service('custom_solr_search.solr_servers')->getServers();
    $results = array();
    foreach ($servers as $server_machine => $server_display) {
      $result = \Drupal::service('custom_solr_search.search')->basicSearch($keyword, 0, 5, $server_machine, $options);
      $results['docs'] = array_merge($results['docs'], $result);
    }
    return $results;
  }
}
