<?php

namespace Drupal\nvli_custom\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a 'User Login Link Block' block.
 *
 * @Block(
 *   id = "user_login_link_block",
 *   admin_label = @Translation("User Login Link Block"),
 * )
 */
class UserLoginLinkBlock extends BlockBase {

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
   
    if (\Drupal::currentUser()->isAnonymous()) {
      
      $url = Url::fromUri('internal:/user/login');
      $login_link = Link::fromTextAndUrl(t('Login'), $url)->toString();
      $user_name = '';
    }
    else {
      $url = Url::fromUri('internal:/user/logout');
      $login_link = Link::fromTextAndUrl(t('Log out'), $url)->toString();
      
      $current_user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
      $user_name = $current_user->get('name')->value;
    }
    
    $render[] = array(
      '#theme' => 'user_login_link_block',
      '#login_link' => $login_link,
      '#user_name' => $user_name,
    );
    
    
    return $render;
  }

}
