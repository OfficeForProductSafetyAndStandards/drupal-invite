<?php

namespace Drupal\invite_by_email\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\invite_by_email\Form\InviteByEmailBlockForm;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an 'InviteByEmailBlock' block.
 *
 * @Block(
 *  id = "invite_by_email_block",
 *  admin_label = @Translation("Invite By Email Block"),
 *  deriver = "Drupal\invite\Plugin\Derivative\InviteBlock"
 * )
 */
class InviteByEmailBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The building of form.
   *
   * @var \Drupal\Core\Form\FormBuilder
   */
  protected $formBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilder $form_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $block_id = $this->getDerivativeId();
    $build = [];
    $form = $this->formBuilder->getForm(new InviteByEmailBlockForm(), $block_id);
    $build['form'] = $form;

    return $build;
  }

}
