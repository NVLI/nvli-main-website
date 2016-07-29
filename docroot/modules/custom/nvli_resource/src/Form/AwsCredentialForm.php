<?php

namespace Drupal\nvli_resource\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AwsCredentialForm.
 *
 * @package Drupal\nvli_resource\Form
 */
class AwsCredentialForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'nvli_resource.awscredential',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'aws_credential_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('nvli_resource.awscredential');
    $form['awsaccesskeyid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('AWSAccessKeyID'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('awsaccesskeyid'),
    ];
    $form['awssecretaccesskey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('AWSSecretAccessKey'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('awssecretaccesskey'),
    ];
    $form['associatetag'] = [
      '#type' => 'textfield',
      '#title' => $this->t('AssociateTag'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('associatetag'),
    ];
    $form['request_signature'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Request Signature'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('request_signature'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('nvli_resource.awscredential')
      ->set('awsaccesskeyid', $form_state->getValue('awsaccesskeyid'))
      ->set('awssecretaccesskey', $form_state->getValue('awssecretaccesskey'))
      ->set('associatetag', $form_state->getValue('associatetag'))
      ->set('request_signature', $form_state->getValue('request_signature'))
      ->save();
  }

}
