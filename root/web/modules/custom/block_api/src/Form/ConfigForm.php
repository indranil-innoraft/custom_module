<?php

namespace Drupal\block_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'flagship_program_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames()
  {
    return ['flagship_form.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $config = $this->config('flagship_form.settings');
    $data = $form_state->get('data');
    if (empty($data)) {
      $data = $config->get('data');
      $form_state->set('data', $data);
    }

    $form['table']['#prefix'] = '<div id="flagship-wrapper">';
    $form['table']['#suffix'] = '</div>';

    $form['table']['data'] = [
      '#type' => 'table',
      '#empty' => $this->t('No data available.'),
      '#header' => [
        $this->t('Title'),
        $this->t('Label 1'),
        $this->t('Value 1'),
        $this->t('Label 2'),
        $this->t('Value 2'),
        $this->t('Remove'),
      ],
    ];

    $data = $form_state->get('data');
    foreach ($data as $index => $value) {
      $form['table']['data'][$index]['title'] = [
        '#type' => 'textfield',
        '#default_value' => $value['title'] ?? '',
      ];

      $form['table']['data'][$index]['label1'] = [
        '#type' => 'textfield',
        '#default_value' => $value['label1'] ?? '',
      ];

      $form['table']['data'][$index]['value1'] = [
        '#type' => 'textfield',
        '#default_value' => $value['value1'] ?? '',
      ];

      $form['table']['data'][$index]['label2'] = [
        '#type' => 'textfield',
        '#default_value' => $value['label2'] ?? '',
      ];

      $form['table']['data'][$index]['value2'] = [
        '#type' => 'textfield',
        '#default_value' => $value['value2'] ?? '',
      ];

      $form['table']['data'][$index]['remove'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove This'),
        '#submit' => ['::removeRow'],
        '#attributes' => [
          'row_number' => $index,
        ],
        '#ajax' => [
          'callback' => '::updateCallback',
          'wrapper' => 'flagship-wrapper',
        ],
      ];
    }

    $form['table']['add_one_more'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add One More'),
      '#submit' => ['::addRow'],
      '#ajax' => [
        'callback' => '::updateCallback',
        'wrapper' => 'flagship-wrapper',
      ],
    ];

    $form['#cache'] = [
      'tags' => ['flagship-config'],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Override the form data.
   *
   * @param array $form
   *   This array contains form informations.
   * @param FormStateInterface $form_state
   *   This tracks the form states.
   *
   * @return array
   *
   */
  public function updateCallback(array &$form, FormStateInterface $form_state)
  {
    return $form['table'];
  }

  /**
   * This function add a new tag in the form.
   *
   * @param array $form
   *   This array contains form data.
   * @param FormStateInterface $form_state
   *   This contains the form states.
   *
   * @return void
   *
   */
  public function addRow(array &$form, FormStateInterface $form_state)
  {
    $data = $form_state->get('data');
    $data[] = [
      'title' => '',
      'label1' => '',
      'value1' => '',
      'label2' => '',
      'value2' => '',
    ];
    $form_state->set('data', $data);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Remove a perticular row based on that it will update form.
   *
   * @param array $form
   *   This array contains form data.
   * @param FormStateInterface $form_state
   *   This contains the form states.
   *
   * @return void
   *
   */
  public function removeRow(array &$form, FormStateInterface $form_state)
  {
    $triggering_element = $form_state->getTriggeringElement();
    $row_number = $triggering_element['#attributes']['row_number'];
    $data = $form_state->get('data');
    if (isset($data[$row_number])) {
      unset($data[$row_number]);
      $form_state->set('data', $data);
      if ($form_state->get('data') === []) {
        $this->addRow($form, $form_state);
      }
      $form_state->setRebuild(TRUE);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $config = $this->configFactory->getEditable('flagship_form.settings');
    $data = $form_state->getValue('data');
    $config->set('data', $data)->save();
    \Drupal::service('cache_tags.invalidator')
      ->invalidateTags(['flagship-config']);
    parent::submitForm($form, $form_state);
  }
}
