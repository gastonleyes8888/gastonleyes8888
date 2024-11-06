<?php


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// Connect to MySQL Database
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


	$consultax = "TRUNCATE TABLE tipos_novedades_updated";
	$resultadox = mysqli_query($enlace, $consultax);				

	$consultax = "TRUNCATE TABLE convenios_updated";
	$resultadox = mysqli_query($enlace, $consultax);		
    
    $consultax = "TRUNCATE TABLE departamentos_updated";
	$resultadox = mysqli_query($enlace, $consultax);

    $consultax = "TRUNCATE TABLE empresas_updated";
	$resultadox = mysqli_query($enlace, $consultax);    

/*

	$consultax = "TRUNCATE TABLE legajosbk2";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "TRUNCATE TABLE bancos_updated";
	$resultadox = mysqli_query($con, $consultax);

	$consultax = "CREATE TABLE departamentos_updated AS (SELECT * FROM departamentos)";
	$resultadox = mysqli_query($enlace, $consultax);    

	//$consultax = "CREATE TABLE legajos_updated AS (SELECT * FROM legajos)";
	//$resultadox = mysqli_query($con, $consultax);		
	//exit;			
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
//if (date("d") < 22) procesar_novedades(); // Para no meter las novedades web despues del 21
//else procesar_novedades_archivo();



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function procesar_tabla($table) // procesar tabla
{
	global $mydb; 
	echo "[+] Procesando tabla $table\n";
	$sufijo = date('w');
	$old_table = $table . "_" . $sufijo;

}


//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//$truncar_legajos_updated = mysql_query("TRUNCATE TABLE legajos_updated");
//echo "Tabla legajos update truncada";


//if ($rapido == 1) foreach ($tablas_rapido as $tabla) procesar_tabla($tabla);
//else foreach ($tablas as $tabla) procesar_tabla($tabla);


$file_legajos = fopen("/var/www/html/file_legajos.csv", "w");

$consulta_bases="SELECT name, dbid FROM master..sysdatabases";
$msresult1= sqlsrv_query( $conn, $consulta_bases);

//$msresult1 =  mssql_query ("SELECT name, dbid FROM master..sysdatabases");

    while ($row = sqlsrv_fetch_array( $msresult1, SQLSRV_FETCH_ASSOC) ) {
        $dbname=$row['name'];
        $dbid=$row['dbid'];        
        //echo "BASE DE DATOS: $dbname"."<br>";

        $connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "$dbname","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
        $conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

        if (! $conn2) {
		echo "[-] No puedo seleccionar DB $dbname\n";
		continue;
	    }else{
            echo "Conectado a $dbname<br>";
        }

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

                        $query_dptos = "INSERT INTO empresas_updated (`cod_empresa`, `desc_empresa`, `calle`, `numero`, `localidad`, `codigo_postal`, `cuit`, `inicio_actividades`) 
                        VALUES ('$dbname','$nombre_comercial','$calle','','$localidad','','','')";
                        $resultado = mysqli_query($enlace , $query_dptos);                        

                 } // while empresa

   
    
    // Departamentos
                 $consulta_departamento="SELECT d.id_departamento, d.cod_departamento, d.desc_departamento FROM departamento d";
                 $msresult4=sqlsrv_query( $conn2, $consulta_departamento);

                 while( $row4 = sqlsrv_fetch_array( $msresult4, SQLSRV_FETCH_ASSOC) ) {
                        $id=$row4['id_departamento'];
                        $cod=$row4['cod_departamento'];
                        $desc=$row4['desc_departamento'];
                    echo "\t[+] Insertando departamento $cod\n";
                    //echo "$cod', '$dbname', $id, '$desc'";

                   $query_dptos = "INSERT INTO departamentos_updated (cod_departamento, cod_empresa, id_departamento, desc_departamento) VALUES('$cod', '$dbname', $id, '$desc')";
                   $resultado = mysqli_query($enlace , $query_dptos);

                 } // while departamento


                // Bancos
                
                 $consulta_bancos="SELECT b.id_banco, b.cod_banco, b.desc_banco FROM banco b";
                 $msresult5=sqlsrv_query( $conn2, $consulta_bancos);

                 while( $row5 = sqlsrv_fetch_array( $msresult5, SQLSRV_FETCH_ASSOC) ) {

                        $id=$row5['id_banco'];
                        $cod=$row5['cod_banco'];
                        $desc=$row5['desc_banco'];

                        $query_dptos = "INSERT INTO bancos_updated (id_banco, cod_banco, cod_empresa, desc_banco) VALUES($id, '$cod', '$dbname', '$desc')";
                        $resultado = mysqli_query($enlace , $query_dptos);
                        echo "\t[+] Insertando banco $desc\n";
                 } // while


                         if ($rapido == 0) {
                                // Novedades
                                $consulta_novedad="SELECT id_novedad, cod_novedad, desc_novedad, unidad_cantidad FROM novedad";
                                $msresult7= sqlsrv_query( $conn2, $consulta_novedad);        
                                while( $row7 = sqlsrv_fetch_array( $msresult7, SQLSRV_FETCH_ASSOC) ) {
                                    $id_novedad=$row7['id_novedad'];
                                    $cod_novedad=$row7['cod_novedad'];
                                    $desc_novedad=$row7['desc_novedad'];
                                    $unidad_cantidad=$row7['unidad_cantidad'];

                                    echo "\t[+] Insertando novedad $desc_novedad\n";
                                    
                                    $query_ins_novedad="INSERT INTO tipos_novedades_updated (tipo_novedad_idx, cod_novedad, id_novedad, cod_empresa, 
                                    desc_novedad, unidad_cantidad)
                                     VALUES('$dbname:$cod_novedad', '$cod_novedad', '$id_novedad', '$dbname', '$desc_novedad', '$unidad_cantidad')";
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

        } // while con bases de datos


        fclose($file_legajos);


        sqlsrv_close($msresult1);
        sqlsrv_close($msresult2);
        sqlsrv_close($msresult3);
        sqlsrv_close($msresult4);
        sqlsrv_close($msresult5);        
        sqlsrv_close($msresult6);
        sqlsrv_close($msresult7);
        sqlsrv_close($msresult8);                        
        sqlsrv_close($msresult_emp);
        mysqli_close($enlace);




//create_login();
//create_novedades();
//create_convenios();
//borrar_recibos_vencidos();
//closemsdb();
//closemydb();

?>