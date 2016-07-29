<?php

namespace Drupal\nvli_annotation_services\Form;


use Drupal\Core\Entity\Entity;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\wizard\WizardPluginBase;
use Drupal\views\Plugin\views\wizard\WizardException;
use Drupal\views\Plugin\ViewsPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class IndexAnnotationForm.
 *
 * @package Drupal\nvli_annotation_services\Form
 */
class IndexAnnotationForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'index_annotation_form';
  }

  /**
   * The wizard plugin manager.
   *
   * @var \Drupal\views\Plugin\ViewsPluginManager
   */
  protected $wizardManager;

  public function __construct(ViewsPluginManager $wizard_manager) {
    $this->wizardManager = $wizard_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.views.wizard')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $wizard_plugins = $this->wizardManager->getDefinitions();
    $options = array();
    foreach ($wizard_plugins as $key => $wizard) {
      $key = preg_replace('/^standard:/', '', $key);
      $options[$key] = $wizard['title'];
    }
    $form['select_type'] = array(
      '#type' => 'select',
      '#title' => $this->t('Select Type'),
      '#description' => $this->t('select content type you wish to index'),
      '#options' => $options,
    );
    $form['index'] = array(
      '#type' => 'submit',
      '#value' => t('Index'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entities = \Drupal::entityTypeManager()
      ->getStorage($form_state->getValue('select_type'))
      ->loadMultiple();
    $batch = array(
      'title' => t('Adding annotation to solr doc'),
      'operations' => array(
        array(
          '\Drupal\nvli_annotation_services\UpdateAnnotation::updateAnnotation',
          array($form_state->getValue('select_type'), $entities)
        ),
      ),
      'finished' => '\Drupal\nvli_annotation_services\UpdateAnnotation::addAnnotationFinishedCallback',
    );

    batch_set($batch);
  }


}
