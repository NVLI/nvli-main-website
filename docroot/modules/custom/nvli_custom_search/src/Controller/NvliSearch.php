<?php

/**
 * @file
 * Contains \Drupal\nvli_custom_search\Controller\NvliSearch.
 */

namespace Drupal\nvli_custom_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\custom_solr_search\FilterQuerySettings;
use Drupal\custom_solr_search\SearchSolrAll;
use Drupal\Custom_solr_search;
use Drupal\Core\Path;

/**
 * Class NvliSearch.
 *
 * @package Drupal\nvli_custom_search\Controller
 */
class NvliSearch extends ControllerBase {

  /**
   * Search.
   *
   * @return form
   *   Return Search Form.
   */
  public function search($keyword = NULL) {
    // Search form.
    $render['form'] = $this->formBuilder()->getForm('Drupal\nvli_custom_search\Form\CustomSearchForm', $keyword);
    return $render;
  }

  /**
   * Search Result Page.
   *
   * @return form
   *   Return Search Form with result.
   */
  public function search_page($resource_type = NULL, $keyword = NULL) {
    $filterId = $resource_type;
    $filterQuerySettings = \Drupal::service('custom_solr_search.filter_query_settings')->getFilterQueryString($filterId);
    // Check the block configuration and search the results.
    // If selected the core.
    if ($filterQuerySettings['server'] == 'all'){
      $options = $filterQuerySettings['filter'];
      $results = \Drupal::service('custom_solr_search.search_all')->seachAll($keyword, 0, 5, $options);
    }
    else {
      $server = $filterQuerySettings['server'];
      $results = \Drupal::service('custom_solr_search.search')->basicSearch($keyword, 0, 5, $server);
    }
    // Format result to display as unformatted list.
    if (!empty($results)) {
      foreach ($results as $result) {
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
    // Search form.
    $markup['form'] = $this->formBuilder()->getForm('Drupal\nvli_custom_search\Form\CustomSearchForm', $keyword);
    $markup['search_results'] = array(
      '#theme' => 'item_list',
      '#items' => $render['result'],
      '#cache' => array(
        'max-age' => 0,
      ),
      '#empty' => t('No search results found!'),
    );
    return $markup;
  }

}
