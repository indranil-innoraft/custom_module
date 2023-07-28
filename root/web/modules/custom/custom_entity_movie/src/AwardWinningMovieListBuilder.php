<?php

namespace Drupal\custom_entity_movie;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of award winning movies.
 */
class AwardWinningMovieListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['level'] = $this->t('Level');
    $header['year'] = $this->t('Year');
    $header['movie_name'] = $this->t('Movie Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\custom_entity_movie\AwardWinningMovieInterface $entity */
    $row['label'] = $entity->label();
    $row['year'] = $entity->get('year');
    $row['movie_name'] = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->load($entity->get('movie_name'))
      ->toLink();
    return $row + parent::buildRow($entity);
  }

}
