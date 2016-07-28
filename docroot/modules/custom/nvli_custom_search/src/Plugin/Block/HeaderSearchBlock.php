<?php

/**
 * @file
 * Contains Drupal\nvli_custom_search\Plugin\Block\HeaderSearchBlock.
 */

namespace Drupal\nvli_custom_search\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'custom header search' block.
 *
 * @Block(
 *   id = "custom_header_search_block",
 *   admin_label = @Translation("Custom Header Search Block"),
 * )
 */
class HeaderSearchBlock extends BlockBase {

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
    $build = [];
    $build = [];
    $build['form'] = \Drupal::formBuilder()->getForm('Drupal\nvli_custom_search\Form\HeaderSearchForm');
    return $build;
  }
}
