<?php

/**
 * @file
 * Contains \Drupal\nvli_custom\Controller\MiradorViewModeController.
 */

namespace Drupal\nvli_custom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Controller routines for Mirador view mode.
 */
class MiradorViewModeController extends ControllerBase {

  /**
   * Page callback: Shows Mirador view of Images.
   */
  public function showMiradorView($entity_id) {
    $entity = Node::load($entity_id);
    return array();
  }

}
