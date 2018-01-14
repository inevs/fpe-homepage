<?php

/**
 * @file
 * Post-update functions for Instagram Block.
 */

/**
 * @addtogroup updates-8.2.x-alpha3-to-8.2.x-beta1
 * @{
 */

/**
 * Moving user_id from global configuration to Block Configuration.
 */
function instagram_block_post_update_move_user_ids() {
  $config = \Drupal::configFactory()->getEditable('instagram_block.settings');
  $user_id = $config->get('user_id');

  $ids = \Drupal::entityQuery('block')
    ->condition('plugin', 'instagram_block_block')
    ->execute();

  foreach ($ids as $id) {
    // Migrating configuration to the block.
    $block_config = \Drupal::configFactory()->getEditable('block.block.' . $id);
    $settings = $block_config->get('settings');
    $settings['user_id'] = $user_id;
    $block_config->set('settings', $settings);
    $block_config->save();
  }

  // Removing old configuration.
  $config->clear('user_id');
  $config->save();
}

/**
 * @} End of "addtogroup updates-8.2.x-alpha2-to-8.2.x-beta1".
 */
