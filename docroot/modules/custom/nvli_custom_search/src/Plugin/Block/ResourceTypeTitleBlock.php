<?php

/**
 * @file
 * Contains Drupal\nvli_custom_search\Plugin\Block\ResourceTypeTitleBlockBlock.
 */

namespace Drupal\nvli_custom_search\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\custom_solr_search\FilterQuerySettings;
use Drupal\nvli_custom_search\Controller;


/**
 * Provides a 'Resource Type Title' block.
 *
 * @Block(
 *   id = "resource_type_title_block",
 *   admin_label = @Translation("Resource Type Title Block"),
 * )
 */
class ResourceTypeTitleBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function build($resource_type = NULL) {
    // Get the type.
    $filterID = \Drupal::request()->get('resource_type');

    // Fetch the query filter.
    $filterQuerySettings = \Drupal::service('custom_solr_search.filter_query_settings')->getFilterQueryString($filterID);
    $title = $filterQuerySettings['label'];
    $filterID = $filterQuerySettings['id'];
    $render = array(
      '#theme' => 'custom_resource_type_listing',
      '#title' => $title,
      '#resource_id' => $filterID,
      '#resource_link' => NULL,
    );
    return $render;
  }

}
