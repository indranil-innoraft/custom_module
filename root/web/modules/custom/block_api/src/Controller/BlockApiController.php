<?php

namespace Drupal\block_api\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Block API routes.
 */
class BlockApiController extends ControllerBase {

  /**
   * Builds the response.
   *
   * @return array
   */
  public function build() {
    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Welcome to our website.'),
    ];

    return $build;
  }
}
