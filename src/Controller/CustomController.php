<?php

namespace Drupal\custom_new\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\custom_new\Form\CustomForm;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Custom controller to render the custom form.
 */
class CustomController extends ControllerBase {

  /**
   * The custom form.
   *
   * @var \Drupal\custom_new\Form\CustomForm
   */
  protected $customForm;

  /**
   * CustomFormController constructor.
   */
  public function __construct(FormBuilderInterface $form_builder) {
    $this->formBuilder = $form_builder;
  }

  /**
   * Creates an instance of the controller.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function display(Node $node) {
    $form = $this->formBuilder->getForm(CustomForm::class, $node);
  return $form;
}
}
