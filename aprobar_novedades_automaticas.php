<?php

//global $mydb, $msdb;
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

    $consultita = "UPDATE novedades n, ext_tipos_novedades e SET n.estado = 'Aprobada' WHERE n.cod_novedad = e.cod_novedad AND e.aprobacion='Automatica' 
    AND n.estado='Pendiente' AND datediff(now(), n.fecha_alta) > 2";

    //$consultita = "UPDATE novedades n, ext_tipos_novedades e SET n.estado = 'Aprobada' WHERE n.cod_novedad = e.cod_novedad AND e.aprobacion='Automatica' AND n.estado='Pendiente'";
    $resultadox = mysqli_query($enlace, $consultita);
    $row_cnt = mysqli_num_rows($resultadox);

    echo "Cantidad: $row_cnt<br>";

?>