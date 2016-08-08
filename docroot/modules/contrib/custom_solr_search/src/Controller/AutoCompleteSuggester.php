<?php

/**
 * @file
 * Contains \Drupal\custom_solr_search\Controller\AutoCompleteSuggester.
 */

namespace Drupal\custom_solr_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Solarium\QueryType\Suggester\Query as SuggestQuery;

/**
 * Class AutoCompleteSuggester.
 *
 * @package Drupal\custom_solr_search\Controller
 */
class AutoCompleteSuggester extends ControllerBase {
  /**
   * Autocompletetitle.
   *
   * @return string
   *   Return Hello string.
   */
  public function AutoCompleteTitle(Request $request) {
    $string = $request->query->get('q');

    // Get solarium client.
    // TODO: Change hard coded solr client machine name.
    $solr_client = \Drupal::service('custom_solr_search.server')->getSolrClient('nvli');
    // Initiate Solarium basic suggest query.
    $query = new SuggestQuery();
    if (!empty($string)) {
      // Set search keyword.
      $query->setQuery($string);
      $query->setDictionary('suggest');
      $query->setOnlyMorePopular(true);
      $query->setCount(10);
      $query->setCollate(true);
      // Create a request for query.
      $suggestion_set = $solr_client->suggester($query);

      $suggestions = array();
      foreach ($suggestion_set as $term => $termResult) {
        foreach ($termResult as $result) {
          $suggestions[] = $result;
        }
      }
      return new JsonResponse($suggestions);
    }
    return new JsonResponse(array());
  }

  public function getMatchArray($string) {

  }

}
