<?php

namespace Drupal\custom_color\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Color;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the 'custom_color_rgb' field widget.
 *
 * @FieldWidget(
 *   id = "custom_color_rgb",
 *   label = @Translation("rgb"),
 *   field_types = {"new_color"},
 * )
 */
class RgbWidget extends CustomWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $current_user_roles = $this->currentUser->getRoles();
    if (in_array('administrator', $current_user_roles)) {
      $color = $items[$delta]->new_color;
      $rgb = Color::hexToRgb($color);
      $comma_seperated_color = implode(",", $rgb);
      $element['new_color'] = [
        '#type' => 'textfield',
        "#size" => 255,
        '#title' => 'Rgb',
        "#description" => 'RGB value will be r,g,b format.',
        "#required" => TRUE,
        '#element_validate' => [
          [$this, 'validate'],
        ],
        '#default_value' => isset($color) ? $comma_seperated_color : NULL,
      ];
    }

    return $element;
  }

  /**
   * Validate the color text field.
   *
   * @param array $element
   *   Element to be randerd.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   It contains form value.
   *
   * @return void
   *   Call the function to save data in the database.
   */
  public static function validate($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    $regx = '^\(\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\$';
    if (preg_match($regx, $value)) {
      $form_state->setError($element, t("Please provide a valid rgb color."));
      return;
    }
    $color = explode(',', $value);
    $hexCode = Color::rgbToHex($color);
    $form_state->setValueForElement($element, $hexCode);
  }

}
