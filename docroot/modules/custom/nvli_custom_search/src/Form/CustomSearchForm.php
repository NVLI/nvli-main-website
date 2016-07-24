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
      '#maxlength' => 64,
      '#size' => 64,
      '#attributes' => array('placeholder' => $this->t('Find articles to explore')),
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
    $path = \Drupal::request()->getPathInfo();
    $type = \Drupal::request()->get('resource_type');
    $keyword = \Drupal::request()->get('keyword');
    // check the url and based on that redirect to path.
    if ($path == '/list/' . $type . '/search' || $path == '/list/' . $type . '/search/'.$keyword) {
      $form_state->setRedirect('nvli_custom_search.nvli_search_resource_keyword_page', array('resource_type' => $type, 'keyword' => $search_keyword));
    }
    else {
      $form_state->setRedirect('nvli_custom_search.nvli_search_result', array('keyword' => $search_keyword));
    }
  }

}
