<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class BaseController extends AbstractController
{
    /*
    const NOMBRE_SERVIDOR = "35.193.84.232";
    const USUARIO_DB = "SAuser";
	const PASSWORD_DB = "SAuser2018";
    const NOMBRE_DB = "SA2018";
    */

    const NOMBRE_SERVIDOR = "35.193.84.232";
    const USUARIO_DB = "SAuser";
	const PASSWORD_DB = "SAuser2018";
    const NOMBRE_DB = "SA2018";

    public function getConexion(){
        $mysqli = new \mysqli( 
            self::NOMBRE_SERVIDOR, 
            self::USUARIO_DB,
            self::PASSWORD_DB,
            self::NOMBRE_DB
        );
        return $mysqli;
    }

}



