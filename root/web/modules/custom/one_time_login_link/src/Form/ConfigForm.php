<?php

namespace Drupal\one_time_login_link\form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\user\Entity\User;

class ConfigForm extends ConfigFormBase {

  /**
   * Store configuration file name.
   */
  private string $configFileName = 'one_time_login_link.settings';

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'one_time_login_link_form';
  }

  /**
 * {@inheritDoc}
 */
  public function getEditableConfigNames() {
    return [
      $this->configFileName,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
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
   * Using ajax show the errors.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return object
   */
  public function generateOneTimeLoginLink(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $user_id = $form_state->getValue('user_id');
    $account = User::load($user_id);
    $current_user_id = \Drupal::currentUser()->id();
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
      $result = user_pass_reset_url($account) . '/login';
      $flag = TRUE;
    }
    if ($flag) {
      $response->addCommand(new HtmlCommand('#link-status', $result));
      $response->addCommand(new CssCommand('#link-status', ['color' => 'green']));
    }
    else {
      $response->addCommand(new HtmlCommand('#link-status', $message));
      $response->addCommand(new CssCommand('#link-status', ['color' => 'red']));
    }
    return $response;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user_id = $form_state->getValue('user_id');
    $config = $this->config($this->configFileName);
    $config->set('User Id', $user_id);
    $config->save();
    return $form;
  }
}
