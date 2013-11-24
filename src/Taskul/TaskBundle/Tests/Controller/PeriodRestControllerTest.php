<?php

namespace Taskul\TaskBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskBaseControllerTest extends WebTestCase
{
    private $client;
    private $serviceContainer;
    private $idTask;
    private $idTaskForbidden;


    public function setUp()
    {
        $this->setClient();
        $this->serviceContainer = self::$kernel->getContainer();
        $this->idTask = 192;
        $this->idTaskForbidden = 162;
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET','/es/api/tasks/'.$this->idTaskForbidden.'/period/index');

        $this->assertEquals(
            403,
            $this->client->getResponse()->getStatusCode()
        );

        $crawler = $this->client->request('GET','/es/api/tasks/'.$this->idTask.'/period/index');

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $crawler = $this->client->request('GET','/es/api/tasks/0/period/index');

        $this->assertEquals(
            404,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testNew()
    {
        $before = $this->getPeriodsCount();

        $crawler = $this->client->request('GET','/es/api/tasks/'.$this->idTask.'/periods/new');
        $form = $crawler->selectButton('form_task_submit')->form();

        $form['taskul_taskbundle_period[begin]'] = '28/12/1978';
        $form['taskul_taskbundle_period[end]'] = '28/12/1990';
        $crawler = $this->client->submit($form);

        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );

        $after = $this->getPeriodsCount();

        $this->assertGreaterThan(
            $before,
            $after
            );

    }

    public function testCGet()
    {
        $crawler = $this->getPeriods();

        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertGreaterThan(
            0,
            $this->getPeriodsCount()
            );
    }

    public function testEditPeriod()
    {
        $crawler = $this->getPeriods();
        $linkEdit = $crawler->filter('a.edit-period')->first();
        $linkView = $crawler->filter('a.view-period')->first();
        $crawler = $this->client->click($linkEdit->link());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $form = $crawler->selectButton('form_task_submit')->form(array(),'PUT');
        $form['taskul_taskbundle_period[begin]'] = '07/03/1980';
        $crawler = $this->client->submit($form);

        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertEquals(
            $linkView->attr('href'),
            $this->client->getResponse()->headers->get('location')
        );

    }

    public function testViewPeriod()
    {
        $crawler = $this->getPeriods();
        $linkView = $crawler->filter('a.view-period')->first();
        $crawler = $this->client->click($linkView->link());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

        $linkBack = $crawler->filter('a.back')->first();
        $crawler = $this->client->click($linkBack->link());
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );

    }
    public function testDeletePeriod()
    {
        $crawler = $this->getPeriods();
        $before = $this->getPeriodsCount();

        $linkDelete = $crawler->filter('a.remove-period')->first();

        $crawler = $this->client->click($linkDelete->link());
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );

        $after = $this->getPeriodsCount();

        $this->assertLessThan(
            $before,
            $after
        );

     }

    private function getPeriodsCount()
    {
        $this->getJsonPeriods();

        return count($this->getPeriodsJsonDecode()->periods);
    }

    private function getPeriodsJsonDecode()
    {
        return json_decode($this->client->getResponse()->getContent());
    }

    private function getJsonPeriods()
    {
        return $this->client->request('GET','/es/api/tasks/'.$this->idTask.'/periods.json');
    }
    private function getPeriods()
    {
        return $this->client->request('GET','/es/api/tasks/'.$this->idTask.'/periods');
    }
    private function setClient($username='camilocot@gmail.com',$password='123456789o',$redirect=FALSE)
    {
        $this->client = static::createClient(array(),array(
            'PHP_AUTH_USER' =>  $username,
            'PHP_AUTH_PW'   => $password
            ));
        $this->client->followRedirects($redirect);
    }
}