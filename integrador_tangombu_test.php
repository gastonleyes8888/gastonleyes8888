<?php
// VERSION 3.0 PARA INSERTAR A TANGO DESDE LICENCIAS_FCIA GALACTUS

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Connect to MySQL Database
    $enlace_ombu = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");
    $enlace_claro = mysqli_connect("181.117.7.13", "galactus", "1qaz", "u551789018_portal");

    //$resultado_claro = mysqli_query($enlace_claro, $consulta);

    $no_cargados = fopen("no_cargados.csv", "w");
    $cargados = fopen("cargados.csv", "w");    

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

    $consulta = "SELECT * FROM licencias_fcia where (aux3 like 'NOFAR_SRL') and (aux5 like 'APER')";
    $resultado_claro = mysqli_query($enlace_claro, $consulta);
    $row_cnt = mysqli_num_rows($resultado_claro);

    if($row_cnt==0){
        echo "<b>NO HAY DATOS EN LA CONSULTA DE licencias_fcia <b><br>";
        exit;

    }else{
        echo "COMIENZO INTEGRACION --- Cantidad DE REGISTROS---------------------------------> $row_cnt<br>";

        $resultado = mysqli_query($enlace_claro, $consulta);
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

            echo "-----------------------------------------------------------------------<br><p>";

            echo "Datos: legajo_id: $legajo_id<br>";
            echo "Datos: dni: $dni<br>";
            echo "Datos: novedad: $novedad<br>";
            echo "Datos: cantidad_dias_licencia: $cant_dias_licencia<br>";
            echo "Datos: unidad_cantidad[1]: ".trim($unidad_cantidad2[0])."<br>";
            echo "Datos: id_novedad: $id_novedad<br>";

            echo "Datos: base_tango: $base_tango<br>";
            echo "Datos: nro_legajo: $nro_legajo<br>";
            echo "Datos: cod_novedad: $cod_novedad<br><p>";

            echo "cant_dias_suspension - $cant_dias_suspension<br>";
            echo "cant_dias_enfermedad - $cant_dias_enfermedad<br>";
            echo "cant_dias_licencia - $cant_dias_licencia<br>";

            echo "-----------------------------------------------------------------------<br><p>";

         ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            $consulta_update = "SELECT * FROM tipos_novedades_updated where cod_novedad like '$cod_novedad' and cod_empresa like '$base_tango' limit 1";
            $resultado_update = mysqli_query($enlace_ombu, $consulta_update);
            $row_cnt_tipos_novedades = mysqli_num_rows($resultado_update);

            if($row_cnt_tipos_novedades==0){
                echo "<b>NO HAY DATOS EN LA CONSULTA DE tipos_novedades_updated <b><br>";
                exit;
            }

            while($misdatos2 = mysqli_fetch_assoc($resultado_update)){
              $cod_novedadx=$misdatos2["cod_novedad"];
              $id_novedadx=$misdatos2["id_novedad"];
              $unidad_cantidadx=$misdatos2["unidad_cantidad"];
              echo "TIPO NOVEDAD: $cod_novedadx - $id_novedadx - $unidad_cantidadx<br>";
            } // while

           

///////////////////////////////////////////////////////////////////////////////// TEST SI FUNCA 
            if (trim($unidad_cantidadx) == '$') { // insertar PESOS
                //$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, valor_novedad, origen_novedad, origen_cloud) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo', 'Externo')");			

                $connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "$base_tango","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
                $conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS
                //$msresult = mssql_query("INSERT INTO novedad_registrada (id_novedad, id_legajo,	fecha_novedad, cant_novedad, origen_novedad) VALUES ($id_novedad_tg, $id_legajo_tg, Convert(varchar(30), '$fecha', 120), $unidad_cantidad_valor, 'Externo')");
                $formato=".000";
                $fecha_final=$fecha_carga_lic.$formato;

                echo "$fecha_final<br>";

                $id_novedadx=intval($id_novedadx);
                $legajo_id=intval($legajo_id);
                $fecha_final=$fecha_final;
                $activo=intval($activo);

                $fecha_final = new DateTime($fecha_final); // le doy el mismo formato que necesita el tango.
                echo $fecha_final->format('Y-m-d H:i:s').'<br>';

                $sql = "INSERT INTO novedad_registrada (id_novedad, id_legajo, fecha_novedad, valor_novedad, origen_novedad, origen_cloud) VALUES (?, ?, ?, ?, ?, ?)";
                $params = array( $id_novedadx, &$legajo_id, &$fecha_final, &$activo, 'Galactus', 'Galactus');
                 $stmt = sqlsrv_query( $conn2, $sql, $params);

                if( $stmt === false ) {
                    $errorcito=print_r( sqlsrv_errors(), true);
                    @$imprimir_cargados_pesos = $legajo_id.';'.$dni.';'.$novedad.';'.$cant_dias_licencia.';'.$base_tango.';'.$nro_legajo.';'.$cod_novedad.';'.$aid_licencia.';'.$errorcito;
                    fwrite($no_cargados, $imprimir_cargados_pesos . PHP_EOL);                                                        
                    die( print_r( sqlsrv_errors(), true));
                }
                else{

                    @$imprimir_cargados_pesos = $legajo_id.';'.$dni.';'.$novedad.';'.$cant_dias_licencia.';'.$base_tango.';'.$nro_legajo.';'.$cod_novedad.';'.$aid_licencia;
                    fwrite($cargados, $imprimir_cargados_pesos . PHP_EOL);

                    echo "PESOS $ --> 1 registro agregado! $aid_licencia - $id_novedadx - $legajo_id - $fecha_final - $activo - Galactus -Externo<br>";
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
                    die( print_r( sqlsrv_errors(), true));
                }
                else{
                    @$imprimir_cargados_pesos = $legajo_id.';'.$dni.';'.$novedad.';'.$cant_dias_licencia.';'.$base_tango.';'.$nro_legajo.';'.$cod_novedad.';'.$aid_licencia;
                    fwrite($cargados, $imprimir_cargados_pesos . PHP_EOL);

                    echo "DIAS --> 1 registro agregado!----> $aid_licencia - $id_novedadx - $legajo_id - - $activo - Galactus -Externo<br>";
                }

            } // if

        } // while
        
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} // if si tiene registros

fclose($no_cargados);
fclose($cargados);

// valores que tiene que insertar
// $cant_dias_suspension  - // $cant_dias_enfermedad - //   $cant_dias_licencia //


?>