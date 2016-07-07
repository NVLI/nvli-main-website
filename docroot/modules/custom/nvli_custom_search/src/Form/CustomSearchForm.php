<?php

/**
 * @file
 * Contains \Drupal\nvli_custom_search\Form\CustomSearchForm.
 */

namespace Drupal\nvli_custom_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CustomSearchForm.
 *
 * @package Drupal\nvli_custom_search\Form
 */
class CustomSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'new_custom_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $keyword = NULL) {
    $form['custom_searchbox'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Search'),
      '#default_value' => empty($keyword) ? '' : $keyword,
      '#description' => $this->t('Please type the keyword to search.'),
      '#maxlength' => 64,
      '#size' => 64,
    );
    $form['search'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('custom_searchbox')) < 3) {
      $form_state->setErrorByName('custom_searchbox', $this->t('Please type search keyword.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $search_keyword = $form_state->getValue('custom_searchbox');
    $form_state->setRedirect('nvli_custom_search.nvli_search_result', array('keyword' => $search_keyword));
  }

}
