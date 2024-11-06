<?php

    $connectionInfo = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "Diccionario_038665_002","Encrypt"=>"No","TrustServerCertificate"=>"No"
); //USUARIO Y CONTRASEÑA DE TANGO
    $conn=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo); //INGRESAR ORIGEN BASE DE DATOS

    $connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "CORTLE_SA","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
    $conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS


    if ( $conn ){
    echo "Conexion establecida al diccionario <br><p>";
    }else{
    echo "Conexion no establecida al diccionario <br><p>";
    die (print_r(sqlsrv_errors(), true));
    }

    if ( $conn2 ){
    echo "Conexion establecida a la empresa <br><p>";
    }else{
    echo "Conexion no establecida a la empresa <br><p>";
    die (print_r(sqlsrv_errors(), true));
    }

    $consulta = "select top 10 * from empresa";
    $consulta2= sqlsrv_query( $conn, $consulta);
    $row = (sqlsrv_fetch_array( $consulta2, SQLSRV_FETCH_ASSOC));
    echo "<pre>";
    print_r ($row);
    echo "</pre>";

    $consulta1 = "select top 10 * from STA11";
    $consulta21= sqlsrv_query( $conn2, $consulta1);
    $row2 = (sqlsrv_fetch_array( $consulta21, SQLSRV_FETCH_ASSOC));
    echo "<pre>";
    print_r ($row2);
    echo "</pre>";

?>
