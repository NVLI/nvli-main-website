<?php

namespace Drupal\nvli_resource\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\nvli_resource\Entity\NvliResource;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "create_nvli_rest_resource",
 *   label = @Translation("Create nvli rest resource"),
 *   serialization_class = "Drupal\nvli_resource\Entity\NvliResource",
 *   uri_paths = {
 *     "canonical" = "/nvli/create-resource",
 *     "https://www.drupal.org/link-relations/create"="/nvli/create-resource"
 *   }
 * )
 */
class CreateNvliRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('nvli_resource'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to POST requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data) {
    $field = array();
    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }
    foreach ($data as $key => $val) {
      if ($key == 'field_image') {
        $url = trim($val->value);
        $image = \Drupal::service('nvli_resource.get_external_image')
          ->getExternalImage($url);
        $field['field_image']['target_id'] = $image;
        $field['field_image']['title'] = trim($data->title->value);
        $field['field_image']['alt'] = trim($data->title->value)  ;
      }
      else {
        $field[$key] = trim($val->value);
      }
    }
    $entity = NvliResource::create($field);

    $entity->save();
    return new ResourceResponse($entity);
  }

}
