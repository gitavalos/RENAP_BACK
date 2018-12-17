<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DPIController extends AbstractController {

    const NOMBRE_SERVIDOR = "35.193.84.232";
    const USUARIO_DB = "SAuser";
    const PASSWORD_DB = "SAuser2018";
    const NOMBRE_DB = "SA2018";

    public function getConexion() {
        $mysqli = new \mysqli(
                self::NOMBRE_SERVIDOR, self::USUARIO_DB, self::PASSWORD_DB, self::NOMBRE_DB
        );
        return $mysqli;
    }

    /**
     * @Route("/DPI", name="d_p_i")
     */
    public function index() {
        return $this->json([
                    'message' => 'Welcome to your new controller!',
                    'path' => 'src/Controller/DPIController.php',
        ]);
    }

    /**
     * @Route("/consultaDPI", name="consultaCui")
     */
    public function consultaCui(Request $request) {
        $cuiBuscado = $fila = $request->get("cui");
        $arrResult = $this->getInfoDPI($cuiBuscado);

        return $this->json($arrResult);
    }

    public function getInfoDPI($cond) {
        $salida = array();
        $salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $data = array();
        if (strlen($cond) < 13) {
            $salida['mensaje'] = "el numero de CUI es menor a lo esperado";
            return $salida;
        }
        if (strlen($cond) > 13) {
            $salida['mensaje'] = "el numero de CUI es mayor a lo esperado";
            return $salida;
        }
        $mysqli = $this->getConexion();


        if ($mysqli->connect_errno) {
            $salida['mensaje'] = "error de conexion";
        } else {
            $consulta = "select 
                        p.cui,p.nombre,p.apellido,p.genero,p.fechaNacimiento
                        ,pais.nombre as 'pais',departamento.nombre as 'departamento', municipio.nombre as 'municipio'
                        ,p.huella, p.fechaVencimiento
                        ,CASE WHEN defuncion.cui is null THEN 0 ELSE 1 END AS muerto
                        from lugar pais
                        left join lugar departamento
                        on pais.idlugar= departamento.padre
                        left join lugar municipio
                        on departamento.idlugar = municipio.padre
                        inner join persona p
                        on p.lugarNacimiento = municipio.idlugar
                        left join defuncion defuncion
                        on p.cui = defuncion.cui
                        where pais.idtipo_lugar = 1
                        and p.cui ='{$cond}'";
            if ($mysqli->multi_query($consulta)) {
                if ($resultado = $mysqli->use_result()) {
                    while ($fila = $resultado->fetch_row()) {
                        $persona = array();
                        $persona['cui'] = $fila[0];
                        $persona['nombre'] = $fila[1];
                        $persona['apellido'] = $fila[2];
                        $persona['genero'] = $fila[3];
                        $persona['fechaNacimiento'] = $fila[4];
                        $persona['pais'] = $fila[5];
                        $persona['departamento'] = $fila[6];
                        $persona['municipio'] = $fila[7];
                        $persona['huella'] = $fila[8];
                        $persona['fechaVencimiento'] = $fila[9];
                        $persona['muerto'] = $fila[10];

                        $data = $persona;
                    }
                    $resultado->close();

                    $salida['status'] = "1";
                    $salida['mensaje'] = "OK";
                    $salida['data'] = $data;
                }
            } else {
                $salida['mensaje'] = "error de consulta";
            }
            $mysqli->close();
        }
        return $salida;
    }

    /**
     * @Route("/ingresoDPI", name="ingresoDpi")
     */
    public function ingresoDpi(Request $request) {


        $salida = array();
        $salida['status'] = "-1";
        $salida['mensaje'] = "fail";


        $mysqli = $this->getConexion();
        if ($request->getMethod('POST')) {
            $persona = array();
            $persona['cui'] = $request->get("cui");
            $persona['pais'] = $request->get("pais");
            $persona['departamento'] = $request->get("departamento");
            $persona['municipio'] = $request->get("municipio");
            $persona['huella'] = $request->get("huella");
            $salida = $this->updateDPI($persona);
        }
        return $this->json($salida);
    }

    public function updateDPI($arrObj) {
        $salida = array();
        $salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        if (strlen($arrObj["cui"]) < 13) {
            $salida['mensaje'] = "el numero de CUI es menor a lo esperado";
            return $salida;
        }
        if (strlen($arrObj["cui"]) > 13) {
            $salida['mensaje'] = "el numero de CUI es mayor a lo esperado";
            return $salida;
        }
        if (count($arrObj["cui"]) == 5) {
            $salida['status'] = "1";
            $salida['mensaje'] = "OK";
        }
        return $salida;
    }

}
