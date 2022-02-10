<?php

namespace Drupal\migrate_uwfblog\Plugin\migrate\source;

use Drupal\paragraphs\Plugin\migrate\source\d7\FieldableEntity as ParagraphsFieldableEntity;

/**
 * Custom source plugin for Drupal 7 paragraphs.
 *
 * Inspired by:
 *  * https://medium.com/codex/migrating-paragraphs-from-drupal-7-to-drupal-9-ab2170d6dff4
 *  * https://softescu.com/blog/how-migrate-paragraphs-drupal-7-drupal-8
 * D7_paragraph_item source.
 *
 * @MigrateSource(
 *   id = "d7_paragraph_item"
 * )
 */
class ParagraphItem extends ParagraphsFieldableEntity {

  /**
   * @inheritDoc
   */
  public function query() {
    // Select node in its last revision.
    $query = $this->select('paragraphs_item', 'fci')
      ->fields('fci', [
        'item_id',
        'field_name',
        'revision_id',
      ]);
    if (isset($this->configuration['field_name'])) {
      $query->innerJoin('field_data_' . $this->configuration['field_name'], 'fd', 'fd.' . $this->configuration['field_name'] . '_value = fci.item_id');
      $query->fields('fd',
        ['entity_type',
          'bundle',
          'entity_id',
          $this->configuration['field_name'] . '_revision_id',
        ]);
      $query->condition('fci.field_name', $this->configuration['field_name']);

    }

    return $query;
  }


  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // If field specified, get field revision ID so there aren't issues mapping.
    if (isset($this->configuration['field_name'])) {
      $row->setSourceProperty('revision_id', $row->getSourceProperty($this->configuration['field_name'] . '_revision_id'));
    }

    // Get field API field values.
    // the 'branch_point' string will vary.
    // foreach (array_keys($this->getFields('paragraphs_item', 'branch_point')) as $field) {
    foreach (array_keys($this->getFields('paragraphs_item', $row->getSourceProperty('bundle'))) as $field) {

      // get the necessary arguments
      $item_id = $row->getSourceProperty('item_id');
      $revision_id = $row->getSourceProperty('revision_id');

      // set this field in the row...
      $row->setSourceProperty($field, $this->getFieldValues('paragraphs_item', $field, $item_id, $revision_id));
    }
    return parent::prepareRow($row);
  }


  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'item_id' => $this->t('Item ID'),
      'revision_id' => $this->t('Revision ID'),
      'field_name' => $this->t('Name of field'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['item_id']['type'] = 'integer';
    $ids['item_id']['alias'] = 'fci';
    return $ids;
  }


}
