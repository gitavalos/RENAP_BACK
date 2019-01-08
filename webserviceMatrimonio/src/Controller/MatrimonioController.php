<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class MatrimonioController extends BaseController
{

    /**
    * @Route("/sa/imprimirmatrimonio", name="geMatrimonio", methods="POST")
    */
    public function geMatrimonio(Request $request)
    {
        
        
        $cui = $request->request->get('cui');
        if(!isset($cui)){
            $cui = $request->request->get('cuihombre');
            if(!isset($cui)){
                $cui = $request->request->get('cuimujer');
            }
        }

        return $this->json($this->selectMatrimonio($cui));
    }
    public function selectMatrimonio($cui){
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        if(isset($cui) ){  
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "select 
                mat.cuiHombre, h.nombre as nombreh, h.apellido as apellidoh,
                paish.nombre as nomPaish, deptoh.nombre as nomDeptoh, munh.nombre as nomMunh,
                mat.cuimujer, m.nombre as nombrem, m.apellido as apellidoh,
                paism.nombre as nomPaism, deptom.nombre as nomDeptom, munm.nombre as nomMunm,
                paismat.nombre as nomPaismat, deptomat.nombre as nomDeptomat, munmat.nombre as nomMunmat,
                mat.direccion, mat.fechamatrimonio, mat.regimenMatrimonial 
                from matrimonio mat  
                inner join persona h
                    on h.cui = mat.cuihombre
                inner join lugar munh 
                    on h.lugarnacimiento = munh.idlugar
                inner join lugar deptoh 
                    on munh.padre = deptoh.idlugar
                inner join lugar paish
                    on deptoh.padre = paish.idlugar
                    
                inner join persona m
                    on m.cui = mat.cuimujer
                inner join lugar munm 
                    on m.lugarnacimiento = munm.idlugar
                inner join lugar deptom 
                    on munm.padre = deptom.idlugar
                inner join lugar paism
                    on deptom.padre = paism.idlugar
                    
                inner join lugar munmat 
                    on mat.lugarmatrimonio = munmat.idlugar
                inner join lugar deptomat 
                    on munmat.padre = deptomat.idlugar 
                inner join lugar paismat 
                    on deptomat.padre = paismat.idlugar " . 
                " where mat.cuiHombre = '" . $cui . "' " . 
                " or mat.cuiMujer = '" . $cui . "' " . 
                ";";
                if ($mysqli->multi_query($query )) {
                    if ($resultado = $mysqli->use_result()) {
                        $matrimonio = array();
                        while ($fila = $resultado->fetch_row()) {
                            $tipo = array();
                            $tipo['cuihombre'] = $fila[0];
                            $tipo['nombrehombre'] = $fila[1];
                            $tipo['apellidohombre'] = $fila[2];
                            $tipo['paishombre'] = $fila[3];
                            $tipo['departamentohombre'] = $fila[4];
                            $tipo['municipiohombre'] = $fila[5];
                            $tipo['cuimujer'] = $fila[6];
                            $tipo['nombremujer'] = $fila[7];
                            $tipo['apellidomujer'] = $fila[8];
                            $tipo['paismujer'] = $fila[9];
                            $tipo['departamentomujer'] = $fila[10];
                            $tipo['municipiomujer'] = $fila[11];
                            $tipo['paismatrimonio'] = $fila[12];
                            $tipo['departamentomatrimonio'] = $fila[13];
                            $tipo['municipiomatrimonio'] = $fila[14];
                            $tipo['lugarmatrimonio'] = $fila[15];
                            $tipo['fechamatrimonio'] = $fila[16];
                            $tipo['regimenmatrimonial'] = $fila[17];

                            array_push($matrimonio, $tipo);
                        }
                        $resultado->close();
                        if( count($matrimonio) > 0){
                            $salida['status'] = "1";
                            $salida['mensaje'] = "OK";
                            $salida['data'] = $matrimonio;
                        }else{
                            $salida['mensaje'] = "no se encontro un matrimonio";
                        }
                    }
                } else {
                    $salida['mensaje'] = "error de consulta";
                }
                $mysqli->close();
            }    
        }else {
            $salida['mensaje'] = "no se envio el paramentro";
        }        
        return ($salida);
    }
    
    /**
    * @Route("/sa/registrarmatrimonio", name="registrarMatrimonio", methods="POST")
    */
    public function registrarMatrimonio(Request $request)
    {    
        $cuiHombre = $request->request->get('cuihombre');
        $cuiMujer = $request->request->get('cuimujer');
        $municipio = $request->request->get('municipio');
        $lugarMatrimonio = $request->request->get('lugarmatrimonio');
        $regimenMatrimonial = $request->request->get('regimenmatrimonial');
        $fechaMatrimonio = $request->request->get('fechamatrimonio');

        $salida = $this->insertarMatrimonio($cuiHombre, $cuiMujer, $municipio, $lugarMatrimonio, $fechaMatrimonio, $regimenMatrimonial);


        return $this->json($salida);
    }
    
    public function insertarMatrimonio($cuiHombre, $cuiMujer, $municipio, $lugarMatrimonio, $fechaMatrimonio, $regimenMatrimonial)
    {
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();

        //if(isset($cuiHombre, $cuiMujer, $municipio, $lugarMatrimonio, $fechaMatrimonio, $regimenMatrimonial)){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $consulta ="INSERT INTO SA2018.matrimonio
                (estado,
                cuiHombre,
                cuiMujer,
                lugarMatrimonio,
                direccion,
                fechaMatrimonio,
                regimenMatrimonial)
                VALUES
                (1,
                '$cuiHombre',
                '$cuiMujer',
                $municipio,
                '$lugarMatrimonio',
                '$fechaMatrimonio',
                '$regimenMatrimonial');";
                error_log($consulta);
                if ($resultado = $mysqli->query($consulta)) {
                    error_log("echo");
                    if($resultado){
                        $salida['status'] = "1";
                        $salida['mensaje'] = "OK";
                        $salida['data'] = array();             
                    }else{
                        $salida['mensaje'] = "no se puede registrar el matrimonio";
                    }
                }else{
                    $salida['mensaje'] = "error de consulta";
                }
                $mysqli->close();
            }
        //}else{
        //    $salida['mensaje'] = "parametros incorrectos";
        //}

        return ($salida);
    }

    public function verificar($cuiEsposo, $cuiEsposa)
    {
        $salida = false; 
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida= false;
                error_log("entro aca1");
            } else {
                $query = "select  * 
                from matrimonio mat  " . 
                " where mat.cuiHombre = '" . $cuiEsposo . "' " . 
                " and mat.cuiMujer = '" . $cuiEsposa . "' " . 
                ";";
                if ($mysqli->multi_query($query )) {
                    if ($resultado = $mysqli->use_result()) {
                        $matrimonio = array();
                        while ($fila = $resultado->fetch_row()) {
                            $tipo = array();
                            $tipo['cuihombre'] = $fila[0];

                            array_push($matrimonio, $tipo);
                        }
                        $resultado->close();
                        if( count($matrimonio) > 0){
                            $salida= true;
                        }else{
                            error_log("entro aca2");
                        }
                    }else{
                        error_log("entro aca3");
                    }
                }else{
                    error_log("entro aca4");
                }
                $mysqli->close();
            }    
                
        return $salida;
    }

}



