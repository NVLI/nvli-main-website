<?php


namespace Drupal\nvli_resource;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
* Provides an interface defining a Nvli Resource entity.
* @ingroup nvli_resource
*/
interface NvliResourceInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
