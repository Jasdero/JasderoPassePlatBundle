<?php
/**
 * Created by PhpStorm.
 * User: apside
 * Date: 25/04/2017
 * Time: 15:23
 */

namespace Jasdero\PassePlatBundle\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VatControllerTest extends WebTestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/vat/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /vat/");
        $crawler = $client->click($crawler->selectLink('add_circle')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('done')->form(array(
            'jasderopasseplatbundle_vat[rate]'  => '100',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("100")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('edit')->link());

        $form = $crawler->selectButton('done')->form(array(
            'jasderopasseplatbundle_vat[rate]'  => '50',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("50")')->count(), 'Missing element td:contains("Foo")');

                // Delete the entity
                $client->submit($crawler->selectButton('delete_forever')->form());
                $crawler = $client->followRedirect();

                // Check the entity has been delete on the list
                $this->assertNotRegExp('/50/', $client->getResponse()->getContent());
    }

}
