<?php

namespace Drupal\livescore\Plugin\Block;

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
 *   id = "livescore",
 *   admin_label = @Translation("LiveScore (BETA)")
 * )
 */
class LiveScoreBlock extends BlockBase {

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
    return [
      'livescore_game_id' => $this->t('100'),
    ];
  }

  /**
   * {@inheritdoc}
   *
   * This method defines form elements for custom block configuration. Standard
   * block configuration fields are added by BlockBase::buildConfigurationForm()
   * (block title and title visibility) and BlockFormController::form() (block
   * visibility settings).
   *
   * @see \Drupal\block\BlockBase::buildConfigurationForm()
   * @see \Drupal\block\BlockFormController::form()
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['livescore_game_id_text'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Game-ID'),
      '#description' => $this->t('The Game-ID from footballscores.'),
      '#default_value' => $this->configuration['livescore_game_id'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * This method processes the blockForm() form fields when the block
   * configuration form is submitted.
   *
   * The blockValidate() method can be used to validate the form submission.
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['livescore_game_id']
      = $form_state->getValue('livescore_game_id_text');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $data = &drupal_static(__FUNCTION__);
    $cid = 'livescore:' . \Drupal::languageManager()->getCurrentLanguage()->getId();

    $config = $this->getConfiguration();
    if (!empty($config['livescore_game_id'])) {
      $game_id = $config['livescore_game_id'];
    }
    else {
      $game_id = $this->t('100');
    }
    $url = 'https://footballscores.herokuapp.com/games/'.$game_id.'.json';

    // if ($cache = \Drupal::cache()->get($cid)) {
    //   $data = $cache->data;
    // }
    // else {
      $data = $this->getDataFrom($url);
    //   \Drupal::cache()->set($cid, $data);
    // }

    $build = [];
    $block = [
      '#theme' => 'livescore_block',
      '#gamedata' => $data,
    ];

    $build['livescore'] = $block;
    return $build;
  }

  function getDataFrom($url) {
    $jsondata = '';
    $client = \Drupal::httpClient();
    try {
      $response = $client->get($url);
      $code = $response->getStatusCode();
      if ($code == 200 ) {
        $jsondata = $response->getBody();
      }
    }
    catch (TransferException $e) {
    }

    $data = json_decode($jsondata, true);
    return $data;
  }

}
