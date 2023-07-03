<?php

namespace Drupal\routing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\user\Entity\User;
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

  /**
   * Custom access checking for current user.
   *
   * @return object
   */
  public function accessCheck() {
    $user = User::load(\Drupal::currentUser()->id());
    if ($user->hasPermission('access the custom page')) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }
}
