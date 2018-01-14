<?php

namespace Drupal\instagram_block\Tests\Update;

use Drupal\system\Tests\Update\UpdatePathTestBase;
use Drupal\block\Entity\Block;

/**
 * Tests the update path for moving user id to block configuration.
 *
 * @group Update
 */
class InstagramBlockUpdatePathTest extends UpdatePathTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setDatabaseDumpFiles() {
    $this->databaseDumpFiles = [
      __DIR__ . '/../../../tests/fixtures/update/drupal-8.instagram-block-2778667.site-with-instagram-block.php.gz',
      __DIR__ . '/../../../tests/fixtures/update/drupal-8.instagram-block-2778667.add-block.php',
    ];
  }

  /**
   * Test that after the update user id is now set on the block.
   */
  public function testUpdateMoveUserIds() {
    // Check that there is no user id before update.
    $block_before = Block::load('testuseridmove');
    $settings = $block_before->get('settings');
    $this->assertTrue(!isset($settings['user_id']), 'No user id setting on block before update.');

    $this->runUpdates();

    // Check that the correct user id is now in the block settings.
    $block_after = Block::load('testuseridmove');
    $user_id = $block_after->get('settings')['user_id'];
    $this->assertEqual($user_id, '412345678', 'User id copied to block successfully.');
  }

}
