<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class LugarController extends BaseController
{

    /**
    * @Route("/sa/departamentos", name="geDepartamentos", methods="POST")
    */
    public function geDepartamentos(Request $request)
    {
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $departamentos = array();
        
        $content = $request->getContent();
        error_log($content);
        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        $jsonContent = json_decode($content);

        $pais = $jsonContent->idPais;
        
        //$pais = $request->request->get('pais');
        if(isset($pais) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "select l.idlugar, l.nombre from ".
                "lugar l " . 
                "inner join lugar lp " .
                "    on l.padre = lp.idlugar " .
                "where l.idtipo_lugar = 2 " .
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

                        $listaDeptos = array();
                        $listaDeptos['listaDepartamentos'] = $departamentos;
                        $salida['data'] = $listaDeptos;
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
    /**
    * @Route("/sa/municipios", name="geMunicipios", methods="POST")
    */
    public function geMunicipios(Request $request)
    {
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $departamentos = array();
        
        $content = $request->getContent();
        error_log($content);
        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        $jsonContent = json_decode($content);

        $departamento = $jsonContent->idDepartamento;
        
        //$departamento = $request->request->get('departamento');
        if(isset($departamento) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "select l.idlugar, l.nombre from ".
                "lugar l " . 
                "inner join lugar lp " .
                "    on l.padre = lp.idlugar " .
                "where l.idtipo_lugar = 3 " .
                " and lp.idlugar = " . $departamento . 
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
                        
                        $listaDeptos = array();
                        $listaDeptos['listaMunicipios'] = $departamentos;
                        $salida['data'] = $listaDeptos;
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



