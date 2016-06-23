<?php

/**
 * @file
 * Contains \Drupal\custom_search\Controller\NewSearch.
 */

namespace Drupal\custom_search\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class NewSearch.
 *
 * @package Drupal\custom_search\Controller
 */
class NewSearch extends ControllerBase {

  /**
   * Search.
   *
   * @return string
   *   Return Hello string.
   */
  public function search($keyword = NULL) {
    $servers = \Drupal::service('custom_solr_search.solr_servers')->getServers();
    // Search form.
    $render['form'] = $this->formBuilder()->getForm('Drupal\custom_search\Form\CustomSearchForm', $keyword);
  
   // Display result if keyword is defined.
    if (!empty($keyword)) {
      // Get search results from solr core.
      foreach ($servers as $key => $value) {
        $server = $key;
        $results[] = \Drupal::service('custom_solr_search.search')->basicSearch($keyword, 0, 5, $server);
      }
    }

      // Format result to display as unformatedlist.
      $markup = '';
      $count = 0;
      foreach ($results as $result) {
        if (!empty($result)) {
          $markup .= "<ul>Block-$count";
          foreach ($result as $key) {
            $markup .= '<li class="title">Title:' .$key->title.'<br>
                        Authore:' .$key->author_sort.'<br>
                        publishDate:' .implode(', ', $key->publishDate).'<br>
                        publisher:' .implode(', ', $key->publisher).'<br>
                        topic:' .implode(', ', $key->topic).'<br>
                        </li>';
          }
        $count++;
        $markup .= '</ul>';
      }
    }

    $render['form']['custom_message'] = array(
      '#item' => 'markup',
      '#markup' => $markup,
    );
    return $render;
  }
}

