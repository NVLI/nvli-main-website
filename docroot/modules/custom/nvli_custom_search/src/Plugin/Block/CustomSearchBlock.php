<?php

/**
 * @file
 * Contains Drupal\nvli_custom_search\Plugin\Block\CustomSearchBlock.
 */

namespace Drupal\nvli_custom_search\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'custom search' block.
 *
 * @Block(
 *   id = "custom_search_block",
 *   admin_label = @Translation("Custom Search Block"),
 * )
 */
class CustomSearchBlock extends BlockBase {

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
    return \Drupal::formBuilder()->getForm('Drupal\nvli_custom_search\Form\CustomSearchForm');
  }

}
