<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DPIController extends AbstractController {

    const NOMBRE_SERVIDOR = "35.208.97.204";
    const USUARIO_DB = "root";
    const PASSWORD_DB = "EVcj+4BQ";
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
     * @Route("/consultarDPI", name="consultarDPI")
     */
    public function consultarDPI(Request $request) {
        $cuiBuscado = $fila = $request->get("cui");
        $arrResult = $this->getInfoDPI($cuiBuscado);
        if (!$request->isMethod('get')) {
            $arrResult = array();
            $arrResult['status'] = "-1";
            $arrResult['mensaje'] = "fail";
        }
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
			$queryPadres = "select pa.cuiPadre, p.nombre, p.apellido,p.fechaNacimiento,pais.nombre as Pais,departamento.nombre as departamento, municipio.nombre as municipio ,pa.idtipo_padre
							from lugar pais
								left join lugar departamento
								on pais.idlugar= departamento.padre
								left join lugar municipio
								on departamento.idlugar = municipio.padre
								inner join persona p
								on p.lugarNacimiento = municipio.idlugar
								inner join padre pa
								on pa.cuiPadre = p.cui
								where
								pa.hijo = '{$cond}'
							";
							
			if ($mysqli->multi_query($queryPadres)) {
                if ($resultado = $mysqli->use_result()) {
                    while ($fila = $resultado->fetch_row()) {
						
						if($fila[7] == 1){
							$data['cuiPadre'] = $fila[0];
							$data['nombrePadre'] = $fila[1];
							$data['apellidoPadre'] = $fila[2];
							$data['fechaNacimientoPadre'] = $fila[3];
							$data['paisPadre'] = $fila[4];
							$data['departamentoPadre'] = $fila[5];
							$data['municipioPadre'] = $fila[6];
						}else if($fila[7] == 2){
							$data['cuiMadre'] = $fila[0];
							$data['nombreMadre'] = $fila[1];
							$data['apellidoMadre'] = $fila[2];
							$data['fechaNacimientoMadre'] = $fila[3];
							$data['paisMadre'] = $fila[4];
							$data['departamentoMadre'] = $fila[5];
							$data['municipioMadre'] = $fila[6];
						}
                        
                    }
                    $resultado->close();

                    $salida['status'] = "2";
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
     * @Route("/actualizarDPI", name="actualizarDPI")
     */
    public function actualizarDPI(Request $request) {


        $salida = array();
        $salida['status'] = "-1";
        $salida['mensaje'] = "fail";

        if ($request->isMethod('POST')) {
            $persona = array();
            $persona['cui'] = $request->get("cui");
            $persona['pais'] = $request->get("pais");
            $persona['departamento'] = $request->get("departamento");
            $persona['municipio'] = $request->get("municipio");
            $persona['huella'] = $request->get("huella");
            $persona['residencia'] = $request->get("residencia");
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
            $salida['mensaje'] = "el numero de CUI tiene menos digitos de lo esperado";
            return $salida;
        }
        if (strlen($arrObj["cui"]) > 13) {
            $salida['mensaje'] = "el numero de CUI tien mas digitos de lo esperado";
            return $salida;
        }
        $mysqli = $this->getConexion();
        //mysqli_query
        $query = "UPDATE persona";
        $query .= "SET lugarVecindad = '{$arrObj["municipio"]}'";
        $query .= ", direccion = '{$arrObj["residencia"]}'";
        if ($arrObj["huella"] != "") {
            $query .= ", huella = '{$arrObj["huella"]}'";
        }
        $query .= "WHERE cui = '{$arrObj['cui']}'";
        if ($mysqli->query($query)) {
            $salida['status'] = "1";
            $salida['mensaje'] = "OK";
        } else {
            $salida['mensaje'] .= " error update";
        }
        $mysqli->close();
        return $salida;
    }

    //INSERT INTO persona 
    //(`cui`, `nombre`, `apellido`, `fechaNacimiento`, `genero`, `lugarNacimiento`, `huella`, `fechaVencimiento`, `lugarVecindad`, `estadoCivil`) 
    //VALUES ('1', '1', '1', '1', '1', '1', '1', '1', '1', '1');


    private function generarDpi($inicial) {
        $dpi = 0;
        $i = 0;

        $entrada = $inicial;
        $numero = $inicial;
        $contador = 9;
        $resul = 0;
        while ($numero > 0) {
            $resul = $resul + $numero % 10 * $contador;
            $numero = $numero / 10;
            $contador--;
        }
        $sal = (string) $entrada . ($resul % 11);
        if ($resul % 11 < 10) {
            $dpi = $sal;
            $i++;
        }

        return $dpi;
    }

    /**
     * @Route("/registrarNacimiento", name="registrarNacimiento")
     */
    public function registrarNacimiento(Request $request) {
        $salida = array();
        $salida['status'] = "-1";
        $salida['mensaje'] = "fail";

        //$this->generarDpi(10000004);
        $mysqli = $this->getConexion();
        $consulta = "SELECT max(cui) FROM persona";
        $ultimoDPI = "";
        $it = 0;
        if ($mysqli->multi_query($consulta)) {
            if ($resultado = $mysqli->use_result()) {
                while ($fila = $resultado->fetch_row()) {
                    $it += 1;
                    $ultimoDPI = $fila[0];
                }
                $resultado->close();
            }
        } else {
            
        }
        if ($it != 1) {
            $salida['mensaje'] .= " error en duplicidad de numeros de dpi";
            return $this->json($salida);
        }

        $originalDate = $request->get("fechaNacimiento");
        $fecha = date("Y/m/d", strtotime($originalDate));


        $dpiCortado = substr($ultimoDPI, 0, -5);
        $lugarNacimiento = (strlen($request->get("lugarNacimiento")) > 3) ? $request->get("lugarNacimiento") : "0" . $request->get("lugarNacimiento");
        $DPICreado = $this->generarDpi(intval($dpiCortado) + 1) . $lugarNacimiento;
        $query = "INSERT INTO persona 
            (`cui`, `nombre`, `apellido`, `fechaNacimiento`, `genero`, `lugarNacimiento`, `huella`
            , `fechaVencimiento`, `lugarVecindad`,`direccion`, `estadoCivil`) 
            VALUES ('{$DPICreado}'"
                . ", '{$request->get("nombre")}'"
                . ", '{$request->get("apellido")}'"
                . ", '{$fecha}'"
                . ", '{$request->get("genero")}'"
                . ", '{$request->get("lugarNacimiento")}'"
                . ", NULL"
                . ", NULL"
                . ", '{$request->get("lugarNacimiento")}'"
                . ", '{$request->get("direccion")}'"
                . ", 'SOLTERO')";


        if ($mysqli->query($query)) {
            $salida['status'] = "1";
            $salida['mensaje'] = "OK";
        } else {
            $salida['mensaje'] .= " error INSERT PERSONA" . $mysqli->error . "|" . $query . "|";
        }


        if ($request->get("cuiPadre") != "") {
            $insert = "INSERT INTO `padre` (`idtipo_padre`, `cuiPadre`, `hijo`) VALUES ('1', '{$request->get("cuiPadre")}', '{$DPICreado}')";
            if ($mysqli->query($insert)) {
                $salida['status'] = "1";
                $salida['mensaje'] = "OK";
            } else {
                $salida['mensaje'] .= " error INSERT cuiPadre" . $mysqli->error . "|" . $query . "|";
            }
        }
        if ($request->get("cuiMadre") != "") {
            $insert = "INSERT INTO `padre` (`idtipo_padre`, `cuiPadre`, `hijo`) VALUES ('2', '{$request->get("cuiMadre")}', '{$DPICreado}')";
            if ($mysqli->query($insert)) {
                $salida['status'] = "1";
                $salida['mensaje'] = "OK";
            } else {
                $salida['mensaje'] .= " error INSERT cuiMadre" . $mysqli->error . "|" . $query . "|";
            }
        }


        $mysqli->close();

        return $this->json($salida);
    }

    /**
     * @Route("/getDepartamentos", name="getDepartamentos")
     */
    public function getDepartamentos(Request $request) {
        $salida = array();
        $salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $data = array();
        $mysqli = $this->getConexion();


        if ($mysqli->connect_errno) {
            $salida['mensaje'] = "error de conexion";
        } else {
            $consulta = "SELECT * FROM lugar where idtipo_lugar = 2";
            if ($mysqli->multi_query($consulta)) {
                if ($resultado = $mysqli->use_result()) {
                    while ($fila = $resultado->fetch_row()) {
                        $departamento = array();
                        $departamento['idlugar'] = $fila[0];
                        $departamento['nombre'] = $fila[1];

                        array_push($data, $departamento);
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
        return $this->json($salida);
    }

    /**
     * @Route("/getMunicipios", name="getMunicipios")
     */
    public function getMunicipios(Request $request) {
        $salida = array();
        $salida['status'] = "-1";
        $salida['mensaje'] = "fail";
        $salida['data'] = array();
        $data = array();
        $mysqli = $this->getConexion();


        if ($mysqli->connect_errno) {
            $salida['mensaje'] = "error de conexion";
        } else {
            $consulta = "select * from lugar where padre = {$request->get("departamento")}";
            if ($mysqli->multi_query($consulta)) {
                if ($resultado = $mysqli->use_result()) {
                    while ($fila = $resultado->fetch_row()) {
                        $municipio = array();
                        $municipio['idlugar'] = $fila[0];
                        $municipio['nombre'] = $fila[1];

                        array_push($data, $municipio);
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
        return $this->json($salida);
    }

}
