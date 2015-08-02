<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

class TopicArticleControllerTest extends WebTestCase
{
	private $em;

	protected function initReset()
	{
		$client = static::createClient();

		$this->em = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $this->em->getConnection();

        $connection->exec('SET FOREIGN_KEY_CHECKS=0');

        // delete test entities
		$connection->exec('TRUNCATE TABLE topic');

        $connection->exec('SET FOREIGN_KEY_CHECKS=1');

    }


    public function testGetAllAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/topics/list');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));
    }

    public function testPostAction()
    {
    	$this->initReset();

        $client = static::createClient();

		$crawler = $client->request('POST', '/api/topics/create', array(
		    'data' => array(
		        'title'  => 'Title Topic Demo'
		    )
		));


        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));
    }

    public function testGetTopicAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/topics/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));
    }

    public function testPutTopicAction()
    {
        $client = static::createClient();

        $crawler = $client->request('PUT', '/api/topics/1', array(
		    'data' => array(
		        'title'  => 'Title Topic Demo Update'
		    )
		));


        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));
    }


    public function testDeleteTopic()
    {
        $client = static::createClient();

        $crawler = $client->request('DELETE', '/api/topics/1');


        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));

        $this->initReset();
    }
}