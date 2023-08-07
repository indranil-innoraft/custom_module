<?php

namespace Drupal\custom_entity_movie\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\custom_entity_movie\AwardWinningMovieInterface;

/**
 * Defines the award winning movie entity type.
 *
 * @ConfigEntityType(
 *   id = "award_winning_movie",
 *   label = @Translation("Award Winning Movie"),
 *   label_collection = @Translation("Award Winning Movies"),
 *   label_singular = @Translation("award winning movie"),
 *   label_plural = @Translation("award winning movies"),
 *   label_count = @PluralTranslation(
 *     singular = "@count award winning movie",
 *     plural = "@count award winning movies",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\custom_entity_movie\AwardWinningMovieListBuilder",
 *     "form" = {
 *       "add" = "Drupal\custom_entity_movie\Form\AwardWinningMovieForm",
 *       "edit" = "Drupal\custom_entity_movie\Form\AwardWinningMovieForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "award_winning_movie",
 *   admin_permission = "administer award_winning_movie",
 *   links = {
 *     "collection" = "/admin/structure/award-winning-movie",
 *     "add-form" = "/admin/structure/award-winning-movie/add",
 *     "edit-form" = "/admin/structure/award-winning-movie/{award_winning_movie}",
 *     "delete-form" = "/admin/structure/award-winning-movie/{award_winning_movie}/delete"
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "movie_name" = "movie_name",
 *     "year" = "year",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "year",
 *     "movie_name"
 *   }
 * )
 */
class AwardWinningMovie extends ConfigEntityBase implements AwardWinningMovieInterface {

  /**
   * Contains the unique id.
   *
   * @var int
   */
  protected $id;

  /**
   * Year of award winning.
   *
   * @var string
   */
  protected $year;

  /**
   * Movie name.
   *
   * @var string
   */
  protected $movie_name;

}
