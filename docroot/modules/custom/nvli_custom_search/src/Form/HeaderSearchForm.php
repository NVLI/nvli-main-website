<?php

/**
 * @file
 * Contains \Drupal\nvli_custom_search\Form\HeaderSearchForm.
 */

namespace Drupal\nvli_custom_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\custom_solr_search;

/**
 * Class HeaderSearchForm.
 *
 * @package Drupal\nvli_custom_search\Form
 */
class HeaderSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'new_custom_header_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $keyword = NULL) {
    $form['header_searchbox'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Search'),
      '#default_value' => empty($keyword) ? '' : $keyword,
      '#maxlength' => 64,
      '#size' => 64,
      '#attributes' => array('placeholder' => $this->t('Find Content')),
       '#autocomplete_route_name' => 'custom_solr_search.auto_complete_suggester_AutoCompleteTitle',
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
    if (strlen($form_state->getValue('header_searchbox')) < 3) {
      $form_state->setErrorByName('header_searchbox', $this->t('Please type search keyword.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $search_keyword = $form_state->getValue('header_searchbox');
    $form_state->setRedirect('nvli_custom_search.nvli_search_result', array('keyword' => $search_keyword));
  }

}
