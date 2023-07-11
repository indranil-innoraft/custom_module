<?php

namespace Drupal\my_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This is the controller class for my_module.
 */
class FirstController extends ControllerBase {

  /**
   * It contains the current login user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Initilize the current user variable.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static($container->get('current_user'));
  }

  /**
   * To show the welcome message to the user.
   *
   * @return array
   */
  public function getWelcomeMessage() {
    $user_name = $this->currentUser()->getAccountName();

    return [
      '#type' => 'markup',
      '#title' => 'Welcome Block',
      '#markup' => $this->t('Hello @user', ['@user' => $user_name]),
    ];
  }
}

?>
