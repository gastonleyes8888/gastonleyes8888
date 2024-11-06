<?php

//global $mydb, $msdb;
/// conexiones
// version 2.0.

$enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");
$connectionInfo = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
$conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo); //INGRESAR ORIGEN BASE DE DATOS


/// fin conexiones
	
	$last_cod_empresa = NULL;
	$lineas_insertadas = 0;
	$lineas_procesadas = 0;
	
    $consultita = "SELECT novedad_id, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM novedades WHERE estado='Aprobada' ORDER BY cod_empresa";
//$consultita = "SELECT novedad_id, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM novedades WHERE estado='Aprobada'
// and fecha like '2024-08-18' ORDER BY cod_empresa";

//$consultita = "SELECT novedad_id, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM novedades WHERE estado='Aprobada'
 //and fecha like '2024-08-18' ORDER BY cod_empresa";

    $resultadox = mysqli_query($enlace, $consultita);
    $row_cnt = mysqli_num_rows($resultadox);
    echo "cantidad: $row_cnt<br>";

    while($misdatos2 = mysqli_fetch_assoc($resultadox)){


        $novedad_id=$misdatos2["novedad_id"];
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
            echo "Conectado a $cod_empresa<br><p>";
        }
                $last_cod_empresa = $cod_empresa;
                echo "Last_cod_empresa - $last_cod_empresa<br>";
        }

		/* Modificado para insertar en tango la fecha del mes vigente  esto ya estaba comentado*/ 
		/* $m_nov =  date('m', strtotime($fecha));
		$m_vig = date('m');
		if ($m_nov != $m_vig) $fecha = date('Y-m-d'); */

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

        $consulta_object="BEGIN TRANSACTION";
        $msresult_emp=sqlsrv_query( $conn2, $consulta_object);

		if (trim($unidad_cantidad) == '$') {
            
            $insert_novedad_registrada="INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, valor_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')";
            $msresult4=sqlsrv_query( $conn2, $insert_novedad_registrada);

            echo "IF --- $id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo'<br> ---";
		} else {

            $insert_novedad_registrada2="INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, cant_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')";
            $msresult4=sqlsrv_query( $conn2, $insert_novedad_registrada2);
            echo "ELSE -- $id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo'<br>";
		}
		if ($msresult4) {
            $consulta_commit="COMMIT";
            $msresult5=sqlsrv_query( $conn2, $consulta_commit);

            $consultita = "UPDATE novedades SET estado='Procesada' WHERE novedad_id = $novedad_id";
            $resultadoy = mysqli_query($enlace, $consultita);

            if ($resultadoy) {
                echo "[+] Novedad por archivo $novedad_id cargada exitosamente\n";
                $lineas_insertadas++;
            } else {
                echo "[-] Error actualizando novedad por archivo $novedad_id en MySQL\n";
            }
		} else {
            $consulta_rollback="ROLLBACK";
            $msresult_error=sqlsrv_query( $conn2, $consulta_rollback);
    
			echo "[-] Error insertando novedad $novedad_id en Tango\n";
		}
		$lineas_procesadas++;

	} // while

	echo "Lineas procesadas: $lineas_procesadas - Lineas insertadas: $lineas_insertadas\n";


//  SELECT * FROM `novedades` WHERE `fecha` LIKE 'Aprobada';
//  SELECT * FROM `novedades` WHERE `fecha` LIKE '2024-08-18';

//  UPDATE `novedades` SET `estado`='Aprobada' WHERE `fecha` like '2024-08-18';

//SELECT * FROM `novedades` WHERE `cod_novedad` like 'DSLIC' and `fecha` like '2024-08-01'
//SELECT * FROM `novedades` WHERE `cod_novedad` LIKE 'DSLIC' AND `fecha` LIKE '2024-08%'
//UPDATE `novedades` SET `estado`='Aprobada' WHERE `cod_novedad` LIKE 'DSLIC' AND `fecha` LIKE '2024-08%'

//SELECT * FROM `novedades` WHERE `fecha` LIKE '201%' AND `estado` LIKE 'Pendiente'
//UPDATE `novedades` SET `estado`='Procesada' WHERE `fecha` LIKE '201%' AND `estado` LIKE 'Pendiente'
//UPDATE `novedades` SET `estado`='Procesada' WHERE `fecha` LIKE '2024-07%' AND `estado` LIKE 'Pendiente'

?>