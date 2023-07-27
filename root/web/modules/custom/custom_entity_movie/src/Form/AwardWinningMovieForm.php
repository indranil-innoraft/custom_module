<?php

namespace Drupal\custom_entity_movie\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Award Winning Movie form.
 *
 * @property \Drupal\custom_entity_movie\AwardWinningMovieInterface $entity
 */
class AwardWinningMovieForm extends EntityForm {

  /**
   * It contains the entity manager of nodes.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs an ExampleForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entityTypeManager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager->getStorage('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $form = parent::form($form, $form_state);
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#description' => $this->t('Label for the award winning movie.'),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\custom_entity_movie\Entity\AwardWinningMovie::load',
      ],
      '#disabled' => !$this->entity->isNew(),
    ];

    $form['year'] = [
      '#title' => $this->t('Year'),
      '#type' => 'date',
      '#default_value' => $entity->get('year'),
      '#required' => TRUE,
    ];

    $form['movie_name'] = [
      '#type' => 'entity_autocomplete',
      '#title' => t('Movie Name'),
      '#target_type' => 'node',
      '#required' => TRUE,
      '#default_value' => $entity->get('movie_name') ? \Drupal::entityTypeManager()->getStorage('node')->load($entity->get('movie_name')) : '',
      '#selection_settings' => [
        'target_bundles' => ['movie'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
    $message_args = ['%label' => $this->entity->label()];
    $message = $this->entity->isNew()
      ? $this->t('Created new award winning movie %label.', $message_args)
      : $this->t('Updated award winning movie %label.', $message_args);
    $this->messenger()->addStatus($message);
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
  }

}
