<?php

namespace Drupal\nvli_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\custom_solr_search\FilterQuerySettings;
use Drupal\nvli_custom_search\Controller;

/**
 * Provides a 'Homepage Resource Listing' block.
 *
 * @Block(
 *   id = "homepage_resource_listing_block",
 *   admin_label = @Translation("Homepage Resource Listing Block"),
 * )
 */
class ResourceListingBlock extends BlockBase {

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
    
    // Fetch the query filter.
    $filterQuerySettings = \Drupal::service('custom_solr_search.filter_query_settings')->getFilterQuerySetings();
   
    // If resource type are present.
    if (!empty($filterQuerySettings)) {
      foreach ($filterQuerySettings as $resource_type => $config_entity) {

        // Generate resource type search page url based on the 
        $url = Url::fromRoute('nvli_custom_search.resource_entity_count', array('resource_type' => $config_entity['id']));

        $render[] = array(
          '#theme' => 'resource_listing_block',
          '#title' => \Drupal::l(t($config_entity['label']), $url),
          '#count' => \Drupal::service('nvli_custom.resource_count')->resourceEntityCount($resource_type),
        );
      }
    }

    return $render;
  }
  
}
