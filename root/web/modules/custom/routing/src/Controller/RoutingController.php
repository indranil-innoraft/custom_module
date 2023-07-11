<?php

namespace Drupal\routing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * This is the controller class for routing module.
 *
 */
class RoutingController extends ControllerBase {

  /**
   * Store user data.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  public function __construct(AccountInterface $current_user) {
    $this->currentUser = $current_user;
  }

  /**
   * Calling the conrainer interface.
   *
   * @param ContainerInterface $container
   *
   * @return mixed
   */
  public static function create(ContainerInterface $container)
  {
    return new static($container->get('current_user'));
  }

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
    $user = $this->currentUser();
    if ($user->hasPermission('access the custom page')) {
      return AccessResult::allowed();
    }
    else {
      return AccessResult::forbidden();
    }
  }

 /**
  * Fetch dynamic parameter form the url.
  *
  * @param integer $value
  * @return array
  */
  public function fetchDynamicParameter(int $value) {
    return [
      '#type' => 'markup',
      '#markup' => t('Campaign value is @value', ['@value' => $value]),
    ];
  }
}
