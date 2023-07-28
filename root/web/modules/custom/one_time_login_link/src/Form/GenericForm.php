<?php

namespace Drupal\one_time_login_link\form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenericForm extends FormBase
{

  /**
   * It contains the users data.
   *
   * @var object
   */
  protected $userStorage;

  /**
   * Stores the current user object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Consturct the class member variables.
   *
   * @param AccountInterface $current_user
   * @param EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $current_user;
    $this->userStorage = $entity_type_manager->getStorage('user');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static($container->get('current_user'), $container->get('entity_type.manager'));
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId()
  {
    return 'one_time_login_link_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['user_id'] = [
      '#type' => 'number',
      '#title' => $this->t('User Id'),
      '#description' => 'Please enter a valid user id to generate link.',
    ];

    $form['actions'] = [
      '#type' => 'submit',
      '#value' => t('Get Link'),
      '#ajax' => [
        'callback' => '::generateOneTimeLoginLink',
      ]
    ];

    $form['status'] = [
      '#type' => 'markup',
      '#markup' => '<p id="link-status"></p>',
    ];
    return $form;
  }

   /**
    * Generate One time login link based on user id.
    *
    * @param array $form
    * @param FormStateInterface $form_state
    * @return object
    */
  public function generateOneTimeLoginLink(array &$form, FormStateInterface $form_state)
  {
    $response = new AjaxResponse();
    $user_id = $form_state->getValue('user_id');
    $account = $this->userStorage->load($user_id);
    $current_user_id = $this->currentUser()->id();
    $flag = FALSE;
    if (is_null($account)) {
      $message = 'User not exists.';
    }
    else if ($current_user_id == $user_id) {
      $message = 'You are already login.';
    }
    else if ($account->isBlocked()) {
      $message = 'This corresponding user are block form this site so the link will
      not genrate';
    }
    else {
      $url = user_pass_reset_url($account) . '/login';
      $flag = TRUE;
    }

    if ($flag) {
      $response->addCommand(new HtmlCommand('#link-status', '<a href=' . $url .
      ' target=_blank>One Time Login Link</a>'));
    } else {
      $response->addCommand(new HtmlCommand('#link-status', $message));
      $response->addCommand(new CssCommand('#link-status', ['color' => 'red']));
    }
    return $response;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    return $form;
  }
}
