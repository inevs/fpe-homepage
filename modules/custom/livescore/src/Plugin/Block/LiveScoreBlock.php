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
 *   admin_label = @Translation("LiveScore")
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
      'gameid' => $this->t('100'),
      'update' => $this->t('60'),
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
    $config = $this->getConfiguration();

    $form['game_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Game-ID'),
      '#default_value' => isset($config['gameid']) ? $config['gameid'] : '1',
      '#required' => TRUE,
      '#description' => $this->t('The Game-ID from footballscores.'),
    ];
    $form['home_team'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Home Team'),
      '#default_value' => isset($config['home_team']) ? $config['home_team'] : '2',
      '#required' => TRUE,
      '#description' => $this->t('Das Heimteam'),
    ];
    $form['away_team'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Away Team'),
      '#default_value' => isset($config['away_team']) ? $config['away_team'] : '3',
      '#required' => TRUE,
      '#description' => $this->t('Das Gastteam'),
    ];
    $form['refresh'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Update-Rate'),
      '#default_value' => isset($config['update']) ? $config['update'] : '4',
      '#required' => TRUE,
      '#description' => $this->t('Wie oft wird aktualisiert in Sekunden.'),
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
    $this->setConfigurationValue('gameid', $form_state->getValue('game_id'));
    $this->setConfigurationValue('update', $form_state->getValue('refresh'));
    $this->setConfigurationValue('home_team', $form_state->getValue('home_team'));
    $this->setConfigurationValue('away_team', $form_state->getValue('away_team'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    return array(
      '#theme' => 'livescore_block',
      '#attached' => array(
        'drupalSettings' => array(
            'livescore' => array(
                'gameId' => $config['gameid'],
                'updateRate' => $config['update'],
                'home_team' => $config['home_team'],
                'away_team' => $config['away_team'],
            )
        ),
        'library' => array(
          'livescore/livescore',
        ),
      ),
    );
  }
}
