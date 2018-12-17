<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class RegistroDefuncionController extends BaseController
{

    /**
    * @Route("/sa/registrarDefuncion", name="postDefuncion", methods="POST")
    */
    public function postDefuncion(Request $request)
    {
        /*
            cui
            cuiCompareciente
            municipio
            lugarDefuncion
            fechaDefuncion
            causa
        */

        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $departamentos = array();

        $cui = $request->request->get('cui');
        $cuiCompareciente = $request->request->get('cuiCompareciente');
        $municipio = $request->request->get('municipio');
        $lugarDefuncion = $request->request->get('lugarDefuncion');
        $fechaDefuncion = $request->request->get('fechaDefuncion');
        $causa = $request->request->get('causa');

        if(isset($cui, $cuiCompareciente, $municipio, $lugarDefuncion, $fechaDefuncion, $causa) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                //verificar cui
                $query = "SELECT cui FROM persona WHERE cui LIKE '".$cui."';";

                if ($mysqli->multi_query($query )) {
                    if ($resultado = $mysqli->use_result()) {
                        while ($fila = $resultado->fetch_row()) {
                            $tipo = array();
                            $tipo['cui'] = $fila[0];
                            array_push($departamentos, $tipo);
                        }
                        
                        $resultado->close();

                        $salida['status'] = "1";
                        $salida['mensaje'] = "OK";
                        $salida['data'] = $departamentos;
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

        /*
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $departamentos = array();
        $pais = $request->request->get('pais');
        if(isset($pais) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "select l.idlugar, l.nombre from ".
                "lugar l " . 
                "inner join lugar lp " .
                "    on l.padre = lp.idlugar " .
                "where l.idtipo_lugar = 1 " .
                " and lp.idlugar = " . $pais . 
                ";";
                if ($mysqli->multi_query($query )) {
                    if ($resultado = $mysqli->use_result()) {
                        while ($fila = $resultado->fetch_row()) {
                            $tipo = array();
                            $tipo['codigo'] = $fila[0];
                            $tipo['nombre'] = $fila[1];
                            array_push($departamentos, $tipo);
                        }
                        $resultado->close();

                        $salida['status'] = "1";
                        $salida['mensaje'] = "OK";
                        $salida['data'] = $departamentos;
                    }
                } else {
                    $salida['mensaje'] = "error de consulta";
                }
                $mysqli->close();
            }    
        }else {
            $salida['mensaje'] = "no se envio el paramentro";
        }
        */
        

    }

}



