<?php

namespace Drupal\nvli_annotation_services\Plugin\rest\resource;

use Drupal\Core\Database\Database;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;
use Solarium\QueryType\Update;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "add_annotation_rest_resource",
 *   label = @Translation("Add annotation rest resource"),
 *   uri_paths = {
 *     "canonical" = "/nvli/add-annotation"
 *   }
 * )
 */
class AddAnnotationRestResource extends ResourceBase {

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
      $container->get('logger.factory')->get('nvli_annotation_services'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $result = $this->load_entity_range($_GET['offset'], $_GET['limit']);
    return new ResourceResponse($result);
  }

  protected function load_entity_range($offset, $limit) {
    $connection = Database::getConnection();

    $query = $connection->select('node', 'n');
      $query->join('node__field_solr_docid', 'sid', 'n.nid=sid.entity_id');
      $query->fields('n', array('nid'));
      $query->fields('sid', array('field_solr_docid_value'));
      $query->range($offset, $limit);
    $reccords = $query->execute()->fetchAll();

    $success = $fail = $exist = 0;
    $message = '';
    foreach ($reccords as $reccord){
      $server = 'nvli';//isset($entity->get('server')->value)?$entity->get('server')->value: 'solr';
      $id = $reccord->field_solr_docid_value;
      $fields = array();
      $query = $connection->select('annotation_store_entity', 'ae')
        ->fields('ae', array('id'));
       $query->condition('resource_ref', $reccord->nid);
      $data = $query->execute()->fetchAll();
      $value = array();
      foreach ($data as $val){
        $value[] = $val->id;
      }

      $entities = \Drupal::entityTypeManager()
        ->getStorage('annotation_store_entity')
        ->loadMultiple($value);
      foreach ($entities as $entity){
        $fields['annotation_key_txt_mv'][] = $entity->id();
        $fields['annotation_txt_mv'][] = $entity->title->value;
        $fields['annotation_type_txt_mv'][] = $entity->type->value;
      }
      $results = \Drupal::service('nvli_annotation_services.add_annotation')
        ->addAnnotation($server, $id, $fields);
      if ($results) {
        $message = $results->getResponse()->getStatusMessage();
      }

      if ($message == 'OK') {
        $success++;
      }
      else {
        $fail++;
      }
    }

    return array('success' => $success, 'fail' => $fail);
  }
}
