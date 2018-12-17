<?php

namespace App\Test\Controller;

use App\Controller\DPIController;
use PHPUnit\Framework\TestCase;


class DPIControllerTest extends TestCase {

  
    public function index() {
        return $this->json([
                    'message' => 'Welcome to your new controller!',
                    'path' => 'src/Controller/DPIController.php',
        ]);
    }

    public function testConsultaCui() {
        $controlador = new DPIController();
		$arrResultado = $controlador->getInfoDPI("1000016530101");
		$this->assertEquals(1,$arrResultado["status"]);
    }

  
    public function testIngresoDpiCorrecto() {
		$controlador = new DPIController();
		$arrResultado = $controlador->getInfoDPI("100001653010");
		$this->assertEquals(-1,$arrResultado["status"]);
    }
	
	public function testVerificarMuerto(){
		$controlador = new DPIController();
		$arrResultado = $controlador->getInfoDPI("1000016530101");
		
		$this->assertEquals(1,$arrResultado["data"]["muerto"]);
		  
	}
	
	public function testVerificarActualizacion(){
		$controlador = new DPIController();
			$persona = array();
            $persona['cui'] = "100001653010";
            $persona['pais'] = "";
            
            $persona['municipio'] = "";
            $persona['huella'] = "";
			//falta algun dato en el post
		$resultado = $controlador->updateDPI($persona);
		$this->assertEquals(-1,$resultado["status"]);
	}
	
	

}
