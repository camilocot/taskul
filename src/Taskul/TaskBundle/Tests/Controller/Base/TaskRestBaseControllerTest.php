<?php

namespace Taskul\TaskBundle\Tests\Controller\Base;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Taskul\TaskBundle\Controller\Base\TasksRestBaseController;

require_once __DIR__.'/../../../../../../app/AppKernel.php';

class TaskBaseControllerTest extends WebTestCase
{
    public function doLogin($username, $password) {
        $this->client = static::createClient();
        $crawler = $this->client->request('GET', '/es/login');
        $form = $crawler->selectButton('_submit')->form(array(
           '_username'  => $username,
           '_password'  => $password,
           ));
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();
    }

    public function testLoadAllTags()
    {
        $this->doLogin('admin','admin');
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $controller = new TasksRestBaseController();
        $tags = $controller->loadAllTags($user);
        $this->assertCount(0, $tags);

    }

    protected static $kernel;
    protected static $container;
    protected $client;


    public static function setUpBeforeClass()
    {
        self::$kernel = new \AppKernel('dev', true);
        self::$kernel->boot();

        self::$container = self::$kernel->getContainer();
    }

    public function get($serviceId)
    {
        return self::$kernel->getContainer()->get($serviceId);
    }
}