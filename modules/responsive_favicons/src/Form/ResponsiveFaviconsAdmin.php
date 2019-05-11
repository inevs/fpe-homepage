<?php

namespace Drupal\responsive_favicons\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;

/**
 * Class ResponsiveFaviconsAdmin.
 *
 * @package Drupal\responsive_favicons\Form
 */
class ResponsiveFaviconsAdmin extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'responsive_favicons_admin';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'responsive_favicons.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('responsive_favicons.settings');
    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path to responsive favicon files'),
      '#description' => $this->t('A local file system path where favicon files will be stored. This directory must exist and be writable by Drupal. An attempt will be made to create this directory if it does not already exist.'),
      '#field_prefix' => file_create_url('public://'),
      '#default_value' => $config->get('path'),
    ];
    $form['tags'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Favicon tags'),
      '#description' => $this->t('Paste the code provided by <a href="http://realfavicongenerator.net/" target="_blank">http://realfavicongenerator.net/</a>. Make sure each link is on a separate line. It is fine to paste links with paths like <code>/apple-touch-icon-57x57.png</code> as these will be converted to the correct paths automatically.'),
      '#default_value' => implode(PHP_EOL, $config->get('tags')),
      '#rows' => 16,
    ];
    $form['upload'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload a zip file from realfavicongenerator.net to install'),
      '#description' => $this->t('For example: %filename from your local computer. This only needs to be done once.', ['%filename' => 'favicons.zip']),
    ];
    $form['remove_default'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove default favicon from Drupal'),
      '#description' => $this->t('It is recommended to remove default favicon as it can cause issues'),
      '#default_value' => $config->get('remove_default'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
    * {@inheritdoc}
    */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('responsive_favicons.settings');

    // We want to save tags as an array.
    $tags = explode(PHP_EOL, $form_state->getValue('tags'));
    $tags = array_map('trim', $tags);
    $tags = array_filter($tags);
    $config->set('tags', $tags);

    // Remove trailing slash on responsive_favicons_path.
    $config->set('path', rtrim($form_state->getValue('path')));

    // Checkbox
    $config->set('remove_default', $form_state->getValue('remove_default'));

    // Attempt the upload and extraction of the zip file. This code is largely
    // based on the code in Drupal core.
    //
    // @see UpdateManagerInstall->submitForm().
    $local_cache = NULL;
    if (!empty($_FILES['files']['name']['upload'])) {
      $validators = array('file_validate_extensions' => array(archiver_get_extensions()));
      $field = 'upload';
      if (!($finfo = file_save_upload('upload', $validators, NULL, 0, FILE_EXISTS_REPLACE))) {
        // Failed to upload the file. file_save_upload() calls
        // drupal_set_message() on failure.
        return;
      }
      $local_cache = $finfo->getFileUri();
    }

    // Only execute the below if a file was uploaded.
    if (isset($local_cache)) {
      $directory = $this->extractDirectory();
      try {
        $archive = $this->archiveExtract($local_cache, $directory);
      }
      catch (\Exception $e) {
        \Drupal::messenger()->addStatus($e->getMessage(), 'error');
        return;
      }

      $files = $archive->listContents();
      if (!$files) {
        $form_state->setError($field, t('Provided archive contains no files.'));
        return;
      }

      $destination = 'public://' . $config->get('path');
      file_prepare_directory($destination, FILE_CREATE_DIRECTORY);

      // Copy the files to the correct location.
      $success_count = 0;
      foreach ($files as $file) {
        $success = file_unmanaged_copy($directory . '/' . $file, $destination, FILE_EXISTS_REPLACE);
        $uri = $destination . '/' . $file;
        if ($success) {
          $success_count++;

          // Rewrite the paths of the JSON files.
          if (preg_match('/\.json$/', $file)) {
            $file_contents = file_get_contents(\Drupal::service('file_system')->realpath($uri));
            $find = preg_quote('"\/android-chrome', '/');
            $replace = '"' . str_replace('/', '\/', _responsive_favicons_normalise_path('/android-chrome'));
            $file_contents = preg_replace('/' . $find . '/', $replace, $file_contents);
            file_unmanaged_save_data($file_contents, $uri, FILE_EXISTS_REPLACE);
          }
          // Rewrite the paths of the XML files.
          else if (preg_match('/\.xml$/', $file)) {
            $file_contents = file_get_contents(\Drupal::service('file_system')->realpath($uri));
            $find = preg_quote('"/mstile', '/');
            $replace = '"' . _responsive_favicons_normalise_path('/mstile');
            $file_contents = preg_replace('/' . $find . '/', $replace, $file_contents);
            file_unmanaged_save_data($file_contents, $uri, FILE_EXISTS_REPLACE);
          }
          // Rewrite the paths of the WEBMANIFEST files.
          else if (preg_match('/\.webmanifest$/', $file)) {
            $file_contents = file_get_contents(\Drupal::service('file_system')->realpath($uri));
            $find = preg_quote('"/android-chrome', '/');
            $replace = '"' . _responsive_favicons_normalise_path('/android-chrome');
            $file_contents = preg_replace('/' . $find . '/', $replace, $file_contents);
            file_unmanaged_save_data($file_contents, $uri, FILE_EXISTS_REPLACE);
          }
        }
      }

      if ($success_count > 0) {
        \Drupal::messenger()->addStatus(\Drupal::translation()->formatPlural($success_count, 'Uploaded 1 favicon file successfully.', 'Uploaded @count favicon files successfully.'));
      }
    }

    // Save the settings.
    $config->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Returns a short unique identifier for this Drupal installation.
   *
   * @return
   *   An eight character string uniquely identifying this Drupal installation.
   */
  private function uniqueIdentifier() {
    $id = &drupal_static(__FUNCTION__, '');
    if (empty($id)) {
      $id = substr(hash('sha256', Settings::getHashSalt()), 0, 8);
    }
    return $id;
  }

  /**
   * Returns the directory where responsive favicons archive files should be
   * extracted.
   *
   * @param $create
   *   (optional) Whether to attempt to create the directory if it does not
   *   already exist. Defaults to TRUE.
   *
   * @return
   *   The full path to the temporary directory where responsive favicons fil
   *   archives should be extracted.
   */
  private function extractDirectory($create = TRUE) {
    $directory = &drupal_static(__FUNCTION__, '');
    if (empty($directory)) {
      $directory = 'temporary://responsive-favicons-' . $this->uniqueIdentifier();
      if ($create && !file_exists($directory)) {
        mkdir($directory);
      }
    }
    return $directory;
  }

  /**
   * Unpacks a downloaded archive file.
   *
   * @param string $file
   *   The filename of the archive you wish to extract.
   * @param string $directory
   *   The directory you wish to extract the archive into.
   *
   * @return Archiver
   *   The Archiver object used to extract the archive.
   *
   * @throws \Exception
   */
  private function archiveExtract($file, $directory) {
    $archiver = archiver_get_archiver($file);
    if (!$archiver) {
      throw new \Exception(t('Cannot extract %file, not a valid archive.', array('%file' => $file)));
    }

    if (file_exists($directory)) {
      file_unmanaged_delete_recursive($directory);
    }

    $archiver->extract($directory);
    return $archiver;
  }
}
