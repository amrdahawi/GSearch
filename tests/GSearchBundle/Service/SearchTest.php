<?php

namespace GSearchBundle\Tests\Service;

use GSearchBundle\Service\Search;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SearchTest extends KernelTestCase
{
    private $container;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $this->container = $kernel->getContainer();
    }

    public function testGoogleSearch()
    {
        /** @var Search $searchService */
        $searchService = $this->container->get('search');
        $result = $searchService->singleSearch('creditorwatch', 'http://www.creditorwatch.com.au');

        // check that the output structure is correct
        $this->assertArrayHasKey('keyword', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('matches', $result);
    }
}
