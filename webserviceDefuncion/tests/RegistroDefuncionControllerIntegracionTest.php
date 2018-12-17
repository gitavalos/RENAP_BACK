<?php

namespace App\Controller;

//use App\Controller\RegistroDefuncionController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testShowPost()
    {
        $client = static::createClient();

        $data = array('cui' => "1000000100108",
        'cuiCompareciente' => "1000109971406",
        'municipio' => 112,
        'lugarDefuncion' => "Mi Casita",
        'fechaDefuncion' => "2018-01-01 12:15:34",
        'causa' => "Muerte Natural"
        );
        $client->request('POST', '/sa/registrarDefuncion', $data);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}