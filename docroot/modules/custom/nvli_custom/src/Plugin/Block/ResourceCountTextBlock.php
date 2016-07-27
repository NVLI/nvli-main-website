<?php

namespace Drupal\nvli_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Resource Count text' block.
 *
 * @Block(
 *   id = "resource_count_text",
 *   admin_label = @Translation("Resource Count text"),
 * )
 */
class ResourceCountTextBlock extends BlockBase {

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
    
    // Fetch resource entity count.
    $count = \Drupal::service('nvli_custom.resource_manager')->resourceEntityCount();
    
    // If count is less then 1000 then display static count of 240000.
    if ($count < 1000) {
      $count = 240000;
    }
    
    // Render resource entity count with text wrapper.
    return array(
      '#prefix' => '<p>' . $this->t('Discover a wealth of content') . '<p>',
      '#markup' => $count,
      '#suffix' =>  '<p>' . $this->t('records') . '<p>',
    );
  }

}
