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
        $cui = $request->request->get('cui');
        $cuiCompareciente = $request->request->get('cuiCompareciente');
        $municipio = $request->request->get('municipio');
        $lugarDefuncion = $request->request->get('lugarDefuncion');
        $fechaDefuncion = $request->request->get('fechaDefuncion');
        $causa = $request->request->get('causa');

         
        $result = $this->registroDefuncion($cui, $cuiCompareciente, $municipio, $lugarDefuncion, $fechaDefuncion, $causa);
        return $this->json($result);
    }

    public function registroDefuncion($cui, $cuiCompareciente, $municipio, $lugarDefuncion, 
                                        $fechaDefuncion, $causa)
    {

        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";

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
        $cui = $request->request->get('cui');
        $result = $this->consultarDefuncion($cui);
        return $this->json($result);
    }

    public function consultarDefuncion($cui)
    {
        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $defunciones = array();

        if(isset($cui) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                $query = "SELECT cui, cuiCompareciente, fechaDefuncion, causa, colegiado_medico, nombre_medico, lugarDefuncion FROM defuncion WHERE cui like '$cui';";
                if ($mysqli->multi_query($query)) {
                    if ($resultado = $mysqli->use_result()) {
                        while ($fila = $resultado->fetch_row()) {
                            $defuncion = array();
                            $defuncion['cui'] = $fila[0];
                            $defuncion['cuiCompareciente'] = $fila[1];
                            $defuncion['fechaDefuncion'] = $fila[2];
                            $defuncion['causa'] = $fila[3];
                            $defuncion['colegiado_medico'] = $fila[4];
                            $defuncion['nombre_medico'] = $fila[5];
                            $defuncion['lugarDefuncion'] = $fila[6];

                            array_push($defunciones, $defuncion);
                        }
                        $resultado->close();

                        $salida['status'] = "1";
                        $salida['mensaje'] = "OK";
                        $salida['data'] = $defunciones;
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



