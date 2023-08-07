<?php

namespace Drupal\events\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Events event subscriber.
 */
class EventsSubscriber implements EventSubscriberInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs event subscriber.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(MessengerInterface $messenger, ConfigFactoryInterface $config_factory) {
    $this->messenger = $messenger;
    $this->configFactory = $config_factory;
  }

  /**
   * Kernel response event handler.
   *
   * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
   *   Response event.
   */
  public function onKernelResponse(ResponseEvent $event) {
    $node = $event->getRequest()->attributes->all()['node'];
    if ($node) {
      $bundle_type = $node->bundle();
      if (strcmp($bundle_type, "movie") == 0) {
        $budget = (double) $this->configFactory
          ->get('custom_entity_movie.settings')
          ->get('budget_friendly_ammout');
        $movie_price = (double) $node->get('field_movie_price')->value;
        if ($movie_price < $budget) {
          $message = 'The movie is under budget';
        }
        elseif ($movie_price > $budget) {
          $message = 'The movie is over budget';
        }
        else {
          $message = 'The movie is within budget';
        }
      }
      $this->messenger->addMessage($message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::RESPONSE => ['onKernelResponse'],
    ];
  }

}
