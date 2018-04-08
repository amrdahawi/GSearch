<?php

namespace GSearchBundle\Service;

use Symfony\Component\HttpFoundation\Response;

class APIClient
{
    private $client;
    private $container;
    private $googleApiUrl;


    /**
     * APIClient constructor.
     * @param \GuzzleHttp\Client $client
     * @param $container
     */
    public function __construct(\GuzzleHttp\Client $client, $container)
    {
        $this->client = $client;
        $this->container = $container;
        $this->googleApiUrl = $this->container->getParameter('google_api_url');
    }

    /**
     * This function is a generic API get function
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface|null
     */
    public function get(array $params = [])
    {
        $googleKey = $this->container->getParameter('google_key');
        $googleCx = $this->container->getParameter('google_cx');

        $queryString = '?key=' . $googleKey . '&cx=' . $googleCx;
        if ($params) {
            foreach ($params as $key => $value) {
                $queryString .= '&' . $key . '=' . $value;
            }
        }
        try {
            $url = $this->googleApiUrl . $queryString;
            $response = $this->client->get($url);
            if ($response->getStatusCode() == Response::HTTP_OK) {
                return json_decode($response->getBody(), true);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

}