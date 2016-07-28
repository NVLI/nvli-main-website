<?php


namespace Drupal\annotation_store;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
* Provides an interface defining a Annotation Store entity.
* @ingroup annotation_store
*/
interface AnnotationStoreInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
