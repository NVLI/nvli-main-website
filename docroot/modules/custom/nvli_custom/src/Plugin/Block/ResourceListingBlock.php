<?php

namespace Drupal\nvli_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\custom_solr_search\FilterQuerySettings;
use Drupal\custom_solr_search\Search;
use Drupal\custom_solr_search\SearchSolrAll;
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

    // Validation to check resurce entity exist.
    if (!empty($filterQuerySettings)) {

      // Iterate resource entity array and create renderable array of resource title 
      // and resource link.
      foreach ($filterQuerySettings as $resource_type => $config_entity) {
        $count = '';
        $options = $config_entity['filter'];
        if (!empty($options)) {
          if ($config_entity['server'] == 'all') {
            $results = \Drupal::service('custom_solr_search.search_all')->seachAll($keyword, $offset, $limit, $options);
          }
          else {
            $server = $config_entity['server'];
            $results = \Drupal::service('custom_solr_search.search')->basicSearch($keyword, $offset, $limit, $server, $options);
          }
          $count = $results['total_docs'];
        }
        // Generate resource type search page url based on the 
        $url = Url::fromRoute('nvli_custom_search.nvli_search_resource_page', array('resource_type' => $config_entity['id']));

        $render[] = array(
          '#theme' => 'resource_listing_block',
          '#title' => \Drupal::l(t($config_entity['label']), $url),
          '#count' => !empty($count) ? $count : '0',
          '#resource_id' => $config_entity['id'],
          '#resource_link' => $url,
        );
      }
    }

    return $render;
  }

}
