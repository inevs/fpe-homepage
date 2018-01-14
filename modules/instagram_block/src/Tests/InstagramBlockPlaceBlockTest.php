<?php

namespace Drupal\instagram_block\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Test the placing of Instagram block blocks.
 *
 * @group instagram_block
 */
class InstagramBlockPlaceBlockTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['instagram_block'];

  /**
   * The block entities used by this test.
   *
   * @var \Drupal\block\BlockInterface[]
   */
  protected $blocks;

  /**
   * A user with permission to administer block placement and Instagram block.
   *
   * @var object
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    // Create and log in an administrative user.
    $this->adminUser = $this->drupalCreateUser(array(
      'administer instagram block',
      'administer blocks',
      'access administration pages',
    ));
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test block admin page exists and functions correctly.
   */
  public function testBlockAdminUiPage() {
    // Visit the blocks admin ui.
    $this->drupalGet('admin/structure/block');

    // Check that there are no blocks in the header region.
    $element = $this->xpath('//tr[contains(@class, :class1) and contains(@class, :class2)]', [
      ':class1' => 'region-header-message',
      ':class2' => 'region-empty',
    ]);
    $this->assertTrue(!empty($element));

    // Place an instagram block in the header region.
    $values = [
      'plugin_id' => 'instagram_block_block',
      'settings' => array('region' => 'header', 'id' => 'instagramblock'),
    ];
    $this->drupalPlaceBlock($values['plugin_id'], $values['settings']);

    // Check that the block was placed successfully.
    $this->drupalGet('admin/structure/block');
    $element = $this->xpath('//tr[contains(@class, :class1) and contains(@class, :class2)]', [
      ':class1' => 'region-header-message',
      ':class2' => 'region-populated',
    ]);
    $this->assertTrue(!empty($element));

    // Test context mapping with valid data.
    $this->drupalGet('admin/structure/block/manage/instagramblock');
    $edit = [
      'settings[user_id]' => '412345678',
      'settings[count]' => '4',
      'settings[width]' => '150',
      'settings[height]' => '150',
      'settings[img_resolution]' => 'thumbnail',
      'settings[cache_time_minutes]' => '1440',
    ];
    $this->drupalPostForm(NULL, $edit, 'Save block');
    $this->assertText('The block configuration has been saved.', 'Block save without errors.');

    // Test user_id with invalid data.
    $edit = [
      'settings[user_id]' => $this->randomString(9),
    ];
    $this->drupalPostForm('admin/structure/block/manage/instagramblock', $edit, 'Save block');
    $this->assertText('User id must be a number.', 'Block failed to save.');

    // Test count with invalid data.
    $edit = [
      'settings[count]' => $this->randomString(4),
    ];
    $this->drupalPostForm('admin/structure/block/manage/instagramblock', $edit, 'Save block');
    $this->assertText('Number of images to display must be a number.', 'Block failed to save.');

    // Test width with invalid data.
    $edit = [
      'settings[width]' => $this->randomString(4),
    ];
    $this->drupalPostForm('admin/structure/block/manage/instagramblock', $edit, 'Save block');
    $this->assertText('Image width in pixels must be a number.', 'Block failed to save.');

    // Test height with invalid data.
    $edit = [
      'settings[height]' => $this->randomString(4),
    ];
    $this->drupalPostForm('admin/structure/block/manage/instagramblock', $edit, 'Save block');
    $this->assertText('Image height in pixels must be a number.', 'Block failed to save.');

    // Test cache_time_minutes with invalid data.
    $edit = [
      'settings[cache_time_minutes]' => $this->randomString(4),
    ];
    $this->drupalPostForm('admin/structure/block/manage/instagramblock', $edit, 'Save block');
    $this->assertText('Cache time in minutes must be a number.', 'Block failed to save.');
  }

  /**
   * Test availability of block in the admin 'Place blocks' list.
   */
  public function testCoreThemeBlockList() {
    \Drupal::service('theme_handler')->install(['bartik', 'seven', 'stark']);
    foreach (['bartik', 'seven', 'stark'] as $theme) {
      $this->drupalGet("admin/structure/block/library/$theme");
      $this->assertText('Instagram block', "Instagram block found in $theme theme block selection.");
    }
  }

}
