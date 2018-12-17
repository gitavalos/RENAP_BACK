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

    public function testMatrimonioIntegracion3()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/sa/matrimonio',
            array('cui' => '1000061831710')
        );

        $result = json_decode($client->getResponse()->getContent());
        $stringResult =  '{"status":"1","mensaje":"OK","data":[{"estado":"1","cuiesposo":"1000061831710","cuiesposa":"1000026681303","fechamatrimonio":"1995-12-27 18:05:16","ocupacionesposo":null,"ocupacionesposa":null,"regimenmatrimonial":"comunidad absoluta de bienes","lugardematrimonio":"1224","autoridad":null}]}';

        $this->assertEquals($stringResult, json_encode($result));
    }
}
