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
}