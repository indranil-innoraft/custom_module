<?php

/**
 * @file
 * Contain the settings of the Registration form.
 */

namespace Drupal\form_with_ajax\form;

use Drupal\Component\Utility\EmailValidatorInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ConfigAjaxForm extends ConfigFormBase {

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
   * Contains the configuration file name.
   */
  private string $CONFIG_FILE_NAME = 'form_with_ajax.settings';

  /**
   * Contains All the error messages.
   */
  public $errorMessages = [];

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
  public function __construct(ConfigFactoryInterface $config_factory, MessengerInterface $messenger, EmailValidatorInterface $email_validator)
  {
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
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'form_with_ajax_registration';
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
      '#description' => 'Please enter your full name.',
      '#suffix' => '<span id="name-error" class="error"></span>',
    ];

    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone Number'),
      '#size' => 30,
      '#description' => 'Please provide only 10 digit valid phone number.',
      '#suffix' => '<span id="phone-error" class="error"></span>',
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Id'),
      '#size' => 30,
      '#description' => 'Please provide email address only public domain
      example: google.com, yahoo.com, outlook.com.',
      '#suffix' => '<span id="email-error" class="error"></span>',
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

    $form['actions'] = [
      '#type' => 'submit',
      '#value' => t('Save Configuration'),
      '#ajax' => [
        'callback' => '::validateWithAjax',
      ]
    ];

    $form['status'] = [
      '#type' => 'markup',
      '#markup' => '<span id="status"></span>',
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
  public function validateWithAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    //Removing the previous errors.
    $response->addCommand(new HtmlCommand('.error', ''));
    if (!$this->validate($form_state)) {
      foreach ($this->errorMessages as $key => $value) {
        $response->addCommand(new CssCommand('#' . $key . '-error', ['color' => 'red']));
        $response->addCommand(new HtmlCommand('#' . $key . '-error', $value));
      }
    }
    else {
      $this->configSave($form, $form_state);
      $response->addCommand(new CssCommand('#status', ['color' => 'green']));
      $response->addCommand(new HtmlCommand('#status', t('Configuration save successfully.')));
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
   * To validate user data submitted by the form
   *
   * @param FormStateInterface $form_state
   * @return boolean
   */
  public function validate(FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $phone = $form_state->getValue('phone');
    $email = $form_state->getValue('email');

    if (!$name) {
      $this->errorMessages['name'] = 'Name is required.';
    }
    else if (!preg_match('/^[a-zA-Z ]*$/', $name)) {
      $this->errorMessages['name'] = 'This appear to be that name is not valid.';
    }

    if(!$phone) {
      $this->errorMessages['phone'] = 'Phone number is required.';
    }
    else if (!preg_match('/^[0-9]{10}$/', $phone)) {
      $this->errorMessages['phone'] = 'This appear to be that phone is not valid.';
    }

    if (!$email) {
      $this->errorMessages['email'] = 'Email is required.';
    }
    else if (!(($this->emailValidator->isValid($email)) && $this->isEmailDomainValid($email))) {
      $this->errorMessages['email'] = 'This appear to be that email is not valid.';
    }

    if (count($this->errorMessages) == 0) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * To check user email address is in public domain or not.
   *
   * @param string $email
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
   * Svae the configuration
   *
   * @param array $form
   * @param FormStateInterface $form_state
   * @return void
   */
  public function configSave(array &$form, FormStateInterface $form_state) {
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
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    return $form;
  }
}
