<?php

/**
 * @file
 * Contain the settings of the Registration form.
 */

namespace Drupal\form\form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigForm extends ConfigFormBase {

  /**
   * It contains the configuration data.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * It contains the messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * It contains the email validator service.
   *
   * @var \Drupal\Component\Utility\EmailValidatorInterface
   */
  protected $emailValidator;

  /**
   * Construct an configForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *  The configuration.
   * @param MessengerInterface $messenger
   *  The messenger service.
   * @param EmailValidatorInterface $email_validator
   *  The email validator service.
   */
  public function __construct(\Drupal\Core\Config\ConfigFactoryInterface $config_factory, MessengerInterface $messenger, EmailValidatorInterface $email_validator) {
    $this->configFactory = $config_factory;
    $this->messenger = $messenger;
    $this->emailValidator = $email_validator;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('config.factory'),
      $container->get('messenger'),
      $container->get('email.validator'),
    );
  }

  /**
   * Contains the configuration file name.
   */
  private string $CONFIG_FILE_NAME = 'form.settings';

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'form_registration';
  }

  /**
 * {@inheritDoc}
 */
  public function getEditableConfigNames() {
    return [
      $this->CONFIG_FILE_NAME,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#size' => 30,
    ];

    $form['phone'] = [
      '#type' => 'number',
      '#title' => $this->t('Phone Number'),
      '#size' => 30,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Id'),
      '#size' => 30,
    ];

    $form['gender_radio'] = [
      '#type' => 'radios',
      '#title' => $this->t('Gender'),
      '#options' => [
        'Male' => $this->t('Male'),
        'Female' => $this->t('Female'),
        'Other' => $this->t('Other'),
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $phone = $form_state->getValue('phone');
    $email = $form_state->getValue('email');
    $error_message = [];
    if (preg_match('/[^a-zA-Z\s]/', $name)) {
      $error_message['name'] = 'This appear to be that that ' . $name . ' is not valid.';
      $this->displayErrorMessage($error_message, 'name', $form_state);
    }
    if (preg_match('/[^0-9]/', $phone) && strlen($phone)!= 10) {
      $error_message['phone'] = 'This appear to be that that ' . $phone . ' is not valid.';
      $this->displayErrorMessage($error_message, 'phone', $form_state);
    }
    if (!(($this->emailValidator->isValid($email)) && $this->isEmailDomainValid($email))) {
      $error_message['email'] = 'This appear to be that that ' . $email . ' is not valid.';
      $this->displayErrorMessage($error_message, 'email', $form_state);
    }
}

/**
 * To check user email address is in public domain or not.
 *
 * @param string $email
 *
 * @return boolean
 */
public function isEmailDomainValid(string $email) {
  $split = explode('@', $email);
  $domain_name = $split[1];
  $email_public_domain = ['yahoo.com', 'google.com', 'outlook.com'];
  if (in_array($domain_name, $email_public_domain)) {
    return TRUE;
  }
  return FALSE;
}

/**
 * To display error message.
 *
 * @param array $error
 * @param string $key
 * @param FormStateInterface $form_state
 * @return void
 */
public function displayErrorMessage(array &$error, string $key, FormStateInterface $form_state) {
  $form_state->setErrorByName($key, $this->t($error[$key]));
}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $phone = $form_state->getValue('phone');
    $email = $form_state->getValue('email');
    $gender = $form_state->getValue('gender_radio');
    $config = $this->configFactory()->getEditable($this->CONFIG_FILE_NAME);
    $config->set('Name', $name);
    $config->set('Phone', $phone);
    $config->set('Email', $email);
    $config->set('Gender', $gender);
    $config->save();
    $this->messenger->addMessage($this->t('Configuration saved successfully.'));
    parent::submitForm($form, $form_state);
  }
}
