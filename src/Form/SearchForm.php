<?php

namespace Drupal\ricknmorty\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ricknmorty\RickAndMortySearchCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SearchForm extends \Drupal\Core\Form\FormBase
{
  private RickAndMortySearchCollector $searchCollector;

  /**
   * @param RickAndMortySearchCollector $searchCollector
   */
  public function __construct(RickAndMortySearchCollector $searchCollector)
  {
    $this->searchCollector = $searchCollector;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('search_collector')
    );
  }


  /**
   * @inheritDoc
   */
  public function getFormId()
  {
    return 'ajax_form';
  }

  /**
   * @inheritDoc
   */
  public function buildForm(array $form, FormStateInterface $form_state, $block_config = null)
  {
//    dump($block_config);
    $form['search'] = [
      '#type' => 'textfield',
      '#ajax' => [
        'callback' => '::getResults',
        'event' => 'keyup',
        'wrapper' => 'autocomplete',
      ],
    ];
    $form['autocomplete'] = [
      '#markup' => '<div id="autocomplete"></div>',
    ];
    $form['#config'] = $block_config;
    return $form;
  }

  /**
   * @inheritDoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    // TODO: Implement submitForm() method.
  }

  public function getResults(array $form, FormStateInterface $form_state)
  {
    \Drupal::logger('trigger')->debug('<pre>' . print_r($form_state->getTriggeringElement(), TRUE) . '</pre>');
    $response = new AjaxResponse();
    $values = $form_state->getValues();
    $results = $this->searchCollector->resolve($form['#config']);

    $result_items = array_map(function($item) {
      return ['#theme' => 'rm_search_result', '#name' => $item['name']];
    }, $results[0]);

    $result_list = [
      '#theme' => 'item_list',
      '#items' => $result_items,
      '#prefix' => '<div id="autocomplete">',
      '#suffix' => '</div>'
    ];
    $response->addCommand(new ReplaceCommand('#autocomplete', $result_list));

    return $response;
  }
}
