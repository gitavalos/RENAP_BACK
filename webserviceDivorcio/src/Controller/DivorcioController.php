<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;



class DivorcioController extends BaseController
{

    /**
    * @Route("/sa/registrarDivorcio", name="postDivorcio", methods="POST")
    */
    public function postDivorcio(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        $jsonContent = json_decode($content);

        /*
            cuiHombre
            cuiMujer
            municipio
            lugarDivorcio
            fechaDivorcio
        */
        $cuiHombre = $jsonContent->cuiHombre;
        $cuiMujer = $jsonContent->cuiMujer;
        $municipio = $jsonContent->municipio;
        $lugarDivorcio = $jsonContent->lugarDivorcio;
        $fechaDivorcio = $jsonContent->fechaDivorcio;

         
        $result = $this->registroDivorcio($cuiHombre, $cuiMujer, $municipio, $lugarDivorcio, $fechaDivorcio);
        return $this->json($result);
    }

    public function registroDivorcio($cuiHombre, $cuiMujer, $municipio, $lugarDivorcio, $fechaDivorcio)
    {

        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";

        if(isset($cuiHombre, $cuiMujer, $municipio, $lugarDivorcio, $fechaDivorcio) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "
                            INSERT INTO divorcio
                            (idMatrimonio, fechaDivorcio, lugarDivorcio, direccion)
                            SELECT idMatrimonio, '$fechaDivorcio', $municipio, '$lugarDivorcio'
                            FROM matrimonio
                            WHERE cuihombre like '$cuiHombre' and cuimujer like '$cuiMujer'
                            ;
                ";
                $resultado =$mysqli->query($query);

                if ($resultado == TRUE) {
                        $salida['status'] = "1";
                        $salida['mensaje'] = "OK";
                } else {
                    $salida['mensaje'] = "error de consulta";
                }

                $mysqli->close();
            }    
        }else {
            $salida['mensaje'] = "no se enviaron los parametros necesarios";
        }        
        return $salida;
    }

    /**
    * @Route("/sa/consultarDivorcio", name="wsConsultarDivorcio", methods="POST")
    */
    public function wsConsultarDivorcio(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        $jsonContent = json_decode($content);

        $cuiHombre = $jsonContent->cuiHombre;
        $cuiMujer = $jsonContent->cuiMujer;

        $result = $this->consultarDivorcio($cuiHombre, $cuiMujer);
        return $this->json($result);
    }

    public function consultarDivorcio($cuiHombre, $cuiMujer)
    {
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $divorcios = array();

        if(isset($cuiHombre, $cuiMujer) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "
                            SELECT  phombre.cui as cuiHombre,
                                    phombre.nombre as nombreHombre,
                                    phombre.apellido as apellidoHombre,
                                    paishombre.nombre as paisHombre,
                                    deptohombre.nombre as departamentoHombre,
                                    munhombre.nombre as municipioHombre,
                                    pmujer.cui as cuiMujer,
                                    pmujer.nombre as nombreMujer,
                                    pmujer.apellido as apellidoMujer,
                                    paismujer.nombre as paisMujer,
                                    deptomujer.nombre as departamentoMujer,
                                    munmujer.nombre as municipioMujer,
                                    mundivorcio.nombre as municipio,
                                    divorcio.direccion as lugarDivorcio,
                                    divorcio.fechaDivorcio,
                                    matrimonio.regimenMatrimonial
                            FROM divorcio
                            JOIN matrimonio
                            ON divorcio.idmatrimonio = matrimonio.idMatrimonio

                            JOIN persona as phombre
                            ON matrimonio.cuiHombre like phombre.cui
                            JOIN lugar AS munhombre
                            ON phombre.lugarNacimiento = munhombre.idlugar
                            JOIN lugar AS deptohombre
                            ON munhombre.padre  = deptohombre.idlugar
                            JOIN lugar AS paishombre
                            ON deptohombre.padre = paishombre.idlugar

                            JOIN persona as pmujer
                            ON matrimonio.cuiMujer like pmujer.cui
                            JOIN lugar AS munmujer
                            ON pmujer.lugarNacimiento = munmujer.idlugar
                            JOIN lugar AS deptomujer
                            ON munmujer.padre  = deptomujer.idlugar
                            JOIN lugar AS paismujer
                            ON deptomujer.padre = paismujer.idlugar

                            JOIN lugar AS mundivorcio
                            ON divorcio.lugarDivorcio = mundivorcio.idlugar
                            WHERE matrimonio.cuiHombre like '$cuiHombre' and matrimonio.cuiMujer like '$cuiMujer'
                            ;
                ";

                if ($mysqli->multi_query($query)) {
                    if ($resultado = $mysqli->use_result()) {
                        $encontrado = false;

                        while ($fila = $resultado->fetch_row()) {
                            $encontrado = true;
                            $divorcio = array();

                            $divorcio['cuiHombre'] = $fila[0];
                            $divorcio['nombreHombre'] = $fila[1];
                            $divorcio['apellidoHombre'] = $fila[2];
                            $divorcio['paisHombre'] = $fila[3];
                            $divorcio['departamentoHombre'] = $fila[4];
                            $divorcio['municipioHombre'] = $fila[5];
                            $divorcio['cuiMujer'] = $fila[6];
                            $divorcio['nombreMujer'] = $fila[7];
                            $divorcio['apellidoMujer'] = $fila[8];
                            $divorcio['paisMujer'] = $fila[9];
                            $divorcio['departamentoMujer'] = $fila[10];
                            $divorcio['municipioMujer'] = $fila[11];
                            $divorcio['municipio'] = $fila[12];
                            $divorcio['lugarDivorcio'] = $fila[13];
                            $divorcio['fechaDivorcio'] = $fila[14];
                            $divorcio['regimenMatrimonial'] = $fila[15];

                            array_push($divorcios, $divorcio);
                        }
                        $resultado->close();

                        if($encontrado){
                            $salida['status'] = "1";
                            $salida['mensaje'] = "OK";
                            $salida['data'] = $divorcios;
                        }else{
                            $salida['status'] = "-1";
                            $salida['mensaje'] = "Divorcio no encontrado";
                            $salida['data'] = $divorcios;
                        }
                        
                    }
                } else {
                    $salida['mensaje'] = "error de consulta";
                }
                $mysqli->close();
            }    
        }else {
            $salida['mensaje'] = "no se enviaron los parametros necesarios";
        }        
        return $salida;
    }

}



