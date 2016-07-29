<?php

namespace Drupal\annotation_store\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;
use Drupal\nvli_annotation_services\AnnotationStoreEvent;

/**
 * Form controller for the annotation_store entity edit forms.
 *
 * @ingroup annotation_store
 */
class AnnotationStoreForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\annotation_store\Entity\AnnotationStore */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $entity->getUntranslated()->language()->getId(),
      '#languages' => Language::STATE_ALL,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.annotation_store_entity.collection');
    $entity = $this->getEntity();
    $entity->save();
    $solrServerId = 'solr';
    // Dispatching annotation store save event.
    $dispatcher = \Drupal::service('event_dispatcher');
    $event = new AnnotationStoreEvent($form_state->getValue('resource_ref')[0]['value'], $solrServerId);
    $dispatcher->dispatch(AnnotationStoreEvent::SAVE, $event);
  }
}
