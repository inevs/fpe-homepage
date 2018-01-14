<?php

namespace Drupal\instagram_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Url;
use Drupal\Component\Serialization\Json;

/**
 * Provides an Instagram block.
 *
 * @Block(
 *   id = "instagram_block_block",
 *   admin_label = @Translation("Instagram block"),
 *   category = @Translation("Social")
 * )
 */
class InstagramBlockBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client to fetch the feed data with.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs a InstagramBlockBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\Client $http_client
   *   The Guzzle HTTP client.
   * @param ConfigFactory $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, Client $http_client, ConfigFactory $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->httpClient = $http_client;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'user_id' => '',
      'count' => 4,
      'width' => 150,
      'height' => 150,
      'img_resolution' => 'thumbnail',
      'cache_time_minutes' => 1440,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['user_id'] = array(
      '#type' => 'number',
      '#title' => $this->t('User id'),
      '#description' => $this->t('The unique Instagram user id of the account to be used for this block. Eg. 460786510'),
      '#default_value' => $this->configuration['user_id'],
    );

    $form['count'] = array(
      '#type' => 'number',
      '#title' => $this->t('Number of images to display'),
      '#default_value' => $this->configuration['count'],
    );

    $form['width'] = array(
      '#type' => 'number',
      '#title' => $this->t('Image width in pixels'),
      '#default_value' => $this->configuration['width'],
    );

    $form['height'] = array(
      '#type' => 'number',
      '#title' => $this->t('Image height in pixels'),
      '#default_value' => $this->configuration['height'],
    );

    $image_options = array(
      'thumbnail' => $this->t('Thumbnail (150x150)'),
      'low_resolution' => $this->t('Low (320x320)'),
      'standard_resolution' => $this->t('Standard (640x640)'),
    );

    $form['img_resolution'] = array(
      '#type' => 'select',
      '#title' => $this->t('Image resolution'),
      '#description' => $this->t('Choose the quality of the images you would like to display.'),
      '#default_value' => $this->configuration['img_resolution'],
      '#options' => $image_options,
    );

    $form['cache_time_minutes'] = array(
      '#type' => 'number',
      '#title' => $this->t('Cache time in minutes'),
      '#description' => $this->t("Default is 1440 - 24 hours. This is important for performance reasons and so the Instagram API limits isn't reached on busy sites."),
      '#default_value' => $this->configuration['cache_time_minutes'],
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    if ($form_state->hasAnyErrors()) {
      return;
    }
    else {
      $this->configuration['count'] = $form_state->getValue('count');
      $this->configuration['width'] = $form_state->getValue('width');
      $this->configuration['height'] = $form_state->getValue('height');
      $this->configuration['img_resolution'] = $form_state->getValue('img_resolution');
      $this->configuration['cache_time_minutes'] = $form_state->getValue('cache_time_minutes');
      $this->configuration['user_id'] = $form_state->getValue('user_id');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Build a render array to return the Instagram Images.
    $build = array();
    $module_config = $this->configFactory->get('instagram_block.settings')->get();

    // If no configuration was saved, don't attempt to build block.
    if (empty($this->configuration['user_id']) || empty($module_config['access_token'])) {
      // @TODO Display a message instructing user to configure module.
      return $build;
    }

    // Build url for http request.
    $uri = "https://api.instagram.com/v1/users/{$this->configuration['user_id']}/media/recent/";
    $options = [
      'query' => [
        'access_token' => $module_config['access_token'],
        'count' => $this->configuration['count'],
      ],
    ];
    $url = Url::fromUri($uri, $options)->toString();

    // Get the instagram images and decode.
    $result = $this->fetchData($url);
    if (!$result) {
      return $build;
    }

    foreach ($result['data'] as $post) {
      $build['children'][$post['id']] = array(
        '#theme' => 'instagram_block_image',
        '#data' => $post,
        '#href' => $post['link'],
        '#src' => $post['images'][$this->configuration['img_resolution']]['url'],
        '#width' => $this->configuration['width'],
        '#height' => $this->configuration['height'],
      );
    }

    // Add css.
    if (!empty($build)) {
      $build['#attached']['library'][] = 'instagram_block/instagram_block';
    }

    // Cache for a day.
    $build['#cache']['keys'] = [
      'block',
      'instagram_block',
      $this->configuration['id'],
      $this->configuration['user_id'],
    ];
    $build['#cache']['context'][] = 'languages:language_content';
    $build['#cache']['max_age'] = $this->configuration['cache_time_minutes'] * 60;

    return $build;
  }

  /**
   * Sends a http request to the Instagram API Server.
   *
   * @param string $url
   *   URL for http request.
   *
   * @return bool|mixed
   *   The encoded response containing the instagram images or FALSE.
   */
  protected function fetchData($url) {
    try {
      $response = $this->httpClient->get($url, array('headers' => array('Accept' => 'application/json')));
      $data = Json::decode($response->getBody());
      if (empty($data)) {
        return FALSE;
      }

      return $data;
    }
    catch (RequestException $e) {
      return FALSE;
    }
  }

}
