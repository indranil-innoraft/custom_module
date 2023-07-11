<?php

namespace Drupal\block_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a custom welcome block block.
 *
 * @Block(
 *   id = "block_api_custom_welcome_block",
 *   admin_label = @Translation("Custom Welcome Role Block"),
 *   category = @Translation("Custom")
 * )
 */
class CustomWelcomeBlockBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The dependency to be injected.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new FlagshipBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $my_service
   *   The injected dependency.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AccountInterface $current_user)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
    );
  }

  /**
   * {@inheritdoc}
   */
   public function build() {
    $roles = $this->currentUser->getRoles();
    $comma_seperated_string = implode(',', $roles);
    $build['content'] = [
      '#markup' => $this->t('Welcome @roles', ['@roles' => $comma_seperated_string]),
    ];
    return $build;
  }
}
