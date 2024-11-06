<?php

// VERSION 3.0 PARA INSERTAR A TANGO DESDE licencias_facor GALACTUS
// inserta la palabra TANGO en aux7 para marcar que fue insertado correctamente a TANGUITO.
// inserta la letra E en estado para marcar que hubo un error.

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Connect to MySQL Database
    //$enlace_ombu = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");
    $enlace_galactus = mysqli_connect("181.117.7.13", "galactus", "1qaz", "u551789018_portal");

    if (!$enlace_galactus) {
	    die("Connection failed");
        echo "Error en conexión a Galactus!!<br>";
        exit;
	}
	else
	{
	    echo "conectado a galactus<p>";
	}

    $no_cargados = fopen("no_cargados_facor.csv", "a");
    $cargados = fopen("cargados_facor.csv", "a");

    //$row_cnt = mysqli_num_rows($resultado);

    $connectionInfo = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
    $conn=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo); //INGRESAR ORIGEN BASE DE DATOS


    if ( $conn ){
        echo "Conexion establecida a la TANGO CLOUD <br><p>";
    }else{
        echo "Conexion no establecida a la empresa <br><p>";
        die (print_r(sqlsrv_errors(), true));
        exit;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $activo="";

    //$consulta = "SELECT * FROM licencias_facor where (aux3 like 'NOFAR_SRL') and (aux5 like 'APER')";
    //$consulta = "SELECT * FROM licencias_facor where aid_licencia like '242'";
    $hoy = date("Y-m-d");    
    //$consulta = "SELECT * FROM licencias_facor where (aux7 not like 'TANGO') and (fecha_carga_lic LIKE '$hoy%')";
    $consulta = "SELECT * FROM licencias_facor where (aux7 not like 'TANGO')";

    //$consulta = "SELECT * FROM licencias_facor where (aux7 not like 'TANGO') and (fecha_carga_lic LIKE '$hoy%') and aux5 NOT LIKE ''";
    //$consulta = "SELECT * FROM licencias_facor where (aux7 not like 'TANGO') and (fecha_carga_lic LIKE '2024-10-13%')";

    $resultado_claro = mysqli_query($enlace_galactus, $consulta);

    $row_cnt = mysqli_num_rows($resultado_claro);

    if($row_cnt==0){
        echo "<b>NO HAY DATOS EN LA CONSULTA DE licencias_facor </b><br>";
        exit;

    }else{
        echo "COMIENZO INTEGRACION --- Cantidad DE REGISTROS------------------------------------------------------------------> $row_cnt<br>";

        $resultado = mysqli_query($enlace_galactus, $consulta);
        $row_cnt = mysqli_num_rows($resultado);
       
        while($misdatos = mysqli_fetch_assoc($resultado)){

            $legajo_id=$misdatos["legajo_id"];
            $aid_licencia=$misdatos["aid_licencia"];            
            $dni=$misdatos["dni"];
            $novedad= $misdatos["novedad"]; // es el que hay que extrar el campo DIAS O PESOS O UNIDAD

            $unidad_cantidad=explode("(",$novedad); // divido por caracter (
            $unidad_cantidad2=explode(")",$unidad_cantidad[1]); // divido por caracter ) // ES EL PIVOTE 
            //$unidad_cantidad[1];

            $id_novedad=$misdatos["aux2"]; // ejmplo 50 SI O SI PARA TANGO
            $base_tango=$misdatos["aux3"]; // ejemplo NUEVA_NORTE_SCS
            $nro_legajo=$misdatos["aux4"];
            $cod_novedad=$misdatos["aux5"]; // ejemplo TARDANZA SI O SI PARA TANGO
            $fecha_carga_lic=$misdatos["fecha_carga_lic"]; // Fecha carga licencia

        // $cant_dias_suspension  - // $cant_dias_enfermedad - //   $cant_dias_licencia //

            $cant_dias_suspension=$misdatos["cant_dias_suspension"];
            $cant_dias_enfermedad=$misdatos["cant_dias_enfermedad"];
            $cant_dias_licencia=$misdatos["cant_dias_licencia"];
            $dias_vacaciones=$misdatos["dias_vacaciones"];    
         
            
            if ($cant_dias_suspension>0){
                $activo = $cant_dias_suspension;
            }

            if ($cant_dias_enfermedad>0){
                $activo = $cant_dias_enfermedad;
            }            

            if ($cant_dias_licencia>0){
                $activo = $cant_dias_licencia;
            }

            
            if ($dias_vacaciones>0){
                $activo = $dias_vacaciones;
            }


//       $cant_dias_licencia=$misdatos["cant_dias_licencia}"];
//////////////////////////////////////////////////////////////////////////////////////////////////////

            echo "-----------------------------------------------------------------------------------------------------------------------------------------------------------------<br><p>";

            echo "Datos: legajo_id: $legajo_id<br>";
            echo "Datos: dni: $dni<br>";
            echo "Datos: novedad: $novedad<br>";
            //echo "Datos: unidad_cantidad[1]: ".trim($unidad_cantidad2[0])."<br>";
            echo "Datos: id_novedad: $id_novedad<br>";

            echo "Datos: base_tango: $base_tango<br>";
            //echo "Datos: nro_legajo: $nro_legajo<br>";
            echo "Datos: cod_novedad: $cod_novedad<br><p>";

            echo "cant_dias_suspension - <b>$cant_dias_suspension</b> -  cant_dias_enfermedad - <b>$cant_dias_enfermedad</b>-  cant_dias_licencia - <b>$cant_dias_licencia</b>- -cant_dias_Vacaciones - <b>$dias_vacaciones</b> - - <br>";
//            echo "cant_dias_enfermedad - $cant_dias_enfermedad<br>";
 //           echo "cant_dias_licencia - $cant_dias_licencia<br>";
  //          echo "cant_dias_Vacaciones - $dias_vacaciones<br>";

            echo "-----------------------------------------------------------------------------------------------------------------------------------------------------------------<br><p>";

         ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            $consulta_update = "SELECT * FROM tipos_novedades_updated where cod_novedad like '$cod_novedad' and cod_empresa like '$base_tango' limit 1";
            $resultado_update = mysqli_query($enlace_galactus, $consulta_update);
            $row_cnt_tipos_novedades = mysqli_num_rows($resultado_update);

            if($row_cnt_tipos_novedades==0){
                echo "<b>NO HAY DATOS EN LA CONSULTA DE tipos_novedades_updated - $cod_novedad -  $base_tango</b><br>";
                //exit;
            }

            while($misdatos2 = mysqli_fetch_assoc($resultado_update)){

              $cod_novedadx=$misdatos2["cod_novedad"];
              $id_novedadx=$misdatos2["id_novedad"];
              $unidad_cantidadx=$misdatos2["unidad_cantidad"];
              echo "TIPO NOVEDAD: $cod_novedadx - ID NOVEDAD: $id_novedadx - UNIDAD: $unidad_cantidadx<br>";

            } // while

           

///////////////////////////////////////////////////////////////////////////////// TEST SI FUNCA 
            if (trim($unidad_cantidadx) == '$') { // insertar PESOS

                $connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "$base_tango","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
                $conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS
                //$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, cant_novedad, origen_novedad) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo')");
                $formato=".000";

                $fecha_final=$fecha_carga_lic.$formato;

                //echo "Fecha insertar: $fecha_final<br>";

                $id_novedadx=intval($id_novedadx);
                $legajo_id=intval($legajo_id);
                $fecha_final=$fecha_final;
                $activo=intval($activo);

                $fecha_final = new DateTime($fecha_final); // le doy el mismo formato que necesita el tango.
                echo "Fecha final insertar: ".$fecha_final->format('Y-m-d H:i:s').'<br>';

                $sql = "INSERT INTO novedad_registrada (id_novedad, id_legajo, fecha_novedad, valor_novedad, origen_novedad, origen_cloud) VALUES (?, ?, ?, ?, ?, ?)";
                $params = array( $id_novedadx, &$legajo_id, &$fecha_final, &$activo, 'Galactus', 'Galactus');
                $stmt = sqlsrv_query( $conn2, $sql, $params);

                if( $stmt === false ) {
                    $errorcito=print_r( sqlsrv_errors(), true);
                    @$imprimir_cargados_pesos = $legajo_id.';'.$dni.';'.$novedad.';'.$cant_dias_licencia.';'.$base_tango.';'.$nro_legajo.';'.$cod_novedad.';'.$aid_licencia.';'.$errorcito;
                    fwrite($no_cargados, $imprimir_cargados_pesos . PHP_EOL);

                    $consulta_update = "UPDATE licencias_facor SET estado = 'E' WHERE aid_licencia = '$aid_licencia'";
                    $resultado_update = mysqli_query($enlace_galactus, $consulta_update);

                    //die( print_r( sqlsrv_errors(), true));
                    echo "Errors() - $imprimir_cargados_pesos<br>";
                }
                else{

                    @$imprimir_cargados_pesos = $legajo_id.';'.$dni.';'.$novedad.';'.$cant_dias_licencia.';'.$base_tango.';'.$nro_legajo.';'.$cod_novedad.';'.$aid_licencia;
                    fwrite($cargados, $imprimir_cargados_pesos . PHP_EOL);

                    echo "PESOS $ --> 1 registro agregado! $aid_licencia - $id_novedadx - $legajo_id - $fecha_final - $activo - Galactus -Externo<br>";

                    $consulta_update = "UPDATE licencias_facor SET aux7 = 'TANGO' WHERE aid_licencia = '$aid_licencia'";
                    $resultado_update = mysqli_query($enlace_galactus, $consulta_update);                    
                }

            } else { // insertar DIAS

                $connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "$base_tango","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
                $conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

                $formato=".000";
                $fecha_final=$fecha_carga_lic.$formato;

                echo "$fecha_final<br>";

                $id_novedadx=intval($id_novedadx);
                $legajo_id=intval($legajo_id);
                $fecha_final=$fecha_final;
                $activo=intval($activo);

                $fecha_final = new DateTime($fecha_final); // le doy el mismo formato que necesita el tango.
                //echo $fecha_final->format('Y-m-d H:i:s').'<br>';

                $sql = "INSERT INTO novedad_registrada (id_novedad, id_legajo, fecha_novedad, cant_novedad, origen_novedad, origen_cloud) VALUES (?, ?, ?, ?, ?, ?)";
                $params = array(&$id_novedadx, &$legajo_id, &$fecha_final, &$activo, 'Galactus', 'Galactus');

                $stmt = sqlsrv_query( $conn2, $sql, $params);

                if( $stmt === false ) {
                    $errorcito=print_r( sqlsrv_errors(), true);
                    @$imprimir_cargados_pesos = $legajo_id.';'.$dni.';'.$novedad.';'.$cant_dias_licencia.';'.$base_tango.';'.$nro_legajo.';'.$cod_novedad.';'.$aid_licencia.';'.$errorcito;
                    fwrite($no_cargados, $imprimir_cargados_pesos . PHP_EOL);

                    $consulta_update = "UPDATE licencias_facor SET estado = 'E' WHERE aid_licencia = '$aid_licencia'";
                    $resultado_update = mysqli_query($enlace_galactus, $consulta_update);

                    //die( print_r( sqlsrv_errors(), true));
                    echo "Errors() - $imprimir_cargados_pesos<br>";                }
                else{

                    @$imprimir_cargados_pesos = $legajo_id.';'.$dni.';'.$novedad.';'.$cant_dias_licencia.';'.$base_tango.';'.$nro_legajo.';'.$cod_novedad.';'.$aid_licencia;
                    fwrite($cargados, $imprimir_cargados_pesos . PHP_EOL);

                    echo "DIAS --> 1 registro agregado!----> $aid_licencia - $id_novedadx - $legajo_id - - $activo - Galactus -Externo<br>";
                    $consulta_update = "UPDATE licencias_facor SET aux7 = 'TANGO' WHERE aid_licencia = '$aid_licencia'";
                    $resultado_update = mysqli_query($enlace_galactus, $consulta_update);                                        
                }

            } // if

        } // while
        
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} // if si tiene registros

fclose($no_cargados);
fclose($cargados);

// valores que tiene que insertar
// $cant_dias_suspension  - // $cant_dias_enfermedad - //   $cant_dias_licencia //

// pasar al tango esta consulta
//SELECT cod_empresa, id_novedad_tg, id_legajo_tg, fecha, unidad_cantidad_valor, unidad_cantidad FROM novedades WHERE estado LIKE 'Pendiente'

?>