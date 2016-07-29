<?php


namespace Drupal\nvli_resource\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Url;

/**
 * Provides a list controller for nvli_resource entity.
 *
 * @ingroup nvli_resource
 */
class NvliResourceListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['description'] = array(
      '#markup' => $this->t('Solr Entity Example implements a Nvli Resource model. These Resource are fieldable entities. You can manage the fields on the <a href="@adminlink">Nvli Resource admin page</a>.', array(
        '@adminlink' => \Drupal::urlGenerator()->generateFromRoute('nvli_resource.nvli_resource_entity_settings'),
      )),
    );
    $build['table'] = parent::render();
    return $build;
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
    $header['id'] = $this->t('Resource Id');
    $header['title'] = $this->t('Title');
    $header['solr_doc_id'] = $this->t('Solr index doc ID');
    $header['type'] = $this->t('Type');
    $header['format'] = $this->t('Format');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\nvli_resource\Entity\NvliResource */

    $row['id'] = $entity->id();
    $row['title'] = $entity->title->value;
    $row['solr_doc_id'] = $entity->solr_doc_id->value;
    $row['type'] = $entity->type->value;
    $row['format'] = $entity->format->value;
    return $row + parent::buildRow($entity);
  }

}
