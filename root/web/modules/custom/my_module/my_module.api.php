<?php

/**
 * @file
 * Hook related to view count.
 */

/**
  * Check the user is viewed a node first time.
  *
  * If user is viwed a node then its show message.
  *
  * @param int $current_count
  *  Number of time a current user view the perticular node.
  *
  * @param \Drupal\node\NodeInterface $node
  *  The node beign viwed.
 */
function hook_node_view_first_time($current_count, \Drupal\node\NodeInterface $node) {
  if ($current_count === 1) {
    \Drupal::Messenger()->addMessage(t('You have visited @node first time.', ['@node' => $node->label()]));
  }
}
