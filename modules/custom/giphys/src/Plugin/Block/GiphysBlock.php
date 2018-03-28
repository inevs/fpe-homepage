<?php

namespace Drupal\giphys\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a Giphys block block type.
 *
 * @Block(
 *   id = "giphys_block",
 *   admin_label = @Translation("Giphys block"),
 *   category = @Translation("Giphys"),
 * )
 */
class GiphysBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $config = $this->getConfiguration();

    $form['url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('url'),
      '#default_value' => 'http://api.giphy.com/v1/gifs/search',
      '#required' => TRUE,
      '#description' => $this->t('api url'),
    ];

    $form['secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('secret'),
      '#default_value' => 'dc6zaTOxFJmzC',
      '#required' => TRUE,
      '#description' => $this->t('api key'),
    ];

    $form['term'] = [
      '#type' => 'textfield',
      '#title' => $this->t('term'),
      '#default_value' => 'trump frog',
      '#required' => FALSE,
      '#description' => $this->t('default search query term or phrase'),
    ];

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('url', $form_state->getValue('url'));
    $this->setConfigurationValue('secret', $form_state->getValue('secret'));
    $this->setConfigurationValue('term', $form_state->getValue('term'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    return array(
      '#theme' => 'giphys',
      '#attached' => array(
        'drupalSettings' => array(
            'giphys' => array(
                'url' => $config['url'],
                'secret' => $config['secret'],
                'term' => $config['term']
            )
        ),
        'library' => array(
          'giphys/giphys',
        ),
      ),
    );

  }
}
