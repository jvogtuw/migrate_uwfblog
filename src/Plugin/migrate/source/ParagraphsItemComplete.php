<?php

namespace Drupal\migrate_uwfblog\Plugin\migrate\source;

use Drupal\paragraphs\Plugin\migrate\source\d7\ParagraphsItemRevision;
use Drupal\migrate\Row;

/**
 * Paragraphs Item source plugin. Combines the functionality of ParagraphsItem.php
 * and ParagraphsItemRevision.php to get all revisions at once.
 *
 * Available configuration keys:
 * - bundle: (optional) If supplied, this will only return paragraphs
 *   of that particular type.
 *
 * @MigrateSource(
 *   id = "d7_paragraphs_item_complete",
 *   source_module = "paragraphs",
 * )
 */
class ParagraphsItemComplete extends ParagraphsItemRevision {

  /**
   * Join string between the paragraph_item and paragraph_item_revision tables.
   */
  const JOIN = "p.item_id = pr.item_id";

  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();
    $query->orderBy('pr.revision_id');
    // $query = $this->select('paragraphs_item', 'p')
    //   ->fields('p',
    //     ['item_id',
    //       'bundle',
    //       'field_name',
    //       'archived',
    //     ])
    //   ->fields('pr', ['revision_id']);
    // $query->addField('p', 'revision_id', 'current_revision_id');
    // $query->leftJoin('paragraphs_item_revision', 'pr', static::JOIN);
    //
    // // This configuration item may be set by a deriver to restrict the
    // // bundles retrieved.
    // if ($this->configuration['bundle']) {
    //   $query->condition('p.bundle', $this->configuration['bundle']);
    // }
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // echo implode(', ',$row->getSource());
    // var_dump($row->getSource());
  //
  //   // Get Field API field values.
  //   $item_id = $row->getSourceProperty('item_id');
  //   $revision_id = $row->getSourceProperty('revision_id');
  //
  //   foreach (array_keys($this->getFields('paragraphs_item', $row->getSourceProperty('bundle'))) as $field) {
  //     $row->setSourceProperty($field, $this->getFieldValues('paragraphs_item', $field, $item_id, $revision_id));
  //   }
  //
    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  // public function fields() {
  //   $fields = [
  //     'item_id' => $this->t('The paragraph_item id'),
  //     'revision_id' => $this->t('The paragraph_item revision id'),
  //     'bundle' => $this->t('The paragraph bundle'),
  //     'field_name' => $this->t('The paragraph field_name'),
  //   ];
  //
  //   return $fields;
  // }

  /**
   * {@inheritdoc}
   */
  // public function fields() {
  //   $fields = [
  //     'item_id' => $this->t('Item ID'),
  //     'revision_id' => $this->t('Revision ID'),
  //     // 'field_name' => $this->t('Name of field'),
  //   ];
  //   return $fields;
  // }

  /**
   * {@inheritdoc}
   */
  // public function fields() {
  //   // Use all the node fields plus the vid that identifies the version.
  //   return parent::fields() + [
  //       'revision_id' => t('The primary identifier for this version.'),
  //     ];
  // }
  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids = [
      'item_id' => [
        'type' => 'integer',
        'alias' => 'p',
      ],
      'revision_id' => [
        'type' => 'integer',
        'alias' => 'pr'
      ]
    ];

    return $ids;
  }

}
