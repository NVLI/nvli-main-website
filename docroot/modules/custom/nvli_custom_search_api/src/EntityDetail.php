<?php

namespace Drupal\nvli_custom_search_api;

/**
 * Class EntityDetail.
 *
 * @package Drupal\nvli_custom_search_api
 */
class EntityDetail {

  /**
   *
   * @param type $doc_id
   *   Document Id.
   * @return array
   *   Entity Id.
   */
  public function get_nid($doc_id) {
    $nid = \Drupal::database()->select('node__field_solr_docid', 'n')
            ->fields('n', array('entity_id'))
            ->condition('field_solr_docid_value', $doc_id, '=')
            ->execute()->fetchField();

    $entity_id = !empty($nid) ? $nid : [];

    // If entity is present.
    if (!empty($entity_id)) {
      // We get the node storage object.
      $node_storage = \Drupal::EntityTypeManager()->getStorage('node');
      $node = $node_storage->load($entity_id);
      $title = $node->get('title')->value;
      $language = $node->get('field_language')->value;
      $rating = $node->get('field_rating')->rating;
      $harvest_type = $node->get('field_term_ref_harvest_type')->target_id;
      $harvest_name = $this->get_term_name($harvest_type);
      $resource_type = $node->get('field_term_ref_resource_type')->target_id;
      $resource_name = $this->get_term_name($resource_type);
      $tag = $node->get('field_term_ref_tags')->getValue();
      foreach ($tag as $key) {
        $tags = $key['target_id'];
        $tag_name[] = $this->get_term_name($tags);
      }
      $short_url = $node->get('field_text_plain_single_1')->value;
      $fid = $node->get('field_thumb')->target_id;
      $file = \Drupal\file\Entity\File::load($fid);
      $path = $file->getFileUri();
      $url = file_create_url($path);
    }
    $results = array(
      'entity_id' => !empty($entity_id) ? $entity_id : '',
      'node_title' => !empty($title) ? $title : '',
      'language' => !empty($language) ? $language : '',
      'rating' => !empty($rating) ? $rating : '',
      'source' => !empty($harvest_name) ? $harvest_name : '',
      'type' => !empty($resource_name) ? $resource_name : '',
      'tags' => !empty($tag_name) ? $tag_name : '',
      'short_url' => !empty($short_url) ? $short_url : '',
      'image_url' => !empty($url) ? $url : '',
    );
    return $results;
  }

  /**
   *
   * @param type $id
   *   Term Id.
   * @return string
   *   Term Name.
   */
  public function get_term_name($id) {
    $term_name = \Drupal\taxonomy\Entity\Term::load($id)->get('name')->value;
    return $term_name;
  }

}
