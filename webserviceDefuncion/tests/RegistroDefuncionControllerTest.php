<?php

namespace App\Tests;

use App\Controller\RegistroDefuncionController;
use PHPUnit\Framework\TestCase;

class RegistroDefuncionControllerTest extends TestCase
{
    public function testRegistroDefuncion()
    {
        $registroDefuncion = new RegistroDefuncionController();
        $result = $registroDefuncion->registroDefuncion("1000000100108", "'1000108571605'", 112, "Mi Casita", 
        "2018-01-01 10:15:34", "muerte natural");

        $this->assertEquals('-1', $result["status"]);
    }

    public function testRegistroDefuncion2()
    {
        $registroDefuncion = new RegistroDefuncionController();
        $result = $registroDefuncion->registroDefuncion("1000000291504", "'1000108571605'", 602, "Hospital regional", 
        "2018-01-01 10:15:34", "Accidente de transito");

        $this->assertEquals('-1', $result["status"]);
    }

}