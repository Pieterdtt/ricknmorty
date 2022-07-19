<?php

namespace Drupal\ricknmorty;

class RickAndMortySearchCollector {
  private $searchResolvers = [];

  public function addResolver(SearchResolverInterface $searchResolver) {
    $this->searchResolvers[] = $searchResolver;
  }

  public function resolve(array $config) {
    foreach ($this->searchResolvers as $resolver) {
      $result = $resolver->resolve($config);
      if ($result) {
        return $result;
      }
    }
  }
}
