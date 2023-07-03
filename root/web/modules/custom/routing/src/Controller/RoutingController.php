<?php

namespace Drupal\routing\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 *
 * This is the controller class for routing module.
 *
 */
class RoutingController extends ControllerBase {

  /**
   * Simple welcome page markup.
   *
   * @return array
   */
  public function build() {
    return [
      '#type' => 'markup',
      '#markup' => t('Welcome to my drupal website.'),
    ];
  }
}
