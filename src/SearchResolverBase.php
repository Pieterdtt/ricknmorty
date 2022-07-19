<?php

namespace Drupal\ricknmorty;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class SearchResolverBase implements SearchResolverInterface {
  const SOURCE = 'https://rickandmortyapi.com/';
  protected array $results = [];
  public Client $httpClient;

  /**
   * Deliberate choice, I'm assuming all resolvers will all reach out somewhere.
   *
   * @param Client $httpClient
   * @return void
   */
  public function setClient(Client $httpClient) {
      $this->httpClient = $httpClient;
  }

  public function collateResults($results, $query, $params) {
    $page = 1;
    $results = [];

    do {
      $params['query']['page'] = $page++;
      $response = $this->httpClient->request('GET', $query, $params );
      $json = json_decode($response->getBody(), true);
      $results[] = $json['results'];
    }
    while ($json['info']['next']);
    return $results;
  }
}
