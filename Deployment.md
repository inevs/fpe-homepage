## Update auf neuestes Drupal (8.4.4)
 * site in Wartungsmodus setzen
 * download Drupal 8.4.4
 * rename core in _core
 * rename vendor in _vendor
 * upload core and vendor
 * upload composer.json, composer.lock
 * change sites/default/settings.php
   * change file permission to writeable
   * set $settings['update_free_access'] = FALSE; to 'TRUE'
   * upload settings.php
   * call drupal/update.php
 * site Wartungsmodus verlassen
 * alle Caches löschen

## Theme installieren
 * Dateien kopieren

## Installiere Module
 * crop: https://ftp.drupal.org/files/projects/crop-8.x-1.3.tar.gz
 * image_widget_crop: https://ftp.drupal.org/files/projects/image_widget_crop-8.x-2.1.tar.gz
 * ctools: https://ftp.drupal.org/files/projects/ctools-8.x-3.0.tar.gz
 * instagram_block: https://ftp.drupal.org/files/projects/instagram_block-8.x-2.0-beta1.tar.gz
 * token: https://ftp.drupal.org/files/projects/token-8.x-1.1.tar.gz
 * pathauto: https://ftp.drupal.org/files/projects/pathauto-8.x-1.0.tar.gz
 * bg_image_formatter: https://ftp.drupal.org/files/projects/bg_image_formatter-8.x-1.0.tar.gz

## Verknüpfungen löschen (in PROD)
 * unter Verknüpfungen > Verknüpfungen bearbeiten > löschen

## Konfiguration importieren
(Voraussetzung UUID stimmt überein)

 * Export der Konfiguration aus DEV
 * Import der Konfiguration in PROD