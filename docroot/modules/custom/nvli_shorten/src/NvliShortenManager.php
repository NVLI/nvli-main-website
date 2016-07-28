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
}
