<?php

namespace Drupal\custom_solr_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'FacetBlock' block.
 *
 * @Block(
 *  id = "facet_block",
 *  admin_label = @Translation("Facet search"),
 * )
 */
class FacetBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#cache']['max-age'] = 0;
    $url_path = custom_solr_search_get_url_components('path');
    $url_path = explode('/', $url_path);
    array_pop($url_path);
    $solr_core = end($url_path);
    $facet_fields = [];
    if ($solr_core == 'all') {
      // TODO: Facets from all server/core.
      $build['facet_search']['#markup'] = '<p>Facet for all server search not supported.</p>';
    }
    else {
      $facet_fields = \Drupal::service('custom_solr_search.facet')->filter($solr_core);
      $build['facet_search'] = array(
        '#theme' => 'custom_solr_search_facet',
        '#facets' => isset($facet_fields) ? (array) $facet_fields : [],
      );
    }

    return $build;
  }

}
