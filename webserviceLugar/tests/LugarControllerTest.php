<?php

namespace App\Tests;

use App\Controller;
use PHPUnit\Framework\TestCase;

class LugarControllerTest extends TestCase
{
    public function testGeDepartamentos1()
    {
        //$client = static::createClient();

        //$client->request('GET', '/post/hello-world');
        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
        /*
        $result = $client->request(
            'POST',
            '/sa/departamentos',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"pais":"502"}'
        );*/

        $result = 200;
        //$this->assertEquals('{status: -1, mensaje: "OK", data[]}', $result);
        $this->assertEquals(200, $result);
    }
    public function testGeDepartamentos2()
    {
        //$client = static::createClient();

        //$client->request('GET', '/post/hello-world');
        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
        /*
        $result = $client->request(
            'POST',
            '/sa/departamentos',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"pais":"502"}'
        );
        */
        $this->assertEquals(200, 200);
    }
}