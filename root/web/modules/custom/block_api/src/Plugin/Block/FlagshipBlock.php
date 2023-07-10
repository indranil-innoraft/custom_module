<?php

namespace Drupal\block_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a flagship block.
 *
 * @Block(
 *   id = "block_api_flagship",
 *   admin_label = @Translation("Flagship"),
 *   category = @Translation("Custom")
 * )
 */
class FlagshipBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $configFactory = \Drupal::configFactory();
    $config = $configFactory->get('flagship_form.settings');
    $data = $config->get('data');

    return [
      '#theme' => 'flagship_program',
      '#data' => $data,
      '#cache' => [
        'tags' => ['flagship-config'],
      ],
      '#attached' => [
        'library' => [
          'block_api/block_api.flagship_css_libraries',
        ],
      ],
    ];
  }
}
