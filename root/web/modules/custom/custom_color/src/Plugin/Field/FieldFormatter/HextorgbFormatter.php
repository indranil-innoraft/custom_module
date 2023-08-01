<?php

namespace Drupal\custom_color\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Color;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'hexToRgb' formatter.
 *
 * @FieldFormatter(
 *   id = "custom_color_hextorgb",
 *   label = @Translation("RGB"),
 *   field_types = {
 *     "new_color"
 *   }
 * )
 */
class HextorgbFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      if ($item->new_color) {
        $rgb = Color::hexToRgb($item->new_color);
        $element[$delta] = [
          '#markup' => $rgb['red'] . ',' . $rgb['green'] . ',' . $rgb['blue'],
        ];

        $element[$delta] = [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Color :rgb(@red, @green, @blue)',
          [
            '@red' => $rgb['red'],
            '@green' => $rgb['green'],
            '@blue' => $rgb['blue'],
          ]),
          '#attributes' => [
            'style' => 'background-color: ' . $item->new_color . ';',
          ],
        ];

      }
    }

    return $element;
  }

}
