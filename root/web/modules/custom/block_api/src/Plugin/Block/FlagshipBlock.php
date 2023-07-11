<?php

namespace Drupal\block_api\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a flagship block.
 *
 * @Block(
 *   id = "block_api_flagship",
 *   admin_label = @Translation("Flagship"),
 *   category = @Translation("Custom")
 * )
 */
class FlagshipBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The dependency to be injected.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
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
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->configFactory->getEditable('flagship_form.settings');
    $data = $config->get('data');

    return [
      '#theme' => 'flagship_program',
      '#data' => $data,
      '#cache' => [
        'tags' => ['flagship-config'],
      ],
      '#attached' => [
        'library' => [
          'block_api/block_api.flagship_css_libraries',
        ],
      ],
    ];
  }
}
