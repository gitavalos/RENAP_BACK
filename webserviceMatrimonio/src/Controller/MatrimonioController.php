<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class MatrimonioController extends BaseController
{

    /**
    * @Route("/sa/matrimonio", name="geMatrimonio", methods="POST")
    */
    public function geMatrimonio(Request $request)
    {
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $matrimonio = array();
        $cui = $request->request->get('cui');
        if(isset($cui) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "select * ".
                "from matrimonio m " . 
                "where m.cuiHombre = '" . $cui . "' " . 
                " or m.cuiMujer = '" . $cui . "' " . 
                ";";
                if ($mysqli->multi_query($query )) {
                    if ($resultado = $mysqli->use_result()) {
                        while ($fila = $resultado->fetch_row()) {
                            $tipo = array();
                            $tipo['estado'] = $fila[0];
                            $tipo['cuiesposo'] = $fila[1];
                            $tipo['cuiesposa'] = $fila[2];
                            $tipo['fechamatrimonio'] = $fila[3];
                            $tipo['ocupacionesposo'] = $fila[4];
                            $tipo['ocupacionesposa'] = $fila[5];
                            $tipo['regimenmatrimonial'] = $fila[6];
                            $tipo['lugardematrimonio'] = $fila[7];
                            $tipo['autoridad'] = $fila[8];

                            array_push($matrimonio, $tipo);
                        }
                        $resultado->close();

                        $salida['status'] = "1";
                        $salida['mensaje'] = "OK";
                        $salida['data'] = $matrimonio;
                    }
                } else {
                    $salida['mensaje'] = "error de consulta";
                }
                $mysqli->close();
            }    
        }else {
            $salida['mensaje'] = "no se envio el paramentro";
        }        
        return $this->json($salida);
    }
    

}



