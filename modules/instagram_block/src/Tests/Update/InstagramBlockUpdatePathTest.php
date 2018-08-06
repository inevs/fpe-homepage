<?php

namespace Drupal\instagram_block\Tests\Update;

use Drupal\system\Tests\Update\UpdatePathTestBase;
use Drupal\block\Entity\Block;

/**
 * Tests the update path for moving access token to block configuration.
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
   * Test that after the update access token is now set on the block.
   */
  public function testUpdateMoveAccessTokens() {
    // Check that there is no access token before update.
    $block_before = Block::load('testaccesstokenmove');
    $settings = $block_before->get('settings');
    $this->assertTrue(!isset($settings['access_token']), 'No access token setting on block before update.');

    $this->runUpdates();

    // Check that the correct access token is now in the block settings.
    $block_after = Block::load('testaccesstokenmove');
    $access_token = $block_after->get('settings')['access_token'];
    $this->assertEqual($access_token, '412345678.123ab45.cde678fg901h234ij567klm89nop0123', 'Access token copied to block successfully.');
  }

}
