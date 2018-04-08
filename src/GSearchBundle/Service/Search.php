<?php

namespace GSearchBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;

class Search
{
    const NUM_RESULTS_PER_PAGE = 10;
    const NUM_PAGES = 10;

    private $apiClient;

    public function __construct(APIClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Search using keywords and urls.
     * This function can handle multiple searches
     *
     * @param array $keywords
     * @param array $urls
     * @return array
     */
    public function multiSearch(array $keywords, array $urls)
    {
        $results = [];
        // make a google search for each keyword and url
        for ($i = 0; $i < count($keywords); $i++) {
            $results[] = $this->singleSearch($keywords[$i], $urls[$i]);
        }
        return $results;
    }

    /**
     * This function handles single google Search using a keyword and a url
     *
     * @param $keyword
     * @param $url
     * @return array
     */
    public function singleSearch($keyword, $url)
    {
        // limit response to the fields required only, performance Improvement
        $response = $this->apiClient->get([
            'q' => $keyword,
            'num' => self::NUM_RESULTS_PER_PAGE,
            'fields' => 'items(displayLink),queries(nextPage)',
        ]);

        if (!$response) {
            throw new \RuntimeException('Failed to retrieve results from google');
        }
        // store items from the first call, if no items return then return an empty array.
        if (isset($response['items'])) {
            $results = array_values($response['items']);
            // get the rest of the results (maximum of 10 pages, which is 100 results)
            for ($i = 1; $i < self::NUM_PAGES; $i++) {
                if (isset($response['queries']['nextPage'])) {
                    $nextPage = $response['queries']['nextPage'][0]['startIndex'];
                    $response = $this->apiClient->get([
                        'q' => $keyword,
                        'start' => $nextPage,
                        'num' => self::NUM_RESULTS_PER_PAGE
                    ]);
                    if (isset($response['items'])) {
                        $results = array_merge($results, array_values($response['items']));
                    }
                } else {
                    // break if there are no more pages
                    break;
                }
            }
        } else {
            $results = [];
        }

        return [
            'keyword' => $keyword,
            'url' => $url,
            'matches' => $this->getMatchingLinks($results, $url)
        ];
    }

    /**
     * Go through results and find matches
     * This function will return a list found matches (thier order in google results)
     *
     * @param array $results
     * @param $url
     * @return array
     */
    private function getMatchingLinks(array $results, $url)
    {
        $matchedLinks = [];
        // parseUrl to match against DisplayLink
        $parsedUrl = $this->parseUrl($url);
        foreach ($results as $key => $result) {
            if (isset($result['displayLink'])) {
                // parse display link to make sure that matching is accurate
                $parsedDisplayLink = $this->parseUrl($result['displayLink']);
                if ($parsedDisplayLink == $parsedUrl) {
                    // google results starts from 1, hence the belw +1
                    $matchedLinks[] = $key + 1;
                }
            }
        }
        return $matchedLinks;
    }

    /**
     *  Parse Url to remove scheme and www
     *
     * @param $url
     * @return mixed
     */
    private function parseUrl($url)
    {
        $parsedUrl = parse_url($url);
        if (isset($parsedUrl['host'])) {
            $urlHostOrPath = $parsedUrl['host'];
        } else {
            $urlHostOrPath = $parsedUrl['path'];
        }
        return str_replace('www.', '', $urlHostOrPath);
    }
}