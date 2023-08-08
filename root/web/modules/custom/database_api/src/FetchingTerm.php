<?php

namespace Drupal\database_api;

use Drupal\Core\Database\Connection;

/**
 * Fetching taxonomy term data.
 */
class FetchingTerm {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a FetchingTerm object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(Connection $connection) {
    $this->connection = $connection;
  }

  /**
   * Fetching taxonomy term used in the content.
   *
   * @param string $query_parameter
   *   Query parameter.
   *
   * @return string
   *   Display the content to user.
   */
  public function fetchData($query_parameter) {
    $data = $this->connection->select('taxonomy_term_field_data', 'd');
    $data->join('taxonomy_term_data', 't', 'd.tid = t.tid');
    $data->join('taxonomy_index', 'index', 'd.tid = index.tid');
    $data->join('node_field_data', 'node_field', 'index.nid = node_field.nid');
    $data->fields('t', ['uuid']);
    $data->fields('node_field', ['title']);
    $data->fields('index', ['nid', 'tid']);
    $data->fields('d', ['name']);
    $data->condition('d.name', $query_parameter, 'like');
    $res = $data->execute()->fetchAll();
    $header = '<h2>Search Reslt For: ' . $res[0]->name . '</h2>';
    $taxonomy_id = '<h4>Term Id: ' . $res[0]->tid . '</h4>';
    $uuid = '<p>UUID - ' . $res[0]->uuid . '</p>';
    $render = $header . $taxonomy_id . $uuid;

    foreach ($res as $r) {
      $render = $render . '<a href=/node/' . $r->nid . ' target=_blank> ' . $r->title . ',' . '</a>';
    }
    return $render;
  }

  /**
   * Check the taxonomy term is available or not.
   *
   * @param string $query_parameter
   *   Contains query parameter.
   *
   * @return int
   *   Contains number of rows.
   */
  public function isTermAvailable(string $query_parameter) {
    $data = $this->connection->select('taxonomy_term_field_data', 'd');
    $data->where('binary d.name = :given_token', ['given_token' => $query_parameter]);
    $data->fields('d', ['name']);
    $number_of_rows = $data->countQuery()->execute()->fetchField();
    return $number_of_rows;
  }

  /**
   * Check if taxonomy term is being current node or not.
   *
   * @param string $query_parameter
   *   Query parameter.
   *
   * @return int
   *   Contains number of rows.
   */
  public function isNodeAvailable(string $query_parameter) {
    $data = $this->connection->select('taxonomy_term_field_data', 'data');
    $data->join('taxonomy_index', 'index', 'index.tid = data.tid');
    $data->where('binary data.name = :given_token', ['given_token' => $query_parameter]);
    $data->fields('data', ['nid']);
    $number_of_rows = $data->countQuery()->execute()->fetchField();
    return $number_of_rows;
  }

  /**
   * Fetching uuid and id for taxonomy term.
   *
   * @param string $query_parameter
   *   Contains query parameter.
   *
   * @return string
   *   Content display to the user.
   */
  public function fetchUUIDAndID(string $query_parameter) {
    $data = $this->connection->select('taxonomy_term_field_data', 'd');
    $data->join('taxonomy_term_data', 't', 'd.tid = t.tid');
    $data->fields('t', ['uuid']);
    $data->fields('d', ['name', 'tid']);
    $data->condition('d.name', $query_parameter, 'like');
    $res = $data->execute()->fetchAll();
    $header = '<h2>Search Reslt For: ' . $res[0]->name . '</h2>';
    $taxonomy_id = '<h4>Term Id: ' . $res[0]->tid . '</h4>';
    $uuid = '<p>UUID - ' . $res[0]->uuid . '</p>';
    $content = '<p>Content is not available.</p>';
    $render = $header . $taxonomy_id . $uuid . $content;
    return $render;
  }

}
