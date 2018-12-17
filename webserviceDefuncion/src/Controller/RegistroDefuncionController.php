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
}



