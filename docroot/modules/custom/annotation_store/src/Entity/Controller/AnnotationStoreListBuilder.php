<?php

namespace Drupal\annotation_store\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a list controller for annotation_store entity.
 *
 * @ingroup annotation_store
 */
class AnnotationStoreListBuilder extends EntityListBuilder {

  /**
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;
  /**
   * DateFormat for Created and Changed fields.
   */
  protected $dateformat = 'm/d/Y H:i:s';
  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('url_generator')
    );
  }

  /**
   * Constructs a new AnnotationStoreListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, UrlGeneratorInterface $url_generator, $dateformat = '') {
    parent::__construct($entity_type, $storage);
    $this->urlGenerator = $url_generator;
    $this->DateFormat = $dateformat;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getDateFormat() {
    return $this->dateformat;
  }

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = array(
      '#markup' => $this->t('List of annotations', array()),
    );
    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {

    $entity_query = \Drupal::service('entity.query')->get('annotation_store');
    $header = $this->buildHeader();
    $entity_query->pager(20);
    $entity_query->tableSort($header);
    $uids = $entity_query->execute();
    return $this->storage->loadMultiple($uids);
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the contact list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header = array(
      'id' => array(
          'data' => $this->t('ID'),
          'field' => 'id',
          'specifier' => 'id',
      ),
      'text' => array(
          'data' => $this->t('Text'),
          'field' => 'text',
          'specifier' => 'text',
      ),
      'type' => array(
          'data' => $this->t('Type'),
          'field' => 'type',
          'specifier' => 'type',
      ),
      'uri' => array(
          'data' => $this->t('URI'),
          'field' => 'uri',
          'specifier' => 'uri',
      ),
      'author' => array(
          'data' => $this->t('Author'),
          'field' => 'author',
          'specifier' => 'author',
      ),
      'created' => array(
          'data' => $this->t('Created'),
          'field' => 'created',
          'specifier' => 'created',
      ),
      'changed' => array(
          'data' => $this->t('Changed'),
          'field' => 'changed',
          'specifier' => 'changed',
      ),
    );
    return $header;
  }

  /**
   * {@inheritdoc}
   *
   * Construct the row datas for annotation_store.
   *
   * @var config is seperate default form for Date Format as m/d/y with time.
   * @var date_format contains m/d/y with time in created and Changed row.
   */
  public function buildRow(EntityInterface $entity) {
    $link = Url::fromRoute('entity.annotation_store.canonical', array('annotation_store' => $entity->id()));
    $obj = $entity->getOwner();
    $date_format = $this->getDateFormat();
    $row['id'] = $entity->id->value;
    $row['text'] = Link::fromTextAndUrl($entity->text->value, $link);
    $row['type'] = $entity->type->value;
    $row['uri'] = $entity->uri->value;
    $row['user_id'] = Link::fromTextAndUrl($obj->get('name')->value, $link);
    $row['created'] = date($date_format, $entity->created->value);
    $row['changed'] = date($date_format, $entity->changed->value);
    return $row;
  }

}
