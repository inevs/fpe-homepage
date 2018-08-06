<?php

/**
 * @file
 * Post-update functions for Instagram Block.
 */

/**
 * @addtogroup updates-8.2.x-beta1-to-8.2.x-beta2
 * @{
 */

/**
 * Moving access_token from global configuration to Block Configuration.
 */
function instagram_block_post_update_move_access_token() {
  $config = \Drupal::configFactory()->getEditable('instagram_block.settings');
  $access_token = $config->get('access_token');

  if ($access_token == NULL) {
    return;
  }

  $ids = \Drupal::entityQuery('block')
    ->condition('plugin', 'instagram_block_block')
    ->execute();

  foreach ($ids as $id) {
    // Migrating configuration to the block.
    $block_config = \Drupal::configFactory()->getEditable('block.block.' . $id);
    $settings = $block_config->get('settings');
    $settings['access_token'] = $access_token;
    $block_config->set('settings', $settings);
    $block_config->save();
  }

  // Removing configuration file.
  $config->delete();
}

/**
 * @} End of "addtogroup updates-8.2.x-beta1-to-8.2.x-beta2".
 */
