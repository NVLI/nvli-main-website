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
    $facet_fields = \Drupal::service('custom_solr_search.facet')->filter('solr');
    $build['facet_search'] = array(
      '#theme' => 'custom_solr_search_facet',
      '#facets' => isset($facet_fields) ? (array) $facet_fields : [],
    );
    $build['#cache']['max-age'] = 0;

    return $build;
  }

}
