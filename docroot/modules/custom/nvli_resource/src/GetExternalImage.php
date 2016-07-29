<?php

namespace Drupal\nvli_resource;

use Drupal\Core\File\FileSystem;
use Drupal\Component\Utility\Crypt;

/**
 * Class GetExternalImage.
 *
 * @package Drupal\nvli_resource
 */
class GetExternalImage implements GetExternalImageInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * @param $url
   *  require image link.
   *
   * @return int|null|string
   *  file id of image.
   */

  public function getExternalImage($url) {
    $image = file_get_contents($url);
    $file_name = basename($url);
    $file = file_save_data($image, 'public://book_image/' . $file_name);
    return $file->id();
  }

  /**
   * @param $ItemId
   *  AWS item id of the image which needs to be stored.
   *
   * @return int|null|string
   *  file id of image.
   */
  public function getAwsImage($ItemId) {
    $config = \Drupal::config('nvli_resource.awscredential');
    $awsaccesskeyid = $config->get('awsaccesskeyid');
    $awssecretaccesskey = $config->get('awssecretaccesskey');
    $associatetag = $config->get('associatetag');

    // Request_Signature
    $signature = $this->getSignature($awssecretaccesskey);


    $kDate =
    ep($config);
  }

  protected function getSignature($kSecret){
//    Canonical request pseudocode
//
//    CanonicalRequest =
//    HTTPRequestMethod + '\n' +
//    CanonicalURI + '\n' +
//    CanonicalQueryString + '\n' +
//    CanonicalHeaders + '\n' +
//    SignedHeaders + '\n' +
//    HexEncode(Hash(RequestPayload))

    $CanonicalRequest = "GET http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&Operation=ItemLookup&Version=2010-05-08 HTTP/1.1
    Host: webservices.amazonaws.com
    Content-Type: application/x-www-form-urlencoded; charset=utf-8
    X-Amz-Date:".date('YmdThms');




//    Pseudocode for deriving a signing key
//
//    kSecret = Your AWS Secret Access Key
//    kDate = HMAC("AWS4" + kSecret, Date)
//    kRegion = HMAC(kDate, Region)
//    kService = HMAC(kRegion, Service)
//    kSigning = HMAC(kService, "aws4_request")
    $kDate = hash_hmac("sha256", "AWS4".$kSecret, date('Ymd'));
    $kRegion = hash_hmac("sha256", $kDate, 'us-east-1 ');
    $kService = hash_hmac("sha256", $kRegion, 'AWSECommerceService');
    $kSigning = hash_hmac("sha256", $kService, "aws4_request");

    ep($kSigning);exit;

  }
}
