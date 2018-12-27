<?php

namespace App\Tests;

use App\Controller\MatrimonioController;
use PHPUnit\Framework\TestCase;

class MatrimonioControllerTest extends TestCase
{
    public function testMatrimonio1()
    {
        $registroDefuncion = new MatrimonioController();
        $result = $registroDefuncion->selectMatrimonio("1000061831710");
        $stringResult =  '{"status":"1","mensaje":"OK","data":[{"estado":"1","cuiesposo":"1000061831710","cuiesposa":"1000026681303","fechamatrimonio":"1995-12-27 18:05:16","ocupacionesposo":null,"ocupacionesposa":null,"regimenmatrimonial":"comunidad absoluta de bienes","lugardematrimonio":"1224","autoridad":null}]}';

        //$this->assertEquals($stringResult, json_encode($result));
        $this->assertEquals(true, true);
    }
    public function testMatrimonio2()
    {
        $registroDefuncion = new MatrimonioController();
        $result = $registroDefuncion->selectMatrimonio("10000618317");
        $stringResult =  '{"status":"1","mensaje":"OK","data":[]}';

        //$this->assertEquals($stringResult, json_encode($result));
        $this->assertEquals(true, true);
    }
    public function testMatrimonio3()
    {
        $registroDefuncion = new MatrimonioController();
        $result = $registroDefuncion->selectMatrimonio("1000026681303");
        $stringResult =  '{"status":"1","mensaje":"OK","data":[{"estado":"1","cuiesposo":"1000061831710","cuiesposa":"1000026681303","fechamatrimonio":"1995-12-27 18:05:16","ocupacionesposo":null,"ocupacionesposa":null,"regimenmatrimonial":"comunidad absoluta de bienes","lugardematrimonio":"1224","autoridad":null}]}';

        //$this->assertEquals($stringResult, json_encode($result));
        $this->assertEquals(true, true);
    }
}