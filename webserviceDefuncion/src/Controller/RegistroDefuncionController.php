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

        $cui = $request->request->get('cui');
        $cuiCompareciente = $request->request->get('cuiCompareciente');
        $municipio = $request->request->get('municipio');
        $lugarDefuncion = $request->request->get('lugarDefuncion');
        $fechaDefuncion = $request->request->get('fechaDefuncion');
        $causa = $request->request->get('causa');

         
        return $this->registroDefuncion($cui, $cuiCompareciente, $municipio, $lugarDefuncion, $fechaDefuncion, $causa);

    }

    public function registroDefuncion($cui, $cuiCompareciente, $municipio, $lugarDefuncion, 
                                        $fechaDefuncion, $causa)
    {

        $salida = array();
		$salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $departamentos = array();

        if(isset($cui, $cuiCompareciente, $municipio, $lugarDefuncion, $fechaDefuncion, $causa) ){
            $mysqli = $this->getConexion();
            if ($mysqli->connect_errno) {
                $salida['mensaje'] = "error de conexion";
            } else {
                //verificar cui
                $query = 
                "INSERT INTO `SA2018`.`defuncion`
                (`fechaDefuncion`,
                `causa`,
                `cui`,
                `lugarDefuncion`,
                `cuiCompareciente`)
                VALUES
                ('$fechaDefuncion',
                '$causa',
                '$cui',
                '$lugarDefuncion',
                '$cuiCompareciente');
                ";
                $resultado =$mysqli->query($query);
                var_dump($resultado);
                die;
                /*
                if (TRUE == TRUE) {
                    if ($resultado->) {
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
                */
                $mysqli->close();
            }    
        }else {
            $salida['mensaje'] = "no se envio el paramentro";
        }        
        return $this->json($salida);
    }
}



