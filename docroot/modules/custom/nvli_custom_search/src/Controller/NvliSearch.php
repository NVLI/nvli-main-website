<?php

/**
 * @file
 * Contains \Drupal\nvli_custom_search\Controller\NvliSearch.
 */

namespace Drupal\nvli_custom_search\Controller;

use Drupal\Core\Controller\ControllerBase;

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
   *   Return Search Form.
   */
  public function search_page($resource = NULL) {
    $keyword = \Drupal::request()->get('keyword');

    // Search form.
    $render['form'] = $this->formBuilder()->getForm('Drupal\nvli_custom_search\Form\CustomSearchForm', $keyword);
    return $render;
  }
  
}
