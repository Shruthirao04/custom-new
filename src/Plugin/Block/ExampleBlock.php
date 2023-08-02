<?php

namespace Drupal\custom_new\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;

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
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $entityTypeManager, EntityDisplayRepositoryInterface $entityDisplayRepository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->entityDisplayRepository = $entityDisplayRepository;
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
      $container->get('entity_display.repository')
    );
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
    ];

    $view_modes = $this->entityDisplayRepository->getViewModeOptions('node');

    $form['view_mode'] = [
      '#type' => 'radios',
      '#title' => 'View Mode',
      '#options' => $view_modes,
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
