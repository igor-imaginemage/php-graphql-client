<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 10/5/18
 * Time: 12:15 AM
 */

namespace GraphQL;

use GraphQL\Exception\QueryError;

/**
 * Class Client
 *
 * @package GraphQL
 */
class Client
{
    /**
     * @var string
     */
    protected $endpointUrl;

    /**
     * @var array
     */
    protected $authorizationHeaders;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Client constructor.
     *
     * @param string $endpointUrl
     * @param array  $authorizationHeaders
     */
    public function __construct($endpointUrl, $authorizationHeaders = [])
    {
        $this->endpointUrl          = $endpointUrl;
        $this->authorizationHeaders = $authorizationHeaders;
        $this->httpClient           = new \GuzzleHttp\Client();
    }

    /**
     * @param Query $query
     * @param bool  $resultsAsArray
     *
     * @return Results|null
     * @throws QueryError
     */
    public function runQuery(Query $query, $resultsAsArray = false)
    {
        return $this->runRawQuery((string) $query, $resultsAsArray);
    }

    /**
     * @param string $queryString
     * @param bool   $resultsAsArray
     *
     * @return Results|null
     * @throws QueryError
     */
    public function runRawQuery($queryString, $resultsAsArray = false)
    {
        // Set request headers for authorization and content type
        if (!empty($this->authorizationHeaders)) {
            $options['headers'] = $this->authorizationHeaders;
        }
        $options['headers']['Content-Type'] = 'application/json';

        // Set query in the request body
        $options['body'] = json_encode(['query' => $queryString]);

        // Send api request and get response
        $response = $this->httpClient->post($this->endpointUrl, $options);

        // Parse response to extract results
        $results = null;
        if ($response->getStatusCode() === 200) {
            $results = new Results($response, $resultsAsArray);
        }

        return $results;
    }
}