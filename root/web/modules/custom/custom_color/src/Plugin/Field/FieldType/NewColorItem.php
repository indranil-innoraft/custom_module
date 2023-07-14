<?php

namespace Drupal\custom_color\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'new_color' field type.
 *
 * @FieldType(
 *   id = "new_color",
 *   label = @Translation("New Custom Color"),
 *   category = @Translation("General"),
 *   default_widget = "custom_color_picker",
 *   default_formatter = "custom_color_hextohex"
 * )
 *
 * @DCG
 * If you are implementing a single value field type you may want to inherit
 * this class form some of the field type classes provided by Drupal core.
 * Check out /core/lib/Drupal/Core/Field/Plugin/Field/FieldType directory for a
 * list of available field type implementations.
 */
class NewColorItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Text value'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {

    $columns = [
      'new_color' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'New color hex code stors here.',
        'length' => 255,
      ],
    ];

    $schema = [
      'columns' => $columns,
      // @DCG Add indexes here if necessary.
    ];

    return $schema;
  }

}
