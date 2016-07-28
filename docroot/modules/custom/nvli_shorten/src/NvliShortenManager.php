<?php

namespace Drupal\nvli_shorten;


use GuzzleHttp\Exception\RequestException;

class NvliShortenManager {

  const API_BASE_URL = 'http://stage-nvli.iitb.ac.in';

  /**
   * @param $url
   * @return \Psr\Http\Message\StreamInterface
   */
  public function generateShortUrl($url) {
    $client = \Drupal::httpClient();

    try {
      $request = $client->post(self::API_BASE_URL . '/api/shorten', [
        'json' => ['url'=> $url]
      ]);

      $data = json_decode($request->getBody());
      return $data;
    }
    catch (RequestException $e) {
      watchdog_exception('nvli_shorten', $e);
    }
  }

  public function setShortUrls() {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'resource')
      ->condition('field_text_plain_single_1.value', '', '=');
    $result = $query->execute();

    if (count($result)) {
      $resources = \Drupal::entityManager()
        ->getStorage('node')
        ->loadMultiple($result);

      foreach ($resources as $resource) {
        if (!empty($resource->get('field_text_plain_single_1')->value)) {
          $resource_id = $resource->id();
          $source_url = 'http://dev-nvli.iitb.ac.in//node/' . $resource_id;
          $short_url = $this->generateShortUrl($source_url);
          $resource->set('field_text_plain_single_1', $short_url->shortUrl);
          $resource->save();
        }
      }
    }
  }
}
