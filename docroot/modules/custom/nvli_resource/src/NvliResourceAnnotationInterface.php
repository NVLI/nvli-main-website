<?php


namespace Drupal\nvli_resource;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
* Provides an interface defining a Nvli Resource Annotation entity.
* @ingroup nvli_resource
*/
interface NvliResourceAnnotationInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
