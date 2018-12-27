<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class BaseController extends AbstractController
{
    const NOMBRE_SERVIDOR = "35.208.97.204";//35.227.104.139//
    const USUARIO_DB = "root";
	const PASSWORD_DB = "EVcj+4BQ";
    const NOMBRE_DB = "SA2018";

    public function getConexion(){
        $mysqli = new \mysqli( 
            self::NOMBRE_SERVIDOR, 
            self::USUARIO_DB,
            self::PASSWORD_DB,
            self::NOMBRE_DB
        );
        return $mysqli;
    }

    /**
    * @Route("/sa/tipolugar", name="geTipoLugar")
    */
    public function geTipoLugar(Request $request)
    {
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $tipoLugar = array();
        
        $mysqli = $this->getConexion();
        if ($mysqli->connect_errno) {
            $salida['mensaje'] = "error de conexion";
        } else {
            if ($mysqli->multi_query("select * from tipo_lugar" )) {
                if ($resultado = $mysqli->use_result()) {
                    while ($fila = $resultado->fetch_row()) {
                        $tipo = array();
                        $tipo['codigo'] = $fila[0];
                        $tipo['nombre'] = $fila[1];
                        array_push($tipoLugar, $tipo);
                    }
                    $resultado->close();

                    $salida['status'] = "1";
                    $salida['mensaje'] = "OK";
                    $salida['data'] = $tipoLugar;
                }
            } else {
                $salida['mensaje'] = "error de consulta";
            }
            $mysqli->close();
        }
        return $this->json($salida);
    }

}



