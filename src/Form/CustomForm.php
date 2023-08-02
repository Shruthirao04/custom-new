<?php

namespace Drupal\custom_new\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;
use Drupal\node\Entity\Node;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Inherits parent class.
 */
class CustomForm extends FormBase {
  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The database service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  protected $entityTypeManager;
  protected $currentUser;

  /**
   * CustomForm constructor.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Drupal\Core\Database\Connection $database
   *   The database service.
   */
  public function __construct(MessengerInterface $messenger, Connection $database, EntityTypeManagerInterface $entity_type_manager, AccountInterface $current_user) {
    $this->messenger = $messenger;
    $this->database = $database;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('database'),
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  /**
   * Generated form id.
   */
  public function getFormId() {
    return 'custom_form_details';
  }

  /**
   * Build form generates form.
   */
  public function buildForm(array $form, FormStateInterface $form_state, Node $node = NULL) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => 'Title',
      '#required' => TRUE,
      '#placeholder' => 'title',
      '#default_value' => $node ? $node->getTitle() : '',
    ];

    $user = User::load($this->currentUser->id());
    $form['user'] = [
      '#type' => 'entity_autocomplete',
      '#title' => 'User',
      '#target_type' => 'user',
      '#required' => TRUE,
      '#placeholder' => 'user',
      '#default_value' => $user,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
    ];
    return $form;
  }
  /**
   * Submit form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->messenger->addMessage("User Details Submitted Successfully");
    $this->database->insert("user_details")->fields([
      'title' => $form_state->getValue("title"),
      'user' => $form_state->getValue("user"),
    ])->execute();
  }

}
