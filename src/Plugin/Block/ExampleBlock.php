<?php

namespace Drupal\custom_new\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "custom_new_example",
 *   admin_label = @Translation("Example"),
 *   category = @Translation("custom new")
 * )
 */
final class ExampleBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity display repository service.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * Constructs a new CustomBlockBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getViewModeOptions($entity_type) {
    return $this->getDisplayModeOptions('view_mode', $entity_type);
  }

  /**
   * Gets an array of display mode options.
   *
   * @param string $display_type
   *   The display type to be retrieved. It can be "view_mode" or "form_mode".
   * @param string $entity_type_id
   *   The entity type whose display mode options should be returned.
   *
   * @return array
   *   An array of display mode labels, keyed by the display mode ID.
   */
  protected function getDisplayModeOptions($display_type, $entity_type_id) {
    $options = ['default' => t('Default')];
    foreach ($this->getDisplayModesByEntityType($display_type, $entity_type_id) as $mode => $settings) {
      $options[$mode] = $settings['label'];
    }
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['node_id'] = [
      '#type' => 'entity_autocomplete',
      '#title' => 'Select Node',
      '#default_value' => Node::load($this->configuration['node_id']),
      '#target_type' => 'node',
      '#selection_settings' => [
        'target_bundles' => ['article'],
      ],
    ];

    $view_modes = $this->getViewModeOptions('node');
    $options = [];
    foreach ($view_modes as $view_mode => $info) {
      $options[$view_mode] = $info['label'];
    }

    $form['view_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('View Mode'),
      '#options' => $options,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['node_id'] = $form_state->getValue('node_id');
    $this->configuration['view_mode'] = $form_state->getValue('view_mode');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node_id = $this->configuration['node_id'];
    $node = $this->entityTypeManager->getStorage('node')->load($node_id);
    $build = [];

    if ($node) {
      $view_mode = $this->configuration['view_mode'];
      $view_builder = $this->entityTypeManager->getViewBuilder('node');
      $build = $view_builder->view($node, $view_mode);
    }

    return $build;
  }

}
