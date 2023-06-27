<?php

/**
 * @file
 * Contain the settings of the Registration form.
 */

namespace Drupal\form_with_ajax\form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;

class ConfigAjaxForm extends ConfigFormBase {

  /**
   * Contains the configuration file name.
   */
  private string $configFileName = 'form_with_ajax.settings';

  /**
   * Contains All the error messages.
   */
  public $errorMessages = [];

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
      $this->configFileName,
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
      '#suffix' => '<span id="name-error"></span>',
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
    if (!$this->validate($form_state)) {
      foreach ($this->errorMessags as $key => $value) {
        $response->addCommand(new CssCommand('#' . $key . '-error', ['color' => 'red']));
        $response->addCommand(new HtmlCommand('#' . $key . '-error', $value));
      }
    }
    return $response;
  }


  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }


  /**
   * To validate user data submitted by the form
   *
   * @param FormStateInterface $form_state
   * @return array
   */
  public function validate(FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $phone = $form_state->getValue('phone');
    $email = $form_state->getValue('email');
    if (preg_match('/[^a-zA-Z\s]/', $name)) {
      $this->errorMessages['name'] = 'This appear to be that that ' . $name . ' is not valid.';
    }
    if (preg_match('/[^0-9]/', $phone) && strlen($phone)!= 10) {
      $this->errorMessages['phone'] = 'This appear to be that that ' . $phone . ' is not valid.';
    }
    if (!((\Drupal::service('email.validator')->isValid($email)) && $this->isEmailDomainValid($email))) {
      $this->errorMessages['email'] = 'This appear to be that that ' . $email . ' is not valid.';
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
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $name = $form_state->getValue('name');
    $phone = $form_state->getValue('phone');
    $email = $form_state->getValue('email');
    $gender = $form_state->getValue('gender_radio');
    $config = $this->config($this->configFileName);
    $config->set('Name', $name);
    $config->set('Phone', $phone);
    $config->set('Email', $email);
    $config->set('Gender', $gender);
    $config->save();
    \Drupal::messenger()->addMessages(t('Configuration saved successfully.'));
    parent::submitForm($form, $form_state);
    return $form;
  }
}
