<?php

namespace Drupal\instagram_block\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Test the Instagram block Config Form.
 *
 * @group instagram_block
 */
class InstagramBlockConfigFormTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['instagram_block'];

  /**
   * A user with permission to administer instagram block.
   *
   * @var object
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->adminUser = $this->drupalCreateUser(['administer instagram block']);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests Instagram Block Configuration Form.
   */
  public function testConfigForm() {
    $edit['access_token'] = $this->randomString(20);
    $this->drupalPostForm('admin/config/content/instagram_block', $edit, t('Save configuration'));
    $this->assertText(t('The configuration options have been saved.'), t('Saved configuration'));
  }

}
