<?php

global $enlace;
/// conexiones

    //
    $enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");
    
    if ( $enlace ){
        echo "Conexion establecida a Ombu<br><p>";
        //exit;
    }else{
        echo "Conexion no establecida a Ombu<br><p>";
        exit;
        //die (print_r(sqlsrv_errors(), true));
    }

$consultita = "SELECT id, archivo FROM archivos_masivo WHERE estado = 'Pendiente'";
$resultadox = mysqli_query($enlace, $consultita);
$row_cnt = mysqli_num_rows($resultadox);

while($misdatos2 = mysqli_fetch_assoc($resultadox)){
        $id=$misdatos2["id"];
        $archivo=$misdatos2["archivo"];

    $l = procesar_archivo($id);
    echo "[+] $archivo: $l procesadas\n";
}// while

echo "cantidad: $row_cnt<br>";



///////////////////////////////////////////////////////////////////////////////////////////////////////////////// bucle para procesar 
function procesar_archivo($archivo_id)
	{
		//global $mydb;
        $enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");


		$lineas_procesadas = 0;
		$lineas_rechazadas = 0;
		$base_archivos = '/var/www/html/rrhh.farmar-net.com.ar/assets/uploads/masivo/';

		//$query = mysql_query("SELECT cod_novedad, archivo FROM archivos_masivo WHERE estado='Pendiente' AND id=$archivo_id");
        $query = "SELECT cod_novedad, archivo FROM archivos_masivo WHERE estado='Pendiente' AND id=$archivo_id";
        $resultadoy = mysqli_query($enlace, $query);
        $row_cnt = mysqli_num_rows($resultadoy);

		if ($row_cnt == 1) {

			//list ($cod_novedad, $archivo) = mysql_fetch_row($query);

            while($misdatos3 = mysqli_fetch_assoc($resultadoy)){
                $cod_novedad=$misdatos3["cod_novedad"];
                $archivo=$misdatos3["archivo"];            
            }


			if (is_readable($base_archivos . '/' . $archivo)) {
				$csvFile = fopen($base_archivos . '/' . $archivo, 'r');
				while(($datos = fgetcsv($csvFile, 0, ';')) !== FALSE) {
					$code_error = 0;
					$desc_error = NULL;
					$registro_ok = 1;
					$estado = 'Pendiente';
					if (count($datos) >= 3 and strlen($datos[0]) > 0) {
						$dni = $datos[0];
						$fecha = $datos[1];
						if (! validar_fecha($fecha)) {
							$desc_error = 'Fecha no valida';
							$code_error = 1;
							$estado = 'Rechazado';
							if ($registro_ok == 1) {
								$lineas_rechazadas++;
								$registro_ok = 0;
							}
						
						}

						$valor = str_replace(',', '.', $datos[2]);
						if (!is_numeric($valor)) {
							$desc_error = 'Valor no numerico';
							$code_error = 1;
							$estado = 'Rechazado';
							if ($registro_ok == 1) {
								$lineas_rechazadas++;
								$registro_ok = 0;
							}
						}
                        $consulta_ins="INSERT INTO archivos_masivo_detalle (id_archivo, cod_novedad, nro_documento, fecha, unidad_cantidad_valor, code_error, desc_error, estado) VALUES ($archivo_id, '$cod_novedad', '$dni', STR_TO_DATE('$fecha', '%d/%m/%Y'), '$valor', $code_error, '$desc_error', '$estado')";
                        $resultadoz = mysqli_query($enlace, $consulta_ins);
                        $row_cntz = mysqli_num_rows($resultadoz);                        
                        //$query_ins = mysql_query("INSERT INTO archivos_masivo_detalle (id_archivo, cod_novedad, nro_documento, fecha, unidad_cantidad_valor, code_error, desc_error, estado) VALUES ($archivo_id, '$cod_novedad', '$dni', STR_TO_DATE('$fecha', '%d/%m/%Y'), '$valor', $code_error, '$desc_error', '$estado')");
						if ($resultadoz) $lineas_procesadas++;
					}
				}

                $update_archivo_masivo="UPDATE archivos_masivo SET estado='Procesado', registros_procesados=$lineas_procesadas WHERE id=$archivo_id";
                $resultado_update_masivo = mysqli_query($enlace, $update_archivo_masivo);

				validar_archivo($archivo_id, $lineas_rechazadas);
				return $lineas_procesadas;
			
			} else {
				// Archivo no accesible
				return -2;
			}
		} else {
			// Archivo id no valido
			return -1;
		}
	} // function


/////////////////////////////////////////////////////////////////////////////////////////////////////////////// validar fecha

    function validar_fecha($fecha)
	{
		list($dia, $mes, $anio) = explode('/', $fecha, 3);
		return checkdate($mes, $dia, $anio);
	}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////// validar archivo

    function validar_archivo($archivo_id, $lineas_rechazadas)
    {
           //global $mydb;
    $enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");

    $consulta_archivos_masivo_detalle="SELECT id, cod_novedad, nro_documento, fecha, unidad_cantidad_valor FROM archivos_masivo_detalle WHERE estado='Pendiente' AND id_archivo=$archivo_id";
    $resultado_archivos_masivo_detalle = mysqli_query($enlace, $consulta_archivos_masivo_detalle);

    while($misdatos5 = mysqli_fetch_assoc($resultado_archivos_masivo_detalle)){
            $id=$misdatos5["id"];
            $cod_novedad=$misdatos5["cod_novedad"];
            $nro_documento=$misdatos5["nro_documento"];
            $fecha=$misdatos5["fecha"];
            $unidad_cantidad_valor=$misdatos5["unidad_cantidad_valor"];


        $legajo = buscar_legajo($nro_documento);
        if ($legajo->code_error == 0) {
            $update_archivos_masivo_detalle="UPDATE archivos_masivo_detalle SET cod_empresa='$legajo->cod_empresa', id_legajo_tg=$legajo->id_legajo, nro_legajo=$legajo->nro_legajo, tipo_documento='$legajo->tipo_documento', cod_departamento='$legajo->cod_departamento' WHERE id=$id";
            $resultado_update_archivos_masivo_detalle = mysqli_query($enlace, $update_archivos_masivo_detalle);
            $novedad = buscar_novedad($cod_novedad, $legajo->cod_empresa);

            if ($novedad->code_error == 0) {
                $update_archivos_masivo_detalle="UPDATE archivos_masivo_detalle SET id_novedad_tg=$novedad->id_novedad, unidad_cantidad='$novedad->unidad_cantidad', estado='Aprobado' WHERE id=$id";
                $resultado_update_archivos_masivo_detalle = mysqli_query($enlace, $update_archivos_masivo_detalle);
            } else {
                $update_archivos_masivo_detalle="UPDATE archivos_masivo_detalle SET code_error=$novedad->code_error, desc_error='$novedad->desc_error', estado='Rechazado' WHERE id=$id";
                $resultado_update_archivos_masivo_detalle = mysqli_query($enlace, $update_archivos_masivo_detalle);

                $lineas_rechazadas++;
            }
        } else {
            $update_archivos_masivo_detalle="UPDATE archivos_masivo_detalle SET code_error=$legajo->code_error, desc_error='$legajo->desc_error', estado='Rechazado' WHERE id=$id";
            $resultado_update_archivos_masivo_detalle = mysqli_query($enlace, $update_archivos_masivo_detalle);            
            $lineas_rechazadas++;
        }
    }
    $update_archivos_masivo_detalle="UPDATE archivos_masivo SET registros_rechazados=$lineas_rechazadas WHERE id=$archivo_id";
    $resultado_update_archivos_masivo_detalle = mysqli_query($enlace, $update_archivos_masivo_detalle);

} // function


///////////////////////////////////////////////////////////////////////////////////////////////////////////////

function buscar_legajo ($dni)
{
    //global $mydb;
    $enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");
        
    $result = array();
    $consulta_legajo="SELECT cod_empresa, id_legajo, cod_departamento, nro_legajo, tipo_documento FROM legajos WHERE habilitado='S' AND id_departamento != -1 AND nro_documento=$dni";
    $resultado_consulta_legajo = mysqli_query($enlace, $consulta_legajo);
    $row_cnt_legajo = mysqli_num_rows($resultado_consulta_legajo);

//    $query = mysql_query("SELECT cod_empresa, id_legajo, cod_departamento, nro_legajo, tipo_documento FROM legajos WHERE habilitado='S' AND id_departamento != -1 AND nro_documento=$dni");
    if ($row_cnt_legajo == 1) {
        $result = mysqli_fetch_assoc($resultado_consulta_legajo);
        $result['desc_error'] = NULL;
        $result['code_error'] = 0;
    } else {
        $result['desc_error'] = $row_cnt_legajo . " legajos encontrados para el DNI en cuestion";
        $result['code_error'] = 1;
    }
    return (object)$result;
    
}
    
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

function buscar_novedad($cod_novedad, $cod_empresa)
{
    //global $mydb;
    $enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");

    $result = array();
    $consulta_tipos_novedades="SELECT id_novedad, unidad_cantidad FROM tipos_novedades WHERE cod_empresa='$cod_empresa' AND cod_novedad='$cod_novedad'";
    $resultado_tipos_novedades = mysqli_query($enlace, $consulta_tipos_novedades);
    $row_tipos_novedades = mysqli_num_rows($resultado_tipos_novedades);    
    //$query = mysql_query("SELECT id_novedad, unidad_cantidad FROM tipos_novedades WHERE cod_empresa='$cod_empresa' AND cod_novedad='$cod_novedad'");
    if ($row_tipos_novedades == 1) {
        $result = mysqli_fetch_assoc($resultado_tipos_novedades);
        $result['desc_error'] = NULL;
        $result['code_error'] = 0;
    } else {
        $result['desc_error'] = $row_tipos_novedades . " novedades encontradas para la empresa en cuestion";
        $result['code_error'] = 1;
    }
    return (object)$result;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

?>