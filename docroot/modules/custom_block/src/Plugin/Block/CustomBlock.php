<?php

namespace Drupal\custom_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Fax' block.
 *
 * @Block(
 *   id = "custom_search_block",
 *   admin_label = @Translation("Custom Search block"),
 * )
 */
class CustomBlock extends BlockBase {

	
/**
 * {@inheritdoc}
 */
public function build() {
  
  return array(
      '#type' => 'markup',
      '#markup' => 'This block list the article.',
    ); 
}


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
   
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
  	
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {

  }

}