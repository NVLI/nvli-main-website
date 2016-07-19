<?php

namespace Drupal\open_video_annotation\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'text_link' formatter.
 *
 * @FieldFormatter(
 *   id = "text_link",
 *   label = @Translation("Videojs TextField Formatter"),
 *   field_types = {
 *     "string",
 *     "text"
 *   }
 * )
 */
class TextLinkFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = array();
    foreach ($items as $delta => $item) {
      $get_uri = $item->getValue();
      // Check url is from textfield.
      if (array_key_exists('value', $get_uri)) {
        $elements[$delta] = array(
          '#theme' => 'text_link_formatter',
          '#url' => $get_uri['value'],
        );
      }
    }
    return $elements;
  }

}
