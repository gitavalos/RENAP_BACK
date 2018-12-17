<?php

namespace App\Controller;

//use App\Controller\RegistroDefuncionController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testRegistrarDefuncion()
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

    public function testStatusConsultarDefuncion()
    {
        $client = static::createClient();

        $data = array('cui' => "1000000100108");
        $client->request('POST', '/sa/consultarDefuncion', $data);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testConsultarDefucion()
    {
        $client = static::createClient();

        $cui = "1000001182207";
        $cuiResponse = "";

        $client->request(
            'POST',
            '/sa/consultarDefuncion',
            array('cui' => $cui)
        );

        $result = json_decode($client->getResponse()->getContent());
        if($result->status=='1'){
            if(count($result->data)==1){
                $dataResult = $result->data;
                $datosDefuncion = $dataResult[0];
                $cuiResponse = $datosDefuncion->cui;
            }
        }

        $this->assertEquals($cui, $cuiResponse);
    }

    public function testErrorStatusConsultarDefuncion()
    {
        $client = static::createClient();

        $data = array('cui' => "1000000100108");
        $client->request('POST', '/sa/consultarDefuncion', $data);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}