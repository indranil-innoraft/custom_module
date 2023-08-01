<?php

namespace Drupal\custom_color\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'custom_color_picker' field widget.
 *
 * @FieldWidget(
 *   id = "custom_color_picker",
 *   label = @Translation("picker"),
 *   field_types = {"new_color"},
 * )
 */
class PickerWidget extends CustomWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $current_user_roles = $this->currentUser->getRoles();
    if (in_array('administrator', $current_user_roles)) {
      $element['new_color'] = $element + [
        '#type' => 'color',
        '#title' => 'Picker',
        "#description" => 'Choose your desire color.',
        '#default_value' => isset($items[$delta]->new_color) ?? NULL,
      ];
    }

    return $element;
  }

}
