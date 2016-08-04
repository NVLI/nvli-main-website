<?php

namespace Drupal\nvli_annotation_services;

use Symfony\Component\EventDispatcher\Event;

class AnnotationStoreEvent extends Event {

  const SAVE = 'annotation_store.save';
  const DELETE = 'annotation_store.delete';
  protected $referenceSolrDocId;

  protected $solrServer;

  /**
   * AnnotationStoreEvent constructor.
   *
   * @param $referenceSolrDocId
   * @param $solrServer
   */
  public function __construct($referenceSolrDocId, $solrServer)
  {
    $this->referenceSolrDocId = $referenceSolrDocId;
    $this->solrServer = $solrServer;
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

  /**
   * Getter method for referenceSolrDocId.
   *
   * @return mixed
   */
  public function getSolrServer()
  {
    return $this->solrServer;
  }

}
