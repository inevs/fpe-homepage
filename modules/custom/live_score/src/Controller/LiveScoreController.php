<?php

namespace Drupal\live_score\Controller;

use Drupal\Core\Controller\ControllerBase;

class LiveScoreController extends ControllerBase {

  /**
   * Display the markup.
   *
   * @return array
   */
  public function content() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello, World!'),
    ];
  }

}