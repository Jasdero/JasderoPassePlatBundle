<?php


namespace Jasdero\PassePlatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StateControllerTest extends WebTestCase
{
    /**
     * Tests creation, edit and deletion of a Status
     * @runInSeparateProcess
     */

    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/state/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /state/");
        $crawler = $client->click($crawler->selectLink('add_circle')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('done')->form(array(
            'jasderopasseplatbundle_state[name]'  => 'Test',
            'jasderopasseplatbundle_state[color]'  => 'grey',
            'jasderopasseplatbundle_state[activated]'  => '1',
            'jasderopasseplatbundle_state[description]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('edit')->link());

        $form = $crawler->selectButton('done')->form(array(
            'jasderopasseplatbundle_state[name]'  => 'Foo',
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
