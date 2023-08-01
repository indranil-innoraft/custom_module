<?php

namespace Drupal\database_api\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\database_api\FetchingTerm;

/**
 * Provides a Database API form.
 */
class TaxonomyTermFinder extends FormBase {

  /**
   * Contains the taxonomy term fetching object.
   *
   * @var \Drupal\database_api\FetchingTerm
   */
  protected $fetchingData;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'database_api_taxonomy_term_finder';
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(FetchingTerm $fetching_term) {
    $this->fetchingData = $fetching_term;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database_api.fetching_term'));
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['taxonomy_term'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Taxonomy Term'),
      '#description' => $this->t('Please write taxonomy term (case sensitive)'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Find'),
      '#suffix' => '<div id="data"></div>',
      '#ajax' => [
        'callback' => '::getTaxonomyDetails',
      ],
    ];

    return $form;
  }

  /**
   * Using ajax show the errors.
   *
   * @param array $form
   *   Contains form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   It contains form state.
   *
   * @return object
   *   render object
   */
  public function getTaxonomyDetails(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $query_parameter = $form_state->getValue('taxonomy_term');
    if ($query_parameter != NULL) {
      if ($this->fetchingData->isTermAvailable($query_parameter) != 0) {
        if ($this->fetchingData->isNodeAvailable($query_parameter) != 0) {
          $data = $this->fetchingData->fetchData($query_parameter);
        }
        else {
          $data = $this->fetchingData->fetchUUIDAndID($query_parameter);
        }
      }
      else {
        $data = 'Taxonomy Term not present.';
      }
      $response->addCommand(new HtmlCommand('#data', $data));
      return $response;
    }
    $response->addCommand(new HtmlCommand('#data', 'Please Insert data.'));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  }

}
