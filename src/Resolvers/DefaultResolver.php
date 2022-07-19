<?php

namespace Drupal\ricknmorty\Resolvers;

use Drupal\ricknmorty\SearchResolverBase;

class DefaultResolver extends SearchResolverBase {

    public function resolve(array $config) {
        // No point in asking the resolver anything if the block instance isn't asking for a REST resource.
        if ($config['api_source_type'] !== 'api') {
          return null;
        }
        $uri = static::SOURCE . $config['api_source_type'] . '/character';
        $params = ['http_errors' => FALSE, 'query' => ['name' => 'jerry']];

        $response = $this->httpClient->request('GET', $uri, $params );
        $results = [];
        $page = 1;
        if (!$response->getStatusCode() == 200) {
          return null;
        }

        return $this->collateResults($response->getBody(), $uri, $params);
    }
}
