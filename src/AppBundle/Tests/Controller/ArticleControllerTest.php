<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;

class ArticleControllerTest extends WebTestCase
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
		$connection->exec('TRUNCATE TABLE article');

        $connection->exec('SET FOREIGN_KEY_CHECKS=1');

    }


    public function testGetAllAction()
    {
        $this->initReset();

        $client = static::createClient();


        $crawler = $client->request('POST', '/api/topics/create', array(
            'data' => array(
                'title'  => 'Title Topic Demo'
            )
        ));

        $crawler = $client->request('GET', '/api/topics/1/articles/list');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));
    }

    public function testPostAction()
    {

        $client = static::createClient();

		$crawler = $client->request('POST', '/api/topics/articles/create', array(
		    'data' => array(
                'title'  => 'Title Article Demo',
                'topic_id'  => '1',
                'author'  => 'Author Demo',
		        'text'  => 'Lorem Ipsum lorem ipsum'

		    )
		));


        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));
    }

    public function testGetArticleAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/topics/articles/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));
    }

    public function testPutArticleAction()
    {
        $client = static::createClient();

        $crawler = $client->request('PUT', '/api/topics/articles/1', array(
            'data' => array(
                'title'  => 'Title Article Demo Update',
                'topic_id'  => '1',
                'author'  => 'Author Demo',
                'text'  => 'Lorem Ipsum lorem ipsum'
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

        $crawler = $client->request('DELETE', '/api/topics/articles/1');


        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains(
        'Content-Type',
        'application/json'
    	));

        $this->assertTrue(is_string($client->getResponse()->getContent()));

        $this->initReset();
    }
}