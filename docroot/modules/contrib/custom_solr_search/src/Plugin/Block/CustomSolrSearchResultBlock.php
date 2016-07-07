<?php

namespace Drupal\custom_solr_search\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\custom_solr_search\SolrServerDetails;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\custom_solr_search\Search;
use Drupal\custom_solr_search\SearchSolrAll;

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
  array $configuration, $plugin_id, $plugin_definition, Search $search, SolrServerDetails $serverDetails, SearchSolrAll $searchall
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->search = $search;
    $this->serverDetails = $serverDetails;
    $this->searchall = $searchall;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Get the Core Details.
    $servers = array('all' => 'ALL');
    $servers += $this->serverDetails->getServers();

    // Get the configurations.
    $config = $this->getConfiguration();

    $form['custom_solr_result_selection'] = [
      '#type' => 'radios',
      '#title' => $this->t('Select the Solr Result Option'),
      '#default_value' => isset($config['custom_solr_result_selection']) ? $config['custom_solr_result_selection'] : '',
      '#options' => array('core' => $this->t('Core'), 'type' => $this->t('Type')),
    ];
    $form['custom_block_servers'] = [
      '#type' => 'select',
      '#title' => $this->t('Select Server Name'),
      '#options' => $servers,
      '#default_value' => isset($config['custom_block_servers']) ? $config['custom_block_servers'] : '',
      '#states' => array(
        'visible' => array(
          ':input[name="settings[custom_solr_result_selection]"]' => array('value' => 'core'),
        ),
      ),
    ];
    $form['custom_block_type_filter'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom Identifier'),
      '#description' => $this->t('Add the multiple filter identifier with AND/OR operator in SOLR based on you can differentiate the blocks.e.g. (format:"Note" OR format:"Article") AND (id: "ir-10054-5936").'),
      '#default_value' => isset($config['custom_block_type_filter']) ? $config['custom_block_type_filter'] : '',
      '#states' => array(
        'visible' => array(
          ':input[name="settings[custom_solr_result_selection]"]' => array('value' => 'type'),
        ),
      ),
    ];
    $form['custom_solr_search_keyword_argument'] = [
      '#type' => 'number',
      '#min' => 0,
      '#title' => $this->t('Solr Search Keyword Argument'),
      '#default_value' => isset($config['custom_solr_search_keyword_argument']) ? $config['custom_solr_search_keyword_argument'] : 0,
      '#description' => $this->t('Add the argument of search keyword to fetch the results.e.g. example.com/result/keyword if keyword is a argument then enter 2.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('custom_block_servers', $form_state->getValue('custom_block_servers'));
    $this->setConfigurationValue('custom_block_type_filter', $form_state->getValue('custom_block_type_filter'));
    $this->setConfigurationValue('custom_solr_result_selection', $form_state->getValue('custom_solr_result_selection'));
    $this->setConfigurationValue('custom_solr_search_keyword_argument', $form_state->getValue('custom_solr_search_keyword_argument'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $path = \Drupal::request()->getPathInfo();
    $args = explode('/', $path);

    $config = $this->getConfiguration();
    // Get the keyword argument.
    $argument_keyword = $config['custom_solr_search_keyword_argument'];
    $keyword = $args[$argument_keyword];
    // Check the block configuration and search the results.
    // If selected the core.
    if ($config['custom_solr_result_selection'] == 'core') {
      $server = $config['custom_block_servers'];
      $results = $this->search->basicSearch($keyword, 0, 5, $server);
    }
    // Select the type.
    else {
      $options = $config['custom_block_type_filter'];
      $results = $this->searchall->seachAll($keyword, $options);
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
            '#theme' => 'custom_solr_search_result_item',
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
    $markup['search_results'] = array(
      '#theme' => 'item_list',
      '#items' => $result_items,
      '#cache' => array(
        'max-age' => 0,
      ),
      '#empty' => t('No search results found!')
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
        $configuration, $plugin_id, $plugin_definition, $container->get('custom_solr_search.search'), $container->get('custom_solr_search.solr_servers'), $container->get('custom_solr_search.search_all')
    );
  }

}
