<?php

namespace Jasdero\PassePlatBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
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
            array('/product/1/edit'),
            array('/product/new'),
            array('/product/status/1'),
            array('/product/catalog/1'),
            array('/category/'),
            array('/category/new'),
            array('/category/1'),
            array('/category/1/edit'),
            array('/vat/'),
            array('/vat/new'),
            array('/vat/1'),
            array('/vat/1/edit'),
            array('/catalog/'),
            array('/catalog/1'),
            array('/catalog/new'),
            array('/catalog/1/edit'),
            array('/orders'),
            array('/orders/1'),
            array('/orders/1/edit'),
            array('/orders/status/1'),
            array('/orders/catalog/1'),
            array('/orders/user/1'),
            array('/state/'),
            array('/state/1'),
            array('/state/new'),
            array('/state/1/edit'),
            // ...
        );
    }
}
