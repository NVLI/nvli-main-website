<?php

namespace Drupal\custom_solr_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\custom_solr_search\SolrServerDetails;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_solr_search\Search;
use Drupal\custom_solr_search\SearchSolrAll;
use Drupal\custom_solr_search\FilterQuerySettings;
use Drupal\Core\Url;
use Drupal\nvli_custom_search\Controller;

/**
 * Provides a 'Result' Block
 *
 * @Block(
 *   id = "custom_solr_search_result_block",
 *   admin_label = @Translation("Custom SOLR Search Result block"),
 * )
 */
class CustomSolrSearchResultBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * \Drupal\custom_solr_search\Search definition.
   *
   * @var \Drupal\custom_solr_search\Search
   */
  protected $search;

  /**
   * \Drupal\custom_solr_search\SolrServerDetails definition.
   *
   * @var \Drupal\custom_solr_search\SolrServerDetails
   */
  protected $serverDetails;

  /**
   * \Drupal\custom_solr_search\Search definition.
   *
   * @var \Drupal\custom_solr_search\SearchSolrAll
   */
  protected $searchall;

  /**
   * Construct.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\custom_solr_search\Search $search
   *   Custom Solr search service.
   * @param \Drupal\custom_solr_search\SolrServerDetails $serverDetails
   *   Custom Solr server details service.
   * @param \Drupal\custom_solr_search\SearchSolrAll $searchall
   *   Custom Solr search service for all core.
   */
  public function __construct(
  array $configuration, $plugin_id, $plugin_definition, FilterQuerySettings $filtertQueryIds, Search $search, SolrServerDetails $serverDetails, SearchSolrAll $searchall
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->filtertQueryIds = $filtertQueryIds;
    $this->search = $search;
    $this->serverDetails = $serverDetails;
    $this->searchall = $searchall;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Get the Filter Query Details.
    $filters = $this->filtertQueryIds->getFilterQuerySetingids();

    // Get the configurations.
    $config = $this->getConfiguration();
    $form['custom_block_filters'] = [
      '#type' => 'select',
      '#title' => $this->t('Select the Filter query Settings'),
      '#options' => $filters,
      '#default_value' => isset($config['custom_block_filters']) ? $config['custom_block_filters'] : '',
    ];
    $form['custom_solr_search_keyword_argument'] = [
      '#type' => 'number',
      '#min' => 0,
      '#title' => $this->t('Solr Search Keyword Argument'),
      '#default_value' => isset($config['custom_solr_search_keyword_argument']) ? $config['custom_solr_search_keyword_argument'] : 0,
      '#description' => $this->t('Add the argument of search keyword to fetch the results.e.g. example.com/result/keyword if keyword is a argument then enter 2.'),
    ];
        $form['custom_solr_search_offset'] = [
      '#type' => 'number',
      '#min' => 0,
      '#title' => $this->t('Solr Search Result Offset'),
      '#default_value' => isset($config['custom_solr_search_offset']) ? $config['custom_solr_search_offset'] : 0,
      '#description' => $this->t('Add the offset to show the search result in block.'),
    ];
    $form['custom_solr_search_limit'] = [
      '#type' => 'number',
      '#min' => 0,
      '#title' => $this->t('Solr Search Result Limit'),
      '#default_value' => isset($config['custom_solr_search_limit']) ? $config['custom_solr_search_limit'] : 5,
      '#description' => $this->t('Add the Limit to show the search result in block.'),
    ];
    $form['custom_solr_search_result_view_more'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show View More Buton on Solr Search Result '),
      '#default_value' => $config['custom_solr_search_result_view_more'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('custom_block_filters', $form_state->getValue('custom_block_filters'));
    $this->setConfigurationValue('custom_solr_search_keyword_argument', $form_state->getValue('custom_solr_search_keyword_argument'));
    $this->setConfigurationValue('custom_solr_search_limit', $form_state->getValue('custom_solr_search_limit'));
    $this->setConfigurationValue('custom_solr_search_offset', $form_state->getValue('custom_solr_search_offset'));
    $this->setConfigurationValue('custom_solr_search_result_view_more', $form_state->getValue('custom_solr_search_result_view_more'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $path = \Drupal::request()->getPathInfo();
    $args = explode('/', $path);

    $config = $this->getConfiguration();
    $filterId = $config['custom_block_filters'];
    $filterQuerySettings = $this->filtertQueryIds->getFilterQueryString($filterId);
    // Get the keyword argument.
    $argument_keyword = $config['custom_solr_search_keyword_argument'];
    $limit = $config['custom_solr_search_limit'];
    $offset = $config['custom_solr_search_offset'];
    $keyword = $args[$argument_keyword];
    $view_more = $config['custom_solr_search_result_view_more'];
    // Check the block configuration and search the results.
    // If selected the core.

    if ($filterQuerySettings['server'] == 'all'){
      $options = $filterQuerySettings['filter'];
      $results = $this->searchall->seachAll($keyword, $offset, $limit, $options);
    }
    else {
      $server = $filterQuerySettings['server'];
      $results = $this->search->basicSearch($keyword, $offset, $limit, $server);
    }
    // Format result to display as unformatted list.
    if (!empty($results)) {
      foreach ($results as $result) {
        if (!empty($result)) {
          if (isset($result->title)) {
            $title = $result->title;
          }
          else {
            $title = $result->label;
          }

          $result_item = array(
            '#theme' => 'custom_solr_search_result',
            '#url' => $result->url[0],
            '#title' => $title,
            '#author' => $result->author_sort,
            '#publishDate' => implode(', ', $result->publishDate),
            '#publisher' => implode(', ', $result->publisher),
            '#topic' => implode(', ', $result->topic)
          );

          $result_items[] = render($result_item);
        }
      }
    }
    $url = Url::fromRoute('nvli_custom_search.nvli_search_resource_keyword_page', array('resource_type' => $filterId, 'keyword' => $keyword));
    $link = \Drupal::l(t('View More'), $url);
    $markup['search_results'] = array(
      '#theme' => 'item_list',
      '#items' => $result_items,
      '#cache' => array(
        'max-age' => 0,
      ),
      '#empty' => t('No search results found!'),
      '#suffix' => !empty($view_more) ? $link : '',
    );

    return $markup;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return array
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
        $configuration, $plugin_id, $plugin_definition, $container->get('custom_solr_search.filter_query_settings') ,$container->get('custom_solr_search.search'), $container->get('custom_solr_search.solr_servers'), $container->get('custom_solr_search.search_all')
    );
  }

}
