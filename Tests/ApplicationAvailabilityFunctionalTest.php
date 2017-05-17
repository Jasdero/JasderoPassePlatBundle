<?php

namespace Jasdero\PassePlatBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * Tests that urls are active; modify accordingly if you changed the routes, also needs at least one entry in tables
     * @dataProvider urlProvider
     * @param $url
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/dashboard'),
            array('/product/'),
            array('/category/'),
            array('/category/new'),
            array('/vat/'),
            array('/vat/new'),
            array('/catalog/'),
            array('/catalog/new'),
            array('/orders'),
            array('/state/'),
            array('/state/new'),
            array('/orders/archives'),
            // ...
        );
    }
}
