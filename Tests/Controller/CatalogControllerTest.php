<?php


namespace Jasdero\PassePlatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CatalogControllerTest extends WebTestCase
{
    /**
     * Tests creation, edit and deletion of a Catalog
     * @runInSeparateProcess
     */

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/catalog/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /catalog/");
        $crawler = $client->click($crawler->selectLink('add_circle')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('done')->form(array(
            'jasderopasseplatbundle_catalog[name]'  => 'Test',
            'jasderopasseplatbundle_catalog[description]'  => 'Unit fonctionnel',
            'jasderopasseplatbundle_catalog[activated]'  => '1',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('edit')->link());

        $form = $crawler->selectButton('done')->form(array(
            'jasderopasseplatbundle_catalog[name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Foo")')->count(), 'Missing element td:contains("Foo")');

        // Delete the entity
        $client->submit($crawler->selectButton('delete_forever')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }
}
