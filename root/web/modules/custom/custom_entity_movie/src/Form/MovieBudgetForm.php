<?php

namespace Drupal\custom_entity_movie\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Movie Entity settings for this site.
 */
class MovieBudgetForm extends ConfigFormBase {

  const CONFIG_NAME = 'custom_entity_movie.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'custom_entity_movie_movie_budget';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [MovieBudgetForm::CONFIG_NAME];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(MovieBudgetForm::CONFIG_NAME);
    $form['budget_friendly_ammout'] = [
      '#type' => 'number',
      '#title' => $this->t('Budget Friendly Movie'),
      '#default_value' => $config->get('budget_friendly_ammout'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $budget = (double) $form_state->getValue('budget_friendly_ammout');
    if (!is_numeric($budget)) {
      $form_state->setErrorByName('budget_friendly_ammout',
        $this->t('The value should be integer.'));
    }
    elseif ($budget < 0) {
      $form_state->setErrorByName('budget_friendly_ammout',
        $this->t('The value should be greater than zero.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $budget = $form_state->getValue('budget_friendly_ammout');
    $this->config(MovieBudgetForm::CONFIG_NAME)
      ->set('budget_friendly_ammout', $budget)
      ->save();
    parent::submitForm($form, $form_state);
  }

}
