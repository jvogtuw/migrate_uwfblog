<?php
namespace Drupal\migrate_uwfblog\Plugin\MediaWysiwyg;

use Drupal\media_migration\MediaWysiwygPluginBase;

/**
 * File Media WYSIWYG plugin.
 *
 * This plugin targets files (not file entities)...I guess. Without it, you get
 * the PluginException error from MigratePluginAlterer->addMediaWysiwygProcessor().
 * The error was:
 * "Could not find a MediaWysiwyg plugin for field 'field_caption' used in
 * source entity type 'file'. You probably need to create a new one. Have a look
 * at 'Drupal\media_migration\Plugin\MediaWysiwyg\Paragraphs' for an example."
 *
 * @MediaWysiwyg(
 *   id = "file",
 *   label = @Translation("File (plain)"),
 *   entity_type_map = {
 *     "file" = "media",
 *   },
 * )
 *
 * @see \Drupal\media_migration\MediaWysiwygInterface
 */
class File extends MediaWysiwygPluginBase {}
