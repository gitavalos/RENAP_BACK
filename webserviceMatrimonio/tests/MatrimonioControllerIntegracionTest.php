<?php

namespace App\Tests;

use App\Controller\MatrimonioController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MatrimonioControllerIntegracionTest extends WebTestCase
{
    public function testMatrimonioIntegracion1()
    {
        $client = static::createClient();

        $client->request('GET', '/sa/tipolugar');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testMatrimonioIntegracion2()
    {
        $client = static::createClient();

        $client->request('POST', '/sa/matrimonio', array('cui' => '1000061831710'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
