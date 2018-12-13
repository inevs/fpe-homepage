<?php

namespace Drupal\newsletter\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Exception\RequestException;

/**
 *
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "newsletter",
 *   admin_label = @Translation("Newsletter")
 * )
 */
class NewsletterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   *
   * This method sets the block default configuration. This configuration
   * determines the block's behavior when a block is initially placed in a
   * region. Default values for the block configuration form should be added to
   * the configuration array. System default configurations are assembled in
   * BlockBase::__construct() e.g. cache setting and block title visibility.
   *
   * @see \Drupal\block\BlockBase::__construct()
   */
  public function defaultConfiguration() {
    return [];
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    return array(
      '#theme' => 'newsletter_block',
    );
  }
}
