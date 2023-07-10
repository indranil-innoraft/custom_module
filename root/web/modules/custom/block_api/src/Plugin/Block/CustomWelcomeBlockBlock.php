<?php

namespace Drupal\block_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\user\Entity\User;

/**
 * Provides a custom welcome block block.
 *
 * @Block(
 *   id = "block_api_custom_welcome_block",
 *   admin_label = @Translation("Custom Welcome Role Block"),
 *   category = @Translation("Custom")
 * )
 */
class CustomWelcomeBlockBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $current_user = User::load(\Drupal::currentUser()->id());
    $roles = $current_user->getRoles();
    $comma_seperated_string = implode(',', $roles);
    $build['content'] = [
      '#markup' => $this->t('Welcome @roles', ['@roles' => $comma_seperated_string]),
    ];
    return $build;
  }
}
