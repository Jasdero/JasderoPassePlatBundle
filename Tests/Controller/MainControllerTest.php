<?php

namespace Jasdero\PassePlatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{

    public function testDashboard()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/dashboard');

        $this->assertCount(1, $crawler->filter('nav'));
    }

    /**
     * @dataProvider linksProvider
     * @param $linkContent
     */
    public function testLinksNavSettings($linkContent)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/dashboard');

        $link = $crawler->selectLink($linkContent)->link();

        $targetPage = $client->click($link);

        $this->assertEquals($linkContent.' list', $targetPage->filter('h5')->first()->text());
        $this->assertCount(1, $targetPage->filter('table'));
    }

    public function linksProvider()
    {
        return array(
          array('Catalogs'),
          array('Categories'),
          array('Statuses'),
          array('Vats'),
        );
    }
}
