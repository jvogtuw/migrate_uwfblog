<?php

namespace Drupal\migrate_uwfblog\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a map_text_format plugin.
 *
 * This plugin extends the default get plugin for formatted text fields, like a
 * body field. Use it to map source to destination text formats or override all
 * to use a specific format.
 *
 * Available configuration keys:
 * - source: Source property.
 * - default_format: The format to use if the source format doesn't map to a
 *   destination format. Defaults to restricted_html.
 * - format: Text format to use regardless of the source format.
 *
 * Examples:
 *
 * @code
 * process:
 *   bar:
 *     plugin: map_text_format
 *     source: foo
 * @endcode
 *
 * This gets the foo property from the source and attempts to map its text
 * format to a destination text format. If mapping fails, the restricted_html
 * format will be used.
 *
 * @code
 * process:
 *   bar:
 *     plugin: map_text_format
 *     source: foo
 *     default_format: full_html
 * @endcode
 *
 * This gets the foo property from the source and attempts to map its text
 * format to a destination text format. If mapping fails, the full_html format
 * will be used.
 *
 * @code
 * process:
 *   bar:
 *     plugin: map_text_format
 *     source: foo
 *     format: full_html
 * @endcode
 *
 * This gets the foo property from the source and sets its text format to
 * full_html regardless of the source format.
 *
 * @MigrateProcessPlugin(
 *   id = "map_text_format"
 * )
 */
class MapTextFormat extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if (isset($value['format'])) {
      $value['format'] = $this->mapFormat($value['format']);
    }
    return $value;

  }

  /**
   * Maps text formats from source to destination.
   *
   * @param string $source_format
   *   The source text format.
   *
   * @return string
   *   Returns the destination text format.
   */
  protected function mapFormat($source_format = '') {

    if (isset($this->configuration['format'])) {
      return $this->configuration['format'];
    }

    if (!empty($source_format)) {
      $map = [
        'full_html' => 'full_html',
        'filtered_html' => 'standard_html',
      ];
      return $map[$source_format];
    }

    return $this->configuration['default_format'] ?? 'basic_html';
  }

}
