<?php

namespace Drupal\ricknmorty\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ricknmorty\RickAndMortySearchCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a rick and morty block.
 *
 * @Block(
 *   id = "ricknmorty",
 *   admin_label = @Translation("Rick and Morty"),
 *   category = @Translation("Custom")
 * )
 */
class RicknmortyBlock extends BlockBase implements ContainerFactoryPluginInterface
{

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;
  private RickAndMortySearchCollector $searchCollector;

  /**
   * Constructs a new RicknmortyBlock instance.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param RickAndMortySearchCollector $search_collector
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RickAndMortySearchCollector $search_collector)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->searchCollector = $search_collector;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('search_collector')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration()
  {
    return [
      'api_source_type' => 'api',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state)
  {
    $form['api_source_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('SOURCE TYPE'),
      '#options' => [
        'api' => $this->t('Rest'),
        'graphql' => $this->t('Graphql'),
      ],
      '#default_value' => $this->configuration['api_source_type'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state)
  {
    $this->configuration['foo'] = $form_state->getValue('foo');
  }

  /**
   * {@inheritdoc}a
   */
  public function build()
  {
    $build = \Drupal::formBuilder()->getForm('\Drupal\ricknmorty\Form\SearchForm', $this->configuration);

//    $config = $this->getConfiguration();
//    $this->searchCollector->resolve($config);
    return $build;
  }

}
