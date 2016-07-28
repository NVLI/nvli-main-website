<?php

namespace Drupal\nvli_annotation_services;

use Symfony\Component\EventDispatcher\Event;

class AnnotationStoreEvent extends Event {

  const SAVE = 'annotation_store.save';
  const DELETE = 'annotation_store.delete';
  protected $referenceSolrDocId;

  /**
   * AnnotationStoreEvent constructor.
   *
   * @param $referenceSolrDocId
   */
  public function __construct($referenceSolrDocId)
  {
    $this->referenceSolrDocId = $referenceSolrDocId;
  }

  /**
   * Getter method for referenceSolrDocId.
   *
   * @return mixed
   */
  public function getReferenceSolrDocId()
  {
    return $this->referenceSolrDocId;
  }
}
