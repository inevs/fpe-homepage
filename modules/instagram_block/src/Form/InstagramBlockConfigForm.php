<?php

namespace Drupal\instagram_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Configure instagram_block settings for this site.
 */
class InstagramBlockConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'instagram_block_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['instagram_block.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get module configuration.
    $config = $this->config('instagram_block.settings');

    $form['authorise'] = array(
      '#markup' => $this->t('Instagram Block requires connecting to a specific Instagram account. You need to be able to log into that account when asked to. The @help page helps with the setup.', array('@help' => Link::fromTextAndUrl($this->t('Authenticate with Instagram'), Url::fromUri('https://www.drupal.org/node/2746185'))->toString())),
    );

    $form['access_token'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Access Token'),
      '#description' => $this->t('Your Instagram access token. Eg. 460786509.ab103e5.a54b6834494643588d4217ee986384a8'),
      '#default_value' => $config->get('access_token'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $access_token = $form_state->getValue('access_token');

    // Get module configuration.
    $this->config('instagram_block.settings')
      ->set('access_token', $access_token)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
