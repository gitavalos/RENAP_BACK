<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RegistroDefuncionController extends BaseController
{

    /**
    * @Route("/sa/registrarDefuncion", name="postDefuncion", methods="POST")
    */
    public function postDefuncion(Request $request)
    {
        $content = $request->getContent();

        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        $jsonContent = json_decode($content);

        $cui = $jsonContent->cui;
        $cuiCompareciente = $jsonContent->cuiCompareciente;
        $municipio = $jsonContent->municipio;
        $lugarDefuncion = $jsonContent->lugarDefuncion;
        $fechaDefuncion = $jsonContent->fechaDefuncion;
        $causa = $jsonContent->causa;
        
        $result = $this->registroDefuncion($cui, $cuiCompareciente, $municipio, $lugarDefuncion, $fechaDefuncion, $causa);
        return $this->json($result);
    
    }

    public function registroDefuncion($cui, $cuiCompareciente, $municipio, $lugarDefuncion, 
                                        $fechaDefuncion, $causa)
    {

        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = null;

        if(isset($cui, $cuiCompareciente, $municipio, $lugarDefuncion, $fechaDefuncion, $causa) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "INSERT INTO `SA2018`.`defuncion`
                (`fechaDefuncion`,
                `causa`,
                `cui`,
                `lugarDefuncion`,
                `cuiCompareciente`)
                VALUES
                ('$fechaDefuncion',
                '$causa',
                '$cui',
                $municipio,
                '$cuiCompareciente');
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
    * @Route("/sa/consultarDefuncion", name="wsConsultarDefuncion", methods="POST")
    */
    public function wsConsultarDefuncion(Request $request)
    {
        $content = $request->getContent();

        error_log($content);

        if(empty($content)){
            throw new BadRequestHttpException("Content is empty");
        }

        $jsonContent = json_decode($content);

        $cui = $jsonContent->cui;

        $result = $this->consultarDefuncion($cui);
        return $this->json($result);
    }

    public function consultarDefuncion($cui)
    {
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = null;
        $defunciones = array();

        if(isset($cui) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                //$query = "SELECT cui, cuiCompareciente, fechaDefuncion, causa, colegiado_medico, nombre_medico, lugarDefuncion FROM defuncion WHERE cui like '$cui';";

                $query = "
                            SELECT def.cui, 
                                    per.nombre, 
                                    per.apellido, 
                                    per.genero,
                                    per.fechaNacimiento, 
                                    lugpais.nombre as pais, 
                                    lugdepto.nombre as depto, 
                                    lugmun.nombre as mun, 
                                    per.lugarNacimiento,
                                    per.estadoCivil,
                                    '' as nombreConyugue,
                                    '' as apellidoConyugue,
                                    def.cuiCompareciente, 
                                    percomp.nombre as nombrecompareciente, 
                                    percomp.apellido as apellidocompareciente,
                                    lugcomppais.nombre as paiscompareciente, 
                                    lugcompdepto.nombre as deptocompareciente,  
                                    lugcompmun.nombre as muncompareciente,
                                    percomp.direccion as recidenciacompareciente,
                                    lugdefpais.nombre as paisdefuncion, 
                                    lugdefdepto.nombre as deptodefuncion,
                                    def.direccion,
                                    def.fechaDefuncion,
                                    def.causa
                            FROM defuncion AS def
                            INNER JOIN persona AS per
                            ON def.cui like per.cui
                            INNER JOIN lugar AS lugmun
                            ON per.lugarNacimiento = lugmun.idlugar
                            INNER JOIN lugar AS lugdepto
                            ON lugmun.padre = lugdepto.idlugar
                            INNER JOIN lugar AS lugpais
                            ON lugdepto.padre = lugpais.idlugar
                            INNER JOIN persona AS percomp
                            ON def.cuicompareciente like percomp.cui
                            INNER JOIN lugar AS lugcompmun
                            ON percomp.lugarNacimiento = lugcompmun.idlugar
                            INNER JOIN lugar AS lugcompdepto
                            ON lugcompmun.padre = lugcompdepto.idlugar
                            INNER JOIN lugar AS lugcomppais
                            ON lugcompdepto.padre = lugcomppais.idlugar
                            INNER JOIN lugar AS lugdefmun
                            ON def.lugarDefuncion = lugdefmun.idlugar
                            INNER JOIN lugar as lugdefdepto
                            ON lugdefmun.padre = lugdefdepto.idlugar
                            INNER JOIN lugar as lugdefpais
                            ON lugdefdepto.padre = lugdefpais.idlugar
                            WHERE def.cui like '$cui'
                            ;
                ";

                if ($mysqli->multi_query($query)) {
                    if ($resultado = $mysqli->use_result()) {
                        $encontrado = false;

                        while ($fila = $resultado->fetch_row()) {
                            $encontrado = true;

                            $defuncion = array();
                            $defuncion['cui'] = $fila[0];
                            $defuncion['nombre'] = $fila[1];
                            $defuncion['apellido'] = $fila[2];
                            $defuncion['genero'] = $fila[3];
                            $defuncion['fechaNacimiento'] = $fila[4];
                            $defuncion['pais'] = $fila[5];
                            $defuncion['departamento'] = $fila[6];
                            $defuncion['municipio'] = $fila[7];
                            $defuncion['lugarNacimiento'] = $fila[8];
                            $defuncion['estadoCivil'] = $fila[9];
                            $defuncion['nombreConyugue'] = $fila[10];
                            $defuncion['apellidoConyugue'] = $fila[11];
                            $defuncion['cuiCompareciente'] = $fila[12];
                            $defuncion['nombreCompareciente'] = $fila[13];
                            $defuncion['apellidoCompareciente'] = $fila[14];
                            $defuncion['paisCompareciente'] = $fila[15];
                            $defuncion['departamentoCompareciente'] = $fila[16];
                            $defuncion['municipioCompareciente'] = $fila[17];
                            $defuncion['recidenciaCompareciente'] = $fila[18];
                            $defuncion['paisDefuncion'] = $fila[19];
                            $defuncion['departamentoDefuncion'] = $fila[20];
                            $defuncion['lugarDefuncion'] = $fila[21];
                            $defuncion['fechaDefuncion'] = $fila[22];
                            $defuncion['causa'] = $fila[23];

                            array_push($defunciones, $defuncion);
                        }
                        $resultado->close();

                        if($encontrado){
                            $salida['status'] = "1";
                            $salida['mensaje'] = "OK";
                            $salida['data'] = $defunciones[0];
                        }else{
                            $salida['status'] = "-1";
                            $salida['mensaje'] = "Defunción no encontrada";
                            $salida['data'] = null;
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



