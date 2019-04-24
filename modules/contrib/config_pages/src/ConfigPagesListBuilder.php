<?php

namespace Drupal\config_pages;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityListBuilderInterface;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines a class to build a listing of custom config_pages entities.
 *
 * @see \Drupal\config_pages\Entity\ConfigPages
 */
class ConfigPagesListBuilder extends EntityListBuilder implements EntityListBuilderInterface, EntityHandlerInterface {

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container,
                                        EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('entity_type.manager')->getStorage('config_pages_type'),
      $container->get('plugin.manager.config_pages_context')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeInterface $entity_type,
                              EntityStorageInterface $storage,
                              EntityStorageInterface $cpt_storage,
                              ConfigPagesContextManagerInterface $cp_context) {
    parent::__construct($entity_type, $storage);
    $this->cpt_storage = $cpt_storage;
    $this->cp_context = $cp_context;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Name');
    $header['context'] = t('Context');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);

    // Used context.
    $contextData = [];
    if (!empty($entity->context['group'])) {
      foreach ($entity->context['group'] as $context_id => $context_enabled) {
        if ($context_enabled) {
          $item = $this->cp_context->getDefinition($context_id);
          $context_value = $item['label'];
          $contextData[] = $context_value;
        }
      }
    }
    $row['context'] = implode(', ', $contextData);
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);
    if (isset($operations['edit'])) {
      $operations['edit']['query']['destination'] = $entity->url('collection');
    }
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = [];

    // Use user entry path if available for edit/add form page.
    $path = $entity->menu['path'];
    if (!empty($path)) {
      $operations['edit'] = [
        'title' => t('Edit'),
        'weight' => 10,
        'query' => [],
        'url' => Url::fromUserInput($path),
      ];
    }
    else {
      // Use default config page path in another case.
      $operations['edit'] = [
        'title' => t('Edit'),
        'weight' => 10,
        'query' => [],
        'url' => Url::fromRoute('config_pages.add_form', ['config_pages_type' => $entity->id()]),
      ];
    }
    uasort($operations, '\Drupal\Component\Utility\SortArray::sortByWeightElement');

    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function load() {
    $entity_ids = $this->getEntityIds();
    return $this->cpt_storage->loadMultiple($entity_ids);
  }

  /**
   * Loads entity IDs using a pager sorted by the entity id.
   *
   * @return array
   *   An array of entity IDs.
   */
  protected function getEntityIds() {
    $query = $this->cpt_storage->getQuery();
    $keys = $this->entityType->getKeys();
    return $query
      ->sort($keys['id'])
      ->pager($this->limit)
      ->execute();
  }

}
