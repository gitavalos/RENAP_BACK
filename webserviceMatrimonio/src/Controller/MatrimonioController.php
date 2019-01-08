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
        //$cui = "";
        $content = $request->getContent();
        error_log($content);
        
        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        $jsonContent = json_decode($content);
        error_log(print_r($jsonContent,true ));
        $cuiHombre = $jsonContent->cuiHombre;
        $cuiMujer = $jsonContent->cuiMujer;

        if(!isset($cui)){
            $cui = $cuiHombre;
            if(!isset($cui)){
                $cui = $cuiMujer;
            }
        }

        return $this->json($this->selectMatrimonio($cui));
    }

    /**
    * @Route("/sa/imprimirmatrimonioA", name="geMatrimonioA")
    */
    public function geMatrimonioA(Request $request)
    {
        
        error_log(print_r($request->request, true));
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
                error_log($query);
                if ($mysqli->multi_query($query )) {
                    if ($resultado = $mysqli->use_result()) {
                        $matrimonio = array();
                        while ($fila = $resultado->fetch_row()) {
                            $tipo = array();
                            $tipo['cuiHombre'] = $fila[0];
                            $tipo['nombreHombre'] = $fila[1];
                            $tipo['apellidoHombre'] = $fila[2];
                            $tipo['paisHombre'] = $fila[3];
                            $tipo['departamentoHombre'] = $fila[4];
                            $tipo['municipioHombre'] = $fila[5];
                            $tipo['cuiMujer'] = $fila[6];
                            $tipo['nombreMujer'] = $fila[7];
                            $tipo['apellidoMujer'] = $fila[8];
                            $tipo['paisMujer'] = $fila[9];
                            $tipo['departamentoMujer'] = $fila[10];
                            $tipo['municipioMujer'] = $fila[11];
                            $tipo['paisMatrimonio'] = $fila[12];
                            $tipo['departamentoMatrimonio'] = $fila[13];
                            $tipo['municipioMatrimonio'] = $fila[14];
                            $tipo['lugarMatrimonio'] = $fila[15];
                            $tipo['fechaMatrimonio'] = $fila[16];
                            $tipo['regimenMatrimonial'] = $fila[17];

                            $matrimonio = $tipo;
                        }
                        $resultado->close();
                        if( isset($matrimonio)){
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
        $content = $request->getContent();
        error_log($content);
        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        $jsonContent = json_decode($content);
        error_log(print_r($jsonContent,true ));
        $cuiHombre = $jsonContent->cuiHombre;
        $cuiMujer = $jsonContent->cuiMujer;
        $municipio = $jsonContent->municipio;
        $lugarMatrimonio = $jsonContent->lugarMatrimonio;
        $regimenMatrimonial = $jsonContent->regimenMatrimonial;
        $fechaMatrimonio = $jsonContent->fechaMatrimonio;

        $salida = $this->insertarMatrimonio($cuiHombre, $cuiMujer, $municipio, $lugarMatrimonio, $fechaMatrimonio, $regimenMatrimonial);


        return $this->json($salida);
    }


    /**
    * @Route("/sa/registrarmatrimonioA", name="registrarMatrimonioA", methods="POST")
    */
    public function registrarMatrimonioA(Request $request)
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



