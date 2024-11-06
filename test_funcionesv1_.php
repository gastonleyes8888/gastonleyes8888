<?php


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Connect to MySQL Database
    $enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");


	if (!$enlace) {
	    die("Connection failed");
	}
	else
	{
	    echo "conectado a ombu<p>";
	}


    $enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");


    //$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "CORTLE_SA","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
    $connectionInfo = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO    
    $conn=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo); //INGRESAR ORIGEN BASE DE DATOS


    if ( $conn ){
    echo "Conexion establecida a la empresa <br><p>";
    }else{
    echo "Conexion no establecida a la empresa <br><p>";
    die (print_r(sqlsrv_errors(), true));
    }

/*
    $consultax = "TRUNCATE TABLE empresas_updated";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "TRUNCATE TABLE departamentos_updated";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "TRUNCATE TABLE legajos_updated";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "TRUNCATE TABLE bancos_updated";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "TRUNCATE TABLE tipos_novedades_updated";
	$resultadox = mysqli_query($con, $consultax);				

	$consultax = "TRUNCATE TABLE convenios_updated";
	$resultadox = mysqli_query($con, $consultax);		

	//$consultax = "CREATE TABLE legajos_updated AS (SELECT * FROM legajos)";
	//$resultadox = mysqli_query($con, $consultax);		
	//exit;			
*/


/*
	$consultax = "CREATE TABLE empresas_updated AS (SELECT * FROM empresas)";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "CREATE TABLE departamentos_updated AS (SELECT * FROM departamentos)";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "CREATE TABLE legajos_updated2 AS (SELECT * FROM legajos)";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "CREATE TABLE bancos_updated AS (SELECT * FROM bancos)";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "CREATE TABLE tipos_novedades_updated AS (SELECT * FROM tipos_novedades)";
	$resultadox = mysqli_query($con, $consultax);				

	$consultax = "CREATE TABLE convenios_updated AS (SELECT * FROM convenios)";
	$resultadox = mysqli_query($con, $consultax);					

exit;
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


$rapido = 0;
if (!isset ($funciones)) include ("funcionesv2.php");
//if (openmydb() == NULL) die('Imposible conectar a MySQLX');
//if (openmsdb() == NULL) die('Imposible conectar a MSSQL');


//unlink("/home/operac/ombu2/csv/control_tablas.csv");
//$file = fopen("/home/operac/ombu2/csv/control_tablas.txt", "w");

//exit;

$tablas = array("empresas", "departamentos", "legajos", "bancos", "obras_sociales", "tipos_novedades", "convenios");

$tablas_rapido = array("legajos");

// Si o si porque cambian los idx
if (date("d") < 22) procesar_novedades(); // Para no meter las novedades web despues del 21
else procesar_novedades_archivo();



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function procesar_tabla($table) // procesar tabla
{
	global $mydb; 
	echo "[+] Procesando tabla $table\n";
	$sufijo = date('w');
	$old_table = $table . "_" . $sufijo;

	//$result = mysql_query("DROP TABLE IF EXISTS $old_table CASCADE");
	//$result = mysql_query("RENAME TABLE $table TO $old_table");
	//$result = mysql_query("CREATE TABLE $table LIKE $old_table");
	//$result = mysql_query("CREATE TABLE $table AS SELECT * FROM  $old_table WHERE 1=2");
}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//$truncar_legajos_updated = mysql_query("TRUNCATE TABLE legajos_updated");
//echo "Tabla legajos update truncada";


if ($rapido == 1) foreach ($tablas_rapido as $tabla) procesar_tabla($tabla);
else foreach ($tablas as $tabla) procesar_tabla($tabla);



//$msresult1 =  mssql_query ("SELECT name, dbid FROM master..sysdatabases");

$consulta_bases="SELECT name, dbid FROM master..sysdatabases";
$msresult1= sqlsrv_query( $conn, $consulta_bases);

//$msresult1 =  mssql_query ("SELECT name, dbid FROM master..sysdatabases");

//while (list ($dbname) = mssql_fetch_row ($msresult1)) {
    while ($row = sqlsrv_fetch_array( $msresult1, SQLSRV_FETCH_ASSOC) ) {
        //while( $row2 = sqlsrv_fetch_array( $msresult3, SQLSRV_FETCH_ASSOC) ) {

        //echo $row['name']."---- ".$row['dbid']."<br />";
        $dbname=$row['name'];
        $dbid=$row['dbid'];        
        echo "BASE DE DATOS: $dbname"."<br>";

//	if (! mssql_select_db($dbname, $msdb)) {

        $connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "$dbname","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
        $conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

        if (! $conn2) {
		echo "[-] No puedo seleccionar DB $dbname\n";
		continue;
	    }else{
            echo "Conectado a $dbname<br>";
        }

		//$msresult2 = mssql_query ("IF OBJECT_ID('legajo') IS NOT NULL AND OBJECT_ID('legajo_su') IS NOT NULL BEGIN; SELECT 1; END; ELSE BEGIN SELECT 0; END;");

        $consulta_object="IF OBJECT_ID('legajo') IS NOT NULL AND OBJECT_ID('legajo_su') IS NOT NULL BEGIN; SELECT 1; END; ELSE BEGIN SELECT 0; END;";
        $msresult2= sqlsrv_query( $conn2, $consulta_object);
        list ($isorg) = sqlsrv_fetch_array( $msresult2, SQLSRV_FETCH_NUMERIC);
        echo "$isorg<br>";

        if ($isorg == 1) {
            
            if ($rapido == 0) {
    
                // Empresas
                echo "Empresas<p>";
                $consulta_object="SELECT nombre_comercial, calle, nro_domic, localidad, codigo_postal, cuit, CONVERT(VARCHAR(10), fecha_inicio_actividad,20) FROM empresa";
                $msresult_emp=sqlsrv_query( $conn2, $consulta_object);
                while( $row3 = sqlsrv_fetch_array( $msresult_emp, SQLSRV_FETCH_ASSOC) ) {
                    //while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            
            
                        $nombre_comercial=$row3['nombre_comercial'];
                        $calle=$row3['calle'];
                        $nro_domic=$row3['nro_domic'];
                        $localidad=$row3['localidad'];
                        $codigo_postal=$row3['codigo_postal'];
                        $cuit=$row3['cuit'];
                        $fecha_inicio_actividad=$row3['fecha_inicio_actividad'];
                        echo "[+] Insertando empresa $dbname<br>";

                 } // while empresa

   
    
    // Departamentos
                 $consulta_departamento="SELECT d.id_departamento, d.cod_departamento, d.desc_departamento FROM departamento d";
                 $msresult4=sqlsrv_query( $conn2, $consulta_departamento);

                 while( $row4 = sqlsrv_fetch_array( $msresult4, SQLSRV_FETCH_ASSOC) ) {
                    //while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            
            
                        $id=$row4['id_departamento'];
                        $cod=$row4['cod_departamento'];
                        $desc=$row4['desc_departamento'];
                    echo "\t[+] Insertando departamento $cod\n";
                    echo "$cod', '$dbname', $id, '$desc'";

                   $query_dptos = "INSERT INTO departamentos_updated (cod_departamento, cod_empresa, id_departamento, desc_departamento) VALUES('$cod', '$dbname', $id, '$desc')";
                   $resultado = mysqli_query($enlace , $query_dptos);            



                 } // while departamento


                 			// Bancos
                 $consulta_bancos="SELECT b.id_banco, b.cod_banco, b.desc_banco FROM banco b";
                 $msresult5=sqlsrv_query( $conn2, $consulta_bancos);

                 //$msresult3 = mssql_query ("SELECT b.id_banco, b.cod_banco, b.desc_banco FROM banco b");
                 while( $row5 = sqlsrv_fetch_array( $msresult5, SQLSRV_FETCH_ASSOC) ) {
                    //while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            
            
                        $id=$row5['id_banco'];
                        $cod=$row5['cod_banco'];
                        $desc=$row5['desc_banco'];

                        $query_dptos = "INSERT INTO bancos_updated (id_banco, cod_banco, cod_empresa, desc_banco) VALUES($id, '$cod', '$dbname', '$desc')";
                        $resultado = mysqli_query($enlace , $query_dptos);
                        echo "\t[+] Insertando banco $desc\n";
                 } // while


                 
                            ////////////////////////// legajos


                            $consulta_legajos="SELECT l.nro_legajo, l.id_legajo, l.apellido, l.nombre, l.nro_documento, ls.habilitado, 
                            td.desc_documento, CONVERT(VARCHAR(10),ie.fecha_ingreso,20), CONVERT(VARCHAR(10),l.fecha_nacimiento,20), l.cuil, 
                            ls.sueldo, CONVERT(VARCHAR(10),ie.fecha_egreso,20), me.desc_motivo_egreso, c.cod_categoria, c.desc_categoria, ls.id_banco,
                            ls.id_obra_social, ls.forma_pago, lr.id_departamento, c.id_convenio, l.sexo FROM legajo l, legajo_su ls, legajo_resu lr, 
                            tipo_documento td, categoria c, ingreso_egreso ie LEFT JOIN motivo_egreso me ON ie.id_motivo_egreso = me.id_motivo_egreso
                            WHERE l.id_legajo = ls.id_legajo AND td.id_tipo_documento = l.id_tipo_documento AND ie.id_legajo = l.id_legajo AND ls.id_categoria = c.id_categoria 
                            AND lr.id_legajo = l.id_legajo";
                            $msresult6= sqlsrv_query( $conn2, $consulta_legajos);        
                            //$msresult3 = mssql_query ("SELECT l.nro_legajo, l.id_legajo, l.apellido, l.nombre, l.nro_documento, ls.habilitado, td.desc_documento, CONVERT(VARCHAR(10),ie.fecha_ingreso,20), CONVERT(VARCHAR(10),l.fecha_nacimiento,20), l.cuil, ls.sueldo, CONVERT(VARCHAR(10),ie.fecha_egreso,20), me.desc_motivo_egreso, c.cod_categoria, c.desc_categoria, ls.id_banco, ls.id_obra_social, ls.forma_pago, lr.id_departamento, c.id_convenio, l.sexo FROM legajo l, legajo_su ls, legajo_resu lr, tipo_documento td, categoria c, ingreso_egreso ie LEFT JOIN motivo_egreso me ON ie.id_motivo_egreso = me.id_motivo_egreso WHERE l.id_legajo = ls.id_legajo AND td.id_tipo_documento = l.id_tipo_documento AND ie.id_legajo = l.id_legajo AND ls.id_categoria = c.id_categoria AND lr.id_legajo = l.id_legajo");

                            // necesito que este departamentos - este puede ser el que rompa todo

                            while( $row6 = sqlsrv_fetch_array( $msresult6, SQLSRV_FETCH_ASSOC) ) {
                            //while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {


                                $nro_legajo=$row6['nro_legajo'];
                                $id_legajo=$row6['id_legajo'];
                                $apellido=$row6['apellido'];
                                $nombre=$row6['nombre'];
                                $nro_documento=$row6['nro_documento'];
                                $habilitado=$row6['habilitado'];
                                $desc_documento=$row6['desc_documento'];
                                $fecha_ingreso=$row6['fecha_ingreso'];
                                $fecha_nacimiento=$row6['fecha_nacimiento'];
                                $cuil=$row6['cuil'];
                                $sueldo=$row6['sueldo'];
                                $fecha_egreso=$row6['fecha_egreso'];
                                $desc_motivo_egreso=$row6['desc_motivo_egreso'];
                                $cod_categoria=$row6['cod_categoria'];
                                $desc_categoria=$row6['desc_categoria'];
                                $id_banco=$row6['id_banco'];            
                                $id_obra_social=$row6['id_obra_social'];            
                                $forma_pago=$row6['forma_pago'];            
                                $id_departamento=$row6['id_departamento'];            
                                $id_convenio=$row6['id_convenio'];            
                                $sexo=$row6['sexo'];
                                                    
                            //echo $row[0].", ".$row[1].", ".$row[2]."<br />";
                                //echo "\t[+] Insertando legajo $nro_legajo ($apellido, $nombre)\n";

                                $mi_idx=$dbname.':'.$nro_legajo;
                                //echo "Mi idx: $mi_idx<br>";

                                echo "INSERTANDO LEGAJO: $dbname:$nro_legajo, $nro_legajo, $id_legajo, $id_departamento, '$cod_departamento', '$dbname', '$apellido', 
                                '$nombre', '$desc_documento', '$nro_documento', '$fecha_nacimiento', '$fecha_ingreso', '$fecha_egreso', '$desc_motivo_egreso', 
                                '$cod_categoria', '$desc_categoria', '$habilitado', $sueldo, '$cuil', '$forma_pago', $id_banco, $id_obra_social, $id_convenio, '$sexo'<br>";

                                
//                                $query_ins = "INSERT INTO legajosbk (`legajo_idx`, `nro_legajo`, `id_legajo`, `id_departamento`, `cod_departamento`, `cod_empresa`, `apellido`,
//                                `nombre`, `tipo_documento`, `nro_documento`, `fecha_nacimiento`, `ingreso`, 
//                                `egreso`, `motivo_egreso`, `cod_categoria`, `desc_categoria`, `habilitado`, `sueldo`, `cuil`, `forma_pago`, `id_banco`, `id_obra_social`, `id_convenio`, `sexo`) 
//                                VALUES ('$dbname:$nro_legajo','$nro_legajo','$id_legajo','$id_departamento','$cod_departamento','$dbname','$apellido','$nombre','$desc_documento','$nro_documento','$fecha_nacimiento',
//                                '$fecha_ingreso','$fecha_egreso','$desc_motivo_egreso','$cod_categoria','$desc_categoria','$habilitado','$sueldo','$cuil','$forma_pago','$id_banco','$id_obra_social','$id_convenio','$sexo')";
//                                $resultado = mysqli_query($enlace , $query_ins);            
                                

//                                if (mysql_affected_rows() != 1) {
//                                    echo "\t[-] Error Insertando legajo VALUES('$dbname:$nro_legajo', $nro_legajo, $id_legajo, $id_departamento, '$cod_departamento', '$dbname', '$apellido', '$nombre', '$desc_documento', '$nro_documento', '$fecha_nacimiento', '$fecha_ingreso', '$fecha_egreso', '$desc_motivo_egreso', '$cod_categoria', '$desc_categoria', '$habilitado', $sueldo, '$cuil', '$forma_pago', $id_banco, $id_obra_social, $id_convenio, '$sexo')\n";
//                                    echo mysql_error() . PHP_EOL;
                                //}
                    

                                } // while recorrido de legajos



                            ////////////////////////////// fin legajos


                         if ($rapido == 0) {
                                // Novedades
                                $consulta_novedad="SELECT id_novedad, cod_novedad, desc_novedad, unidad_cantidad FROM novedad";
                                $msresult7= sqlsrv_query( $conn2, $consulta_novedad);        
                                while( $row7 = sqlsrv_fetch_array( $msresult7, SQLSRV_FETCH_ASSOC) ) {
                                    $cod_novedad=$row7['id_novedad'];
                                    $id_novedad=$row7['cod_novedad'];
                                    $desc_novedad=$row7['desc_novedad'];
                                    $unidad_cantidad=$row7['unidad_cantidad'];

                                    echo "\t[+] Insertando novedad $desc_novedad\n";
                                    $query_ins_novedad="INSERT INTO tipos_novedades_updated (tipo_novedad_idx, cod_novedad, id_novedad, cod_empresa, desc_novedad, unidad_cantidad) VALUES('$dbname:$cod_novedad', '$cod_novedad', '$id_novedad', 
                                    '$dbname', '$desc_novedad', '$unidad_cantidad')";
                                    $resultado = mysqli_query($enlace , $query_ins_novedad);
                                } // while novedad


                                // Convenios
                                    $consulta_convenio="SELECT id_convenio, cod_convenio, desc_convenio FROM convenio";
                                    $msresult8= sqlsrv_query( $conn2, $consulta_convenio);
                                    while( $row8 = sqlsrv_fetch_array( $msresult8, SQLSRV_FETCH_ASSOC) ) {
                                        $id_convenio=$row8['id_convenio'];
                                        $cod_convenio=$row8['cod_convenio'];                                    
                                        $desc_convenio=$row8['desc_convenio'];

                                        echo "\t[+] Insertando convenio $desc_convenio\n";                                        
                                        $query_ins_convenio="INSERT INTO convenios_updated(cod_empresa, cod_convenio, id_convenio, desc_convenio) VALUES ('$dbname', '$cod_convenio', $id_convenio, '$desc_convenio')";
                                        $resultado = mysqli_query($enlace , $query_ins_convenio);

                                    }
                        } else {
                            echo "[-] DB $dbname no tiene formato de empresa\n";
                        }




                } // rapido igual a 0
    
            } // is org igual a 1

        }// while con bases de datos







        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// todo oka de aca para abajo

        /*
        $consulta_legajos="SELECT l.nro_legajo, l.id_legajo, l.apellido, l.nombre, l.nro_documento, ls.habilitado, 
        td.desc_documento, CONVERT(VARCHAR(10),ie.fecha_ingreso,20), CONVERT(VARCHAR(10),l.fecha_nacimiento,20), l.cuil, 
        ls.sueldo, CONVERT(VARCHAR(10),ie.fecha_egreso,20), me.desc_motivo_egreso, c.cod_categoria, c.desc_categoria, ls.id_banco,
         ls.id_obra_social, ls.forma_pago, lr.id_departamento, c.id_convenio, l.sexo FROM legajo l, legajo_su ls, legajo_resu lr, 
         tipo_documento td, categoria c, ingreso_egreso ie LEFT JOIN motivo_egreso me ON ie.id_motivo_egreso = me.id_motivo_egreso
          WHERE l.id_legajo = ls.id_legajo AND td.id_tipo_documento = l.id_tipo_documento AND ie.id_legajo = l.id_legajo AND ls.id_categoria = c.id_categoria 
          AND lr.id_legajo = l.id_legajo";
        $msresult3= sqlsrv_query( $conn2, $consulta_legajos);        
		//$msresult3 = mssql_query ("SELECT l.nro_legajo, l.id_legajo, l.apellido, l.nombre, l.nro_documento, ls.habilitado, td.desc_documento, CONVERT(VARCHAR(10),ie.fecha_ingreso,20), CONVERT(VARCHAR(10),l.fecha_nacimiento,20), l.cuil, ls.sueldo, CONVERT(VARCHAR(10),ie.fecha_egreso,20), me.desc_motivo_egreso, c.cod_categoria, c.desc_categoria, ls.id_banco, ls.id_obra_social, ls.forma_pago, lr.id_departamento, c.id_convenio, l.sexo FROM legajo l, legajo_su ls, legajo_resu lr, tipo_documento td, categoria c, ingreso_egreso ie LEFT JOIN motivo_egreso me ON ie.id_motivo_egreso = me.id_motivo_egreso WHERE l.id_legajo = ls.id_legajo AND td.id_tipo_documento = l.id_tipo_documento AND ie.id_legajo = l.id_legajo AND ls.id_categoria = c.id_categoria AND lr.id_legajo = l.id_legajo");

		// necesito que este departamentos - este puede ser el que rompa todo
		
        while( $row2 = sqlsrv_fetch_array( $msresult3, SQLSRV_FETCH_ASSOC) ) {
        //while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {


            $nro_legajo=$row2['nro_legajo'];
            $id_legajo=$row2['id_legajo'];
            $apellido=$row2['apellido'];
            $nombre=$row2['nombre'];
            $nro_documento=$row2['nro_documento'];
            $habilitado=$row2['habilitado'];
            $desc_documento=$row2['desc_documento'];
            $fecha_ingreso=$row2['fecha_ingreso'];
            $fecha_nacimiento=$row2['fecha_nacimiento'];
            $cuil=$row2['cuil'];
            $sueldo=$row2['sueldo'];
            $fecha_egreso=$row2['fecha_egreso'];
            $desc_motivo_egreso=$row2['desc_motivo_egreso'];
            $cod_categoria=$row2['cod_categoria'];
            $desc_categoria=$row2['desc_categoria'];
            $id_banco=$row2['id_banco'];            
            $id_obra_social=$row2['id_obra_social'];            
            $forma_pago=$row2['forma_pago'];            
            $id_departamento=$row2['id_departamento'];            
            $id_convenio=$row2['id_convenio'];            
            $sexo=$row2['sexo'];
                                  
           //echo $row[0].", ".$row[1].", ".$row[2]."<br />";
            //echo "\t[+] Insertando legajo $nro_legajo ($apellido, $nombre)\n";

			$mi_idx=$dbname.':'.$nro_legajo;
            //echo "Mi idx: $mi_idx<br>";

            echo "$dbname:$nro_legajo', $nro_legajo, $id_legajo, $id_departamento, '$cod_departamento', '$dbname', '$apellido', 
            '$nombre', '$desc_documento', '$nro_documento', '$fecha_nacimiento', '$fecha_ingreso', '$fecha_egreso', '$desc_motivo_egreso', 
            '$cod_categoria', '$desc_categoria', '$habilitado', $sueldo, '$cuil', '$forma_pago', $id_banco, $id_obra_social, $id_convenio, '$sexo'<br>";

            
            $query_ins = "INSERT INTO legajosbk (`legajo_idx`, `nro_legajo`, `id_legajo`, `id_departamento`, `cod_departamento`, `cod_empresa`, `apellido`,
             `nombre`, `tipo_documento`, `nro_documento`, `fecha_nacimiento`, `ingreso`, 
            `egreso`, `motivo_egreso`, `cod_categoria`, `desc_categoria`, `habilitado`, `sueldo`, `cuil`, `forma_pago`, `id_banco`, `id_obra_social`, `id_convenio`, `sexo`) 
            VALUES ('$mi_idx','$nro_legajo','$id_legajo','$id_departamento','$cod_departamento','$dbname','$apellido','$nombre','$desc_documento','$nro_documento','$fecha_nacimiento',
            '$fecha_ingreso','$fecha_egreso','$desc_motivo_egreso','$cod_categoria','$desc_categoria','$habilitado','$sueldo','$cuil','$forma_pago','$id_banco','$id_obra_social','$id_convenio','$sexo')";
            $resultado = mysqli_query($enlace , $query_ins);            
            

            } // while recorrido de bases

			//echo "\t[-] Error Insertando legajo VALUES('$dbname:$nro_legajo', $nro_legajo, $id_legajo, $id_departamento, '$cod_departamento', '$dbname', '$apellido', '$nombre', '$desc_documento', '$nro_documento', '$fecha_nacimiento', '$fecha_ingreso', '$fecha_egreso', '$desc_motivo_egreso', '$cod_categoria', '$desc_categoria', '$habilitado', $sueldo, '$cuil', '$forma_pago', $id_banco, $id_obra_social, $id_convenio, '$sexo')\n";


	} // while con bases 

	sqlsrv_close($msresult2);
    sqlsrv_close($msresult1);
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//create_login();
//create_novedades();
//create_convenios();
//borrar_recibos_vencidos();
closemsdb();
closemydb();

?>