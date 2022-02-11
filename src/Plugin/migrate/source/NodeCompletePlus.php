<?php

namespace Drupal\migrate_uwfblog\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d7\NodeComplete as D7NodeComplete;

/**
 * Custom source plugin for Drupal 7 nodes with some additional data.
 *
 * @MigrateSource(
 *   id = "d7_node_complete_plus",
 *   source_module = "node"
 * )
 */
class NodeCompletePlus extends D7NodeComplete {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();

    // Add the published date if available. Requires the publication_date module.
    if ($this->getDatabase()->schema()->tableExists('publication_date')) {
      $query->leftJoin('publication_date', 'pd', '[nr].[nid] = [pd].[nid]');
      $query->fields('pd', ['published_at']);
    }

    // Add the URL alias if available. Requires the path module.
    if ($this->getDatabase()->schema()->tableExists('url_alias')) {
      $query->leftJoin('url_alias', 'ua', '[ua].[source] = CONCAT(\'node/\', [nr].[nid])');
      $query->addField('ua', 'alias', 'original_alias');
    }
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $additional_fields = [
      'alias' => $this->t('Path alias'),
      'published_at' => $this->t('Publication date'),
    ];
    // return ['alias' => $this->t('Path alias')] + parent::fields();
    return $additional_fields + parent::fields();
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // Prepend the original alias for Drupal 9 compatibility. Couldn't figure
    // out how to do this in the query
    $original_alias = $row->getSourceProperty('original_alias');
    if (!empty($original_alias)) {
      $row->setSourceProperty('alias', '/' . $original_alias);
    }

    return parent::prepareRow($row);
  }



}
