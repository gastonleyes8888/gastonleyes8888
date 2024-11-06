<?php
$funciones = 1;

$default_pass = '658JufecSA';
$feriados = array ("20090817", "20091225");
$masterkey = date ("dmyH");


$mydb = NULL;
$msdb = NULL;

        function openmydb()
        {
                global $mydb;
                $mydb = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");
				//return $mydb;
				if ($mydb->connect_error) {
					die("Connection failed: " . $mydb->connect_error);
				}
				else
				{
					echo "Conectado a Ombu funciones<p>";
				}				
        }

        function openmsdb()
        {
                global $msdb;
                //$msdb = mssql_connect('TangoGestion', 'sa', 'Axoft1988'); #tenia # no //
		//$msdb = mssql_connect('192.168.0.96', 'sa', 'Axoft1988'); #
		//$msdb = mssql_connect('192.168.15.10', 'sa', 'Axoft1988'); #

		// asi si estaba
//		$msdb = mssql_connect('192.168.15.10', 'sa');

		//esto no estaba
                //$msdb = mssql_connect('192.168.15.10', 'Axoft', 'Axoft');

//                $msdb = mssql_connect('192.168.15.10', 'Axoft', 'Axoft'); este es el tango viejo.

        $connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "CORTLE_SA","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
        //$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO		SIN BASE SELECCIONADA
        $msdb=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS
		//$conn=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo); //INGRESAR ORIGEN BASE DE DATOS


		if ( $msdb ){
			echo "Conexion establecida a tango web<br><p>";
			}else{
			echo "Conexion no establecida al tango web<br><p>";
			die (print_r(sqlsrv_errors(), true));
			}

		//$msdb = mssql_connect('192.168.15.10', 'Axoft', 'Axoft');
		
                //$msdb = mssql_connect('192.168.15.10', 'Axoft1988', 'Axoft1988');                



        }

	function closemydb()
	{
		global $mydb;
		mysql_close ($mydb);
	}

        function closemsdb()
        {
                global $msdb;
				sqlsrv_close ($msdb);
        }

	function create_login()
	{
		global $mydb, $default_pass;
		$result = mysql_query("INSERT IGNORE INTO login (cod_departamento, clave, fecha_cambioclave, forzar_cambioclave, mensaje, estado) SELECT cod_departamento, '" . $default_pass . "', NOW(), 'N', '', 'Habilitado' FROM departamentos GROUP BY cod_departamento, desc_departamento");
		$result = mysql_query("UPDATE login l SET desc_departamento = (SELECT desc_departamento FROM departamentos WHERE cod_departamento = l.cod_departamento LIMIT 1)");
		// Deshabilita departamentos sin legajos
		//$result = mysql_query("UPDATE login l SET estado='Deshabilitado' WHERE NOT EXISTS (SELECT 1 FROM legajos WHERE cod_departamento = l.cod_departamento)");
	}

	function create_novedades()
	{
		global $mydb;
                $result = mysql_query("INSERT IGNORE INTO ext_tipos_novedades (cod_novedad, estado) SELECT cod_novedad, 'Habilitada' FROM tipos_novedades GROUP BY cod_novedad");
                $result = mysql_query("UPDATE ext_tipos_novedades e SET desc_novedad = (SELECT desc_novedad FROM tipos_novedades WHERE cod_novedad = e.cod_novedad LIMIT 1)");
		$result = mysql_query("INSERT INTO motivos_novedades (cod_novedad, motivo) SELECT cod_novedad, 'Sin Motivo' FROM ext_tipos_novedades tn WHERE NOT EXISTS (SELECT 1 FROM motivos_novedades WHERE cod_novedad = tn.cod_novedad)");

	}

	function create_convenios()
	{
		global $mydb;
		$result = mysql_query("INSERT IGNORE INTO ext_convenios (cod_convenio) SELECT DISTINCT(cod_convenio) FROM convenios");
		$result = mysql_query("UPDATE ext_convenios c SET desc_convenio = (SELECT desc_convenio FROM convenios WHERE cod_convenio = c.cod_convenio LIMIT 1)");
	}


	function procesar_novedades()
	{
		echo "Procesando novedades web\n";
		//procesar_novedades_web();
		echo "Procesando novedades por archivo\n";
		//procesar_novedades_archivo();
	}

	function validar_fecha($fecha)
	{
		list($dia, $mes, $anio) = explode('/', $fecha, 3);
		return checkdate($mes, $dia, $anio);
	}

	function procesar_archivo($archivo_id)
	{
		global $mydb;

		$lineas_procesadas = 0;
		$lineas_rechazadas = 0;
		$base_archivos = '/var/www/html/rrhh.farmar-net.com.ar/assets/uploads/masivo/';
		$query = mysql_query("SELECT cod_novedad, archivo FROM archivos_masivo WHERE estado='Pendiente' AND id=$archivo_id");
		if (mysql_num_rows($query) == 1) {
			list ($cod_novedad, $archivo) = mysql_fetch_row($query);
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
						$query_ins = mysql_query("INSERT INTO archivos_masivo_detalle (id_archivo, cod_novedad, nro_documento, fecha, unidad_cantidad_valor, code_error, desc_error, estado) VALUES ($archivo_id, '$cod_novedad', '$dni', STR_TO_DATE('$fecha', '%d/%m/%Y'), '$valor', $code_error, '$desc_error', '$estado')");
						if ($query_ins) $lineas_procesadas++;
					}
				}
				mysql_query("UPDATE archivos_masivo SET estado='Procesado', registros_procesados=$lineas_procesadas WHERE id=$archivo_id");
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
	}


	function buscar_legajo ($dni)
	{
		global $mydb;
			
		$result = array();
		
		$query = mysql_query("SELECT cod_empresa, id_legajo, cod_departamento, nro_legajo, tipo_documento FROM legajos WHERE habilitado='S' AND id_departamento != -1 AND nro_documento=$dni");
		if (mysql_num_rows($query) == 1) {
			$result = mysql_fetch_assoc($query);
			$result['desc_error'] = NULL;
			$result['code_error'] = 0;
		} else {
			$result['desc_error'] = mysql_num_rows($query) . " legajos encontrados para el DNI en cuestion";
			$result['code_error'] = 1;
		}
		return (object)$result;
		
	}

		
	function buscar_novedad($cod_novedad, $cod_empresa)
	{
		global $mydb;
		
		$result = array();
		
		$query = mysql_query("SELECT id_novedad, unidad_cantidad FROM tipos_novedades WHERE cod_empresa='$cod_empresa' AND cod_novedad='$cod_novedad'");
		if (mysql_num_rows($query) == 1) {
			$result = mysql_fetch_assoc($query);
			$result['desc_error'] = NULL;
			$result['code_error'] = 0;
		} else {
			$result['desc_error'] = mysql_num_rows($query) . " novedades encontradas para la empresa en cuestion";
			$result['code_error'] = 1;
		}
		return (object)$result;
	}

	function borrar_recibos_vencidos()
	{
		global $mydb;
		$query = mysql_query("DELETE FROM recibos where NOW() > DATE_ADD(fecha_alta, INTERVAL 35 DAY)");
	
	}



        function validar_archivo($archivo_id, $lineas_rechazadas)
        {
           	global $mydb;


		$query = mysql_query("SELECT id, cod_novedad, nro_documento, fecha, unidad_cantidad_valor FROM archivos_masivo_detalle WHERE estado='Pendiente' AND id_archivo=$archivo_id");
		while (list($id, $cod_novedad, $nro_documento, $fecha, $unidad_cantidad_valor) = mysql_fetch_row($query)) {
			$legajo = buscar_legajo($nro_documento);
			if ($legajo->code_error == 0) {
				mysql_query("UPDATE archivos_masivo_detalle SET cod_empresa='$legajo->cod_empresa', id_legajo_tg=$legajo->id_legajo, nro_legajo=$legajo->nro_legajo, tipo_documento='$legajo->tipo_documento', cod_departamento='$legajo->cod_departamento' WHERE id=$id");
				$novedad = buscar_novedad($cod_novedad, $legajo->cod_empresa);
				if ($novedad->code_error == 0) {
					mysql_query("UPDATE archivos_masivo_detalle SET id_novedad_tg=$novedad->id_novedad, unidad_cantidad='$novedad->unidad_cantidad', estado='Aprobado' WHERE id=$id");
				} else {
					mysql_query("UPDATE archivos_masivo_detalle SET code_error=$novedad->code_error, desc_error='$novedad->desc_error', estado='Rechazado' WHERE id=$id");
					$lineas_rechazadas++;
				}
			} else {
				mysql_query("UPDATE archivos_masivo_detalle SET code_error=$legajo->code_error, desc_error='$legajo->desc_error', estado='Rechazado' WHERE id=$id");
				$lineas_rechazadas++;
			} 
		}
		mysql_query("UPDATE archivos_masivo SET registros_rechazados=$lineas_rechazadas WHERE id=$archivo_id");
	}

	function procesar_archivos_pendientes()
	{
		global $mydb;

		$query = mysql_query("SELECT id, archivo FROM archivos_masivo WHERE estado = 'Pendiente'");
		while (list($id, $archivo) = mysql_fetch_row($query)) {
			$l = procesar_archivo($id);
			echo "[+] $archivo: $l procesadas\n";
		}
	}

function procesar_novedades_web()
{
	//global $mydb, $msdb;
	

	/*
	$last_cod_empresa = NULL;
	$lineas_insertadas = 0;
	$lineas_procesadas = 0;
	
	$result = mysql_query("SELECT novedad_id, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM novedades WHERE estado='Aprobada' ORDER BY cod_empresa");
	while (list($novedad_id, $cod_empresa, $fecha, $unidad_cantidad, $unidad_cantidad_valor, $id_legajo_tg, $id_novedad_tg) = mysql_fetch_row($result)) {
		if ($last_cod_empresa != $cod_empresa) {
	        if (! mssql_select_db($cod_empresa, $msdb)) {
                echo "[-] No puedo seleccionar DB $dbname\n";
                continue;
			} else {
				$last_cod_empresa = $cod_empresa;
			}
		}
*/


		/* Modificado para insertar en tango la fecha del mes vigente  esto ya estaba comentado*/ 
		/* $m_nov =  date('m', strtotime($fecha));
		$m_vig = date('m');
		if ($m_nov != $m_vig) $fecha = date('Y-m-d'); */


/*

		$m_nov =  date('m', strtotime($fecha));
		$d_nov = date('d', strtotime($fecha));
		$a_nov = date('Y', strtotime($fecha));

		$m_vig = date('m');
		$a_vig = date('Y');
		$ud_vig = date('t');

		if (($m_nov != $m_vig) or ($a_nov != $a_vig)) {
        		if (intval($d_nov) > intval($ud_vig)) $d_nov = $ud_vig;
        		$fecha = date("Y-m-d", strtotime("$a_vig-$m_vig-$d_nov"));
		}


		mssql_query("BEGIN TRANSACTION");
		if (trim($unidad_cantidad) == '$') {
			//$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, valor_novedad, origen_novedad) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo')");
			$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, valor_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')");			
		} else {
			//$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, cant_novedad, origen_novedad) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo')");
			$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, cant_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')");			
		}
		if ($msresult) {
			mssql_query("COMMIT");
			$myresult = mysql_query("UPDATE novedades SET estado='Procesada' WHERE novedad_id = $novedad_id");
			if (mysql_affected_rows() == 1) {
				echo "[+] Novedad $novedad_id cargada exitosamente\n";
				$lineas_insertadas++;
			} else {
				echo "[-] Error actualizando novedad $novedad_id en MySQL\n";
			}
		} else {
			mssql_query("ROLLBACK");
			echo "[-] Error insertando novedad $novedad_id en Tango\n";
		}
		$lineas_procesadas++;

	}
	echo "Lineas procesadas: $lineas_procesadas - Lineas insertadas: $lineas_insertadas\n";
	*/
}

function procesar_novedades_archivo()
{
	/*
	global $mydb, $msdb;
	
	$last_cod_empresa = NULL;
	$lineas_insertadas = 0;
	$lineas_procesadas = 0;
	
	$result = mysql_query("SELECT id, id_archivo, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM archivos_masivo_detalle WHERE estado='Aprobado' ORDER BY cod_empresa");
	while (list($novedad_id, $id_archivo, $cod_empresa, $fecha, $unidad_cantidad, $unidad_cantidad_valor, $id_legajo_tg, $id_novedad_tg) = mysql_fetch_row($result)) {
		if ($last_cod_empresa != $cod_empresa) {
	        if (! mssql_select_db($cod_empresa, $msdb)) {
                echo "[-] No puedo seleccionar DB $dbname\n";
                continue;
			} else {
				$last_cod_empresa = $cod_empresa;
			}
		}
		mssql_query("BEGIN TRANSACTION");
		if (trim($unidad_cantidad) == '$') {
			//$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, valor_novedad, origen_novedad) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo')");
			$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, valor_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')");			
		} else {
			//$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, cant_novedad, origen_novedad) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo')");
			$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, cant_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')");			
		}
		if ($msresult) {
			mssql_query("COMMIT");
			$myresult = mysql_query("UPDATE archivos_masivo_detalle SET estado='Procesado' WHERE id = $novedad_id");
			if (mysql_affected_rows() == 1) {
				echo "[+] Novedad por archivo $novedad_id cargada exitosamente\n";
				$lineas_insertadas++;
			} else {
				echo "[-] Error actualizando novedad por archivo $novedad_id en MySQL\n";
			}
		} else {
			$myresult = mysql_query("UPDATE archivos_masivo_detalle SET estado='Rechazado', code_error = 1, desc_error = '" . mssql_get_last_message() . "' WHERE id = $novedad_id");
			mssql_query("ROLLBACK");
			$myresult = mysql_query("UPDATE archivos_masivo SET registros_rechazados = registros_rechazados + 1 WHERE id = $id_archivo");
			echo "[-] Error insertando novedad por archivo $novedad_id en Tango\n";
		}
		$lineas_procesadas++;

	}
	echo "Lineas procesadas por archivo: $lineas_procesadas - Lineas insertadas: $lineas_insertadas\n";

	*/
}


function aprobar_novedades_automaticas()
{
	/*
	global $mydb;
	$result = mysql_query("UPDATE novedades n, ext_tipos_novedades e SET n.estado = 'Aprobada' WHERE n.cod_novedad = e.cod_novedad AND e.aprobacion='Automatica' AND n.estado='Pendiente' AND datediff(now(), n.fecha_alta) > 2");
	*/
}



function procesar_novedades_archivov2()
{
	
    $enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");

    //$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "CORTLE_SA","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
    $connectionInfo = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO    
    $conn=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo); //INGRESAR ORIGEN BASE DE DATOS

	//$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "$dbname","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
	//$conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS
	
	$last_cod_empresa = NULL;
	$lineas_insertadas = 0;
	$lineas_procesadas = 0;

	$consultita = "SELECT id, id_archivo, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM archivos_masivo_detalle WHERE estado='Aprobado' ORDER BY cod_empresa";
	$result = mysqli_query($enlace, $consultita);
	$row_cnt = mysqli_num_rows($resultado);
	echo " cantidad regs consulta 2: $row_cnt<br>";

	while($misdatos2 = mysqli_fetch_assoc($resultado)){

		$id=$misdatos2["id"];
		$id_archivo=$misdatos2["id_archivo"];
		$cod_empresa=$misdatos2["cod_empresa"];				
		$fecha=$misdatos2["fecha"];				
		$unidad_cantidad=$misdatos2["unidad_cantidad"];				
		$unidad_cantidad_valor=$misdatos2["unidad_cantidad_valor"];				
		$id_legajo_tg=$misdatos2["id_legajo_tg"];				
		$id_novedad_tg=$misdatos2["id_novedad_tg"];				

		if ($last_cod_empresa != $cod_empresa) {

			$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "$cod_empresa","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
        	$conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

        if (! $conn2) {
			echo "[-] No puedo seleccionar DB $cod_empresa\n";
			continue;
	    }else{
            echo "Conectado a $cod_empresa<br>";
        }
	        //if (! mssql_select_db($cod_empresa, $msdb)) {
				$last_cod_empresa = $cod_empresa;
		}
	}
	
	//$result = mysql_query("SELECT id, id_archivo, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM archivos_masivo_detalle WHERE estado='Aprobado' ORDER BY cod_empresa");
	
	
}// function
