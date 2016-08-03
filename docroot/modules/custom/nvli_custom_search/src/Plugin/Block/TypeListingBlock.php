<?php

/**
 * @file
 * Contains Drupal\nvli_custom_search\Plugin\Block\TypeListingBlock.
 */

namespace Drupal\nvli_custom_search\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\custom_solr_search\FilterQuerySettings;
use Drupal\Core\Link;
use Drupal\nvli_custom_search\Controller;
use Drupal\Core\Url;
use Drupal\nvli_custom;
Usr Drupal\Core\Entity;

/**
 * Provides a 'Resource Type Listing' block.
 *
 * @Block(
 *   id = "resource_type_listing_block",
 *   admin_label = @Translation("Resource Type Listing Block"),
 * )
 */
class TypeListingBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get the keyword.
    $keyword = \Drupal::request()->get('keyword');
    // Fetch the query filter.
    $filterQuerySettings = \Drupal::service('custom_solr_search.filter_query_settings')->getFilterQuerySetings();
    // If resource type are present.
    if (!empty($filterQuerySettings)) {
      foreach ($filterQuerySettings as $key) {
        $title = $key['label'];
        $filterID = $key['id'];
        // If keyword is empty rounting path will be different.
        if (empty($keyword)) {
          $url = Url::fromRoute('nvli_custom_search.nvli_search_resource_page', array('resource_type' => $filterID));
          $render[] = array(
            '#theme' => 'custom_resource_type_listing',
            '#title' => $title,
            '#resource_id' => $filterID,
            '#resource_link' => $url,
            '#cache' => array(
              'max-age' => 0,
            ),
          );
        }
        else {
          $url = Url::fromRoute('nvli_custom_search.nvli_search_resource_keyword_page', array('resource_type' => $filterID, 'keyword' => $keyword));
          $render[] = array(
            '#theme' => 'custom_resource_type_listing',
            '#title' => $title,
            '#resource_id' => $filterID,
            '#resource_link' => $url,
            '#cache' => array(
              'max-age' => 0,
            ),
          );
        }
      }
    }

    return $render;
  }

}
