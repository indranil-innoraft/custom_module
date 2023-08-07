<?php

namespace Drupal\custom_color\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Color;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;


/**
 * Defines the 'custom_color_hex' field widget.
 *
 * @FieldWidget(
 *   id = "custom_color_hex",
 *   label = @Translation("hex"),
 *   field_types = {"new_color"},
 * )
 */
class HexWidget extends CustomWidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $current_user_roles = $this->currentUser->getRoles();
    if (in_array('administrator', $current_user_roles)) {
      $element['new_color'] = [
        '#type' => 'textfield',
        '#title' => 'Hex',
        "#description" => 'HEX value will be #somehexcode format.',
        '#default_value' => isset($items[$delta]->new_color) ?? NULL,
        '#element_validate' => [
          [$this, 'validate'],
        ],
      ];
    }

    return $element;
  }

  /**
   * Validate the color field.
   *
   * @param array $element
   *   Element to be randerd.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   It contains form value.
   *
   * @return void
   *   Call the function to save data in the database.
   */
  public function validate($element, FormStateInterface $form_state) {
    $value = $element['#value'];
    if (strlen($value) === 0) {
      $form_state->setValueForElement($element, '');
      return;
    }
    if (!Color::validateHex($value)) {
      $form_state->setError($element, $this->t('Color must be a 3- or 6-digit hexadecimal value.'));
    }
  }

}
