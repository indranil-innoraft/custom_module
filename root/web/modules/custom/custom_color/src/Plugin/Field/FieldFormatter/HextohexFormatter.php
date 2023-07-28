<?php

namespace Drupal\custom_color\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'hexToHex' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_color_hextohex",
 *   label = @Translation("HEX"),
 *   field_types = {
 *     "new_color"
 *   }
 * )
 */
class HextohexFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Color : @color_code', ['@color_code' => $item->new_color]),
        '#attributes' => [
          'style' => 'background-color: ' . $item->new_color . ';',
        ],
      ];
    }

    return $element;
  }

}
