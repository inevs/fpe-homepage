<?php

namespace Drupal\live_score\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
/**
 * Provides a 'LiveScore' Block.
 *
 * @Block(
 *   id = "live_score_block",
 *   admin_label = @Translation("Live Score Block"),
 *   category = @Translation("Live Score"),
 * )
 */
class LiveScoreBlock extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    if (!empty($config['live_score_block_game_id'])) {
      $game_id = $config['live_score_block_game_id'];
    }
    else {
      $game_id = $this->t('565');
    }
    $url = 'https://footballscores.herokuapp.com/games/'.$game_id.'.json';

   $live_score_content = file_get_contents($url);

    return array(
      '#markup' => $this->t('@content', array(
        '@content' => $live_score_content,
      )),
    );
}

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['live_score_block_game_id'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Spiel-ID'),
      '#description' => $this->t('Die ID des Spiels welches angezeigt werden soll'),
      '#default_value' => isset($config['live_score_block_game_id']) ? $config['live_score_block_game_id'] : '100',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['live_score_block_game_id'] = $values['live_score_block_game_id'];
  }
}