<?php

/**
 * @file
 * Contains Drupal\nvli_custom_search_api\Plugin\rest\resource.
 */

namespace Drupal\nvli_custom_search_api\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Psr\Log\LoggerInterface;
use Drupal\Core\Entity;
use Drupal\custom_solr_search\SearchSolrAll;
use Drupal\custom_solr_search\Search;
use Symfony\Component\HttpFoundation;
use Drupal\nvli_custom_search_api\EntityDetail;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "custom_rest_resource",
 *   label = @Translation("Custom rest resource"),
 *   uri_paths = {
 *     "canonical" = "/rest/v1/search"

 *   }
 * )
 */
class NvliSearchResource extends ResourceBase {

  /**
   * \Drupal\custom_solr_search\Search definition.
   *
   * @var \Drupal\custom_solr_search\Search
   */
  protected $search;

  /**
   * \Drupal\custom_solr_search\Search definition.
   *
   * @var \Drupal\custom_solr_search\SearchSolrAll
   */
  protected $searchall;

  /**
   * \Drupal\nvli_custom_search_api\Entity ID.
   *
   * @var \Drupal\nvli_custom_search_api\EntityDetail
   */
  protected $entitydetail;

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
   * @param \Drupal\custom_solr_search\Search $search
   *   Custom Solr search service.
   * @param \Drupal\nvli_custom_search_api\EntityDetail $entitydetail
   *   Custom Entity.
   * @param \Drupal\custom_solr_search\SearchSolrAll $searchall
   *   Custom Solr search service for all core.
   */
  public function __construct(
  array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user, Search $search, SearchSolrAll $searchall, EntityDetail $entitydetail) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
    $this->search = $search;
    $this->searchall = $searchall;
    $this->entitydetail = $entitydetail;
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
        $container->get('logger.factory')->get('rest'),
        $container->get('current_user'),
        $container->get('custom_solr_search.search'),
        $container->get('custom_solr_search.search_all'),
        $container->get('nvli_custom_search_api.entity_detail')
    );
  }

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get() {
    // Fetch the query parameters.
    $offset = \Drupal::request()->get('offset');
    $limit = \Drupal::request()->get('limit');
    $keyword = \Drupal::request()->get('keyword');
    $type = \Drupal::request()->get('type');
    // @ TODO once config entity available then get the filter query.
    $options = '(format:"' . $type . '")';

    // If all the parameter are present return the result.
    if ($keyword != '' && $offset != '' && $limit != '') {
      // Call the service to fetch the result from the solr.
      if ($type == '') {
        $solr_result = $this->searchall->seachAll($keyword, $offset, $limit);
      }
      else {
        $solr_result = $this->searchall->seachAll($keyword, $offset, $limit, urldecode($options));
      }
      // If result is not empty then find it's entity id.
      if ($solr_result != '') {
        // Fetch the entity_id for each doc.
        foreach ($solr_result as $row) {
          $doc_id = $row->id;
          $results['resource'] = $this->entitydetail->get_nid($doc_id);
          $results['metadata'] = json_decode(json_encode($row), True);
          $result[] = $results;
        }
      }
      // Check if result not present.
      if (empty($result)) {
        $result = array("success" => FALSE, "message" => 'Search Result not found.');
      }
      else {
        $result = array("success" => TRUE, "result" => $result);
      }
    }
    else {
      $result = array("success" => FALSE, "message" => 'Parameter can not be empty.');
    }
    $response = new ResourceResponse($result);
    $response->addCacheableDependency($result);
    return $response;
  }

}
