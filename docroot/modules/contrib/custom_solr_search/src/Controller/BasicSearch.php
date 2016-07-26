<?php

/**
 * @file
 * Contains \Drupal\custom_solr_search\Controller\BasicSearch.
 */

namespace Drupal\custom_solr_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use \Drupal\Core\Link;

/**
 * Class BasicSearch.
 *
 * @package Drupal\custom_solr_search\Controller
 */
class BasicSearch extends ControllerBase {

  /**
   * Search.
   *
   * @return string
   *   Return Hello string.
   */
  public function search($server = NULL, $keyword = NULL) {
    global $base_url;
    // Search form.
    $render['form'] = $this->formBuilder()->getForm('Drupal\custom_solr_search\Form\SearchForm', $server, $keyword);
    // Display result if keyword is defined.
    if (!empty($keyword)) {
      $url_components = custom_solr_search_get_url_components();
      foreach ($url_components['facet_query'] as $filter) {
        $keyword .= ' AND ' . urldecode($filter);
      }
      // Get search results from solr core.
      if ($server == 'all') {
        $results = \Drupal::service('custom_solr_search.search_all')->seachAll($keyword);
      }
      else {
        $results = \Drupal::service('custom_solr_search.search')->basicSearch($keyword, 0, 100, $server);
      }
      $render['result']['#attached']['library'][] = 'core/drupal.dialog.ajax';
      // Format result to display as table.
      foreach ($results as $result) {//ep($result);
        if (isset($result->title)) {
          $title = $result->title;
        }
        else {
          $title = $result->label;
        }
        $render['result'][] = array(
          '#theme' => 'custom_solr_search_result',
          '#url' => isset($result->url[0])?$result->url[0]: '',
          '#title' => isset($title)?$title: '',
          '#author' => isset($result->author)?implode(', ', $result->author): '',
          '#publishDate' => isset($result->publishDate)?implode(', ', $result->publishDate): '',
          '#publisher' => isset($result->publisher)?implode(', ', $result->publisher): '',
          '#topic' => isset($result->topic)?implode(', ', $result->topic): '',
          '#docid' => isset($result->id)?$result->id: '',
          '#server' => $server,
          '#keyword' => $keyword,
          '#base_url' => $base_url,
          '#annotation'=> isset($result->annotation)?implode(', ', $result->annotation): '',
        );
      }
    }
//    print_r(count($results));
    return $render;
  }

}
