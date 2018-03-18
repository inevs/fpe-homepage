<?php

namespace Drupal\livescore\Controller;

use Drupal\live_score\Utility\DescriptionTemplateTrait;
/**
 */
class LiveScoreController {
  use DescriptionTemplateTrait;

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'livescore';
  }

}
