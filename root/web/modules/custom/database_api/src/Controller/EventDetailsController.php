<?php

namespace Drupal\database_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Database API routes.
 */
class EventDetailsController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The controller constructor.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Builds the response.
   */
  public function build() {

    return [
      '#theme' => 'events_information',
      '#event_yearly' => $this->countEventsYearly(),
      '#event_quaterly' => $this->calculateQuaterEvent(),
      '#types_of_event' => $this->getEventsType(),
      '#cache' => [
        'tags' => ['events'],
      ],
      '#attached' => [
        'library' => [
          'database_api/database_api.event_details_css_libraries',
        ],
      ],
    ];
  }

  /**
   * Count the events yearly.
   *
   * @return array
   *   Array store year information.
   */
  protected function countEventsYearly() {
    $events_date = $this->getEventsDates();
    $date_array = [];
    foreach ($events_date as $event) {
      if (in_array(date("Y", strtotime($event->field_date_value)), $date_array)) {
        $date_array[date("Y", strtotime($event->field_date_value))] = 1;
      }
      else {
        $date_array[date("Y", strtotime($event->field_date_value))]++;
      }
    }
    return $date_array;
  }

  /**
   * Geting event dates form database.
   *
   * @return array
   *   Contains event date.
   */
  protected function getEventsDates() {
    return $this->connection->select('node__field_date', 'date')
      ->fields('date', ['field_date_value'])
      ->execute()
      ->fetchAll();
  }

  /**
   * Get event type.
   *
   * @return array
   *   Contains quater type.
   */
  protected function getEventsType() {
    $result = $this->connection->select('node__field_type');
    $result->addExpression('COUNT(*)', 'event');
    $result->fields('node__field_type', ['field_type_value']);
    $result->groupBy('node__field_type.field_type_value');
    $res = $result->execute()->fetchAll();

    return $res;
  }

  /**
   * Calculating the quater.
   *
   * @return array
   *   Contains quater count.
   */
  protected function calculateQuaterEvent() {
    $events_date = $this->getEventsDates();
    $quater = [];
    foreach ($events_date as $event) {
      $month_number = date("m", strtotime($event->field_date_value));
      if ($month_number >= 1 && $month_number <= 3) {
        $quater['Jan-March']++;
      }
      elseif ($month_number >= 4 && $month_number <= 6) {
        $quater['April-Jun']++;
      }
      elseif ($month_number >= 7 && $month_number <= 9) {
        $quater['July-Sep']++;
      }
      elseif ($month_number >= 10 && $month_number <= 12) {
        $quater['Oct-Nov']++;
      }
    }
    return $quater;
  }

}
