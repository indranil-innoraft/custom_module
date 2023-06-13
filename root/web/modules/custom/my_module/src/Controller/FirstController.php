<?php

namespace Drupal\my_module\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * This is the controller class for my_module.
 */
class FirstController extends ControllerBase {

  /**
   * To show the welcome message to the user.
   *
   * @return array
   */
  public function getWelcomeMessage() {
    $user_name = \Drupal::currentUser()->getDisplayName();
    return [
      '#type' => 'markup',
      '#title' => 'Welcome Block',
      '#markup' =>t('Hello @user', ['@user' => $user_name]),
    ];
  }
}

?>
