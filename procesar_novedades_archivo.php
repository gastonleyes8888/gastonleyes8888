<?php

//version 1.0 ok

$enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");

$connectionInfo = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO    
$conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo); //INGRESAR ORIGEN BASE DE DATOS

//SELECT * FROM `archivos_masivo_detalle` WHERE `id_archivo` LIKE '1156'
//UPDATE `archivos_masivo_detalle` SET `estado`='Aprobado', `desc_error`='', `code_error`='' WHERE `id_archivo` LIKE '1156'

$last_cod_empresa = NULL;
$lineas_insertadas = 0;
$lineas_procesadas = 0;

//SELECT * FROM `archivos_masivo_detalle` WHERE `fecha` like '2024-09-02'

//$consultita = "SELECT id, id_archivo, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM archivos_masivo_detalle WHERE estado='Aprobado' ORDER BY cod_empresa";
$consultita = "SELECT id, id_archivo, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM archivos_masivo_detalle WHERE estado='Aprobado' ORDER BY cod_empresa";
//$consultita = "SELECT id, id_archivo, cod_empresa, fecha, unidad_cantidad, unidad_cantidad_valor, id_legajo_tg, id_novedad_tg FROM archivos_masivo_detalle WHERE id_archivo like '1176' and estado like 'Rechazado'";
$resultadox = mysqli_query($enlace, $consultita);
$row_cnt = mysqli_num_rows($resultadox);
echo " cantidad regs consulta: $row_cnt<br>";

while($misdatos2 = mysqli_fetch_assoc($resultadox)){

    $novedad_id=$misdatos2["id"];
    $id_archivo=$misdatos2["id_archivo"];
    
    $cod_empresa=$misdatos2["cod_empresa"];
    $fecha=$misdatos2["fecha"];
    $unidad_cantidad=$misdatos2["unidad_cantidad"];
    $unidad_cantidad_valor=$misdatos2["unidad_cantidad_valor"];
    $id_legajo_tg=$misdatos2["id_legajo_tg"];
    $id_novedad_tg=$misdatos2["id_novedad_tg"];

    echo "cod_empresa: $cod_empresa<br>";
    
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
    // funciona

    $consulta_object="BEGIN TRANSACTION";
    $msresult_emp=sqlsrv_query( $conn2, $consulta_object);

    if (trim($unidad_cantidad) == '$') {
        $insert_novedad_registrada="INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, valor_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')";
        $msresult4=sqlsrv_query( $conn2, $insert_novedad_registrada);
    } else {
        $insert_novedad_registrada2="INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, cant_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')";
        $msresult4=sqlsrv_query( $conn2, $insert_novedad_registrada2);
    }
    if ($msresult4) {
        $consulta_commit="COMMIT";
        $msresult5=sqlsrv_query( $conn2, $consulta_commit);

        $consultita = "UPDATE archivos_masivo_detalle SET estado='Procesado' WHERE id = $novedad_id";
        $resultadoy = mysqli_query($enlace, $consultita);

        if ($resultadoy) {
            echo "[+] Novedad por archivo $novedad_id cargada exitosamente\n";
            $lineas_insertadas++;
        } else {
            echo "[-] Error actualizando novedad por archivo $novedad_id en MySQL\n";
        }
    } else {

        $consulta_error = "UPDATE archivos_masivo_detalle SET estado='Rechazado', code_error = 1, desc_error = 'Error nomas' WHERE id = $novedad_id";
        $resultado_error = mysqli_query($enlace, $consulta_error);

        $consulta_rollback="ROLLBACK";
        $msresult_error=sqlsrv_query( $conn2, $consulta_rollback);


        $consulta_rechazados = "UPDATE archivos_masivo SET registros_rechazados = registros_rechazados + 1 WHERE id = $id_archivo";
        $resultado_rechazados = mysqli_query($enlace, $consulta_rechazados);
        echo "[-] Error insertando novedad por archivo $novedad_id en Tango\n";
    }
    $lineas_procesadas++;


} // while

?>
