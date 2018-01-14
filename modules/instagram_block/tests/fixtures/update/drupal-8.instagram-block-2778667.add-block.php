<?php

/**
 * @file
 * Contains database additions to add block to Drupal.
 */

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Database\Database;

$connection = Database::getConnection();

// Load the Instagram block from config.
$block_configs[] = Yaml::decode(file_get_contents(__DIR__ . '/block.block.testuseridmove.yml'));

foreach ($block_configs as $block_config) {
  $connection->insert('config')
    ->fields([
      'collection',
      'name',
      'data',
    ])
    ->values([
      'collection' => '',
      'name' => 'block.block.' . $block_config['id'],
      'data' => serialize($block_config),
    ])
    ->execute();
}
