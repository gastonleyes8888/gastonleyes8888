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


    $consultax = "TRUNCATE TABLE legajos_bk";
	$resultadox = mysqli_query($enlace, $consultax);
    echo "legajos_bk depurada<br>";

    $consultax = "TRUNCATE TABLE tipos_novedades_updated";
	$resultadox = mysqli_query($enlace, $consultax);				

	$consultax = "TRUNCATE TABLE convenios_updated";
	$resultadox = mysqli_query($enlace, $consultax);		
    
    $consultax = "TRUNCATE TABLE departamentos_updated";
	$resultadox = mysqli_query($enlace, $consultax);

    $consultax = "TRUNCATE TABLE empresas_updated";
	$resultadox = mysqli_query($enlace, $consultax);    

    $consultax = "TRUNCATE TABLE bancos_updated";
	$resultadox = mysqli_query($enlace, $consultax);


    /*



	$consultax = "TRUNCATE TABLE tipos_novedades_updated";
	$resultadox = mysqli_query($enlace, $consultax);				

	$consultax = "TRUNCATE TABLE convenios_updated";
	$resultadox = mysqli_query($enlace, $consultax);		
    
    $consultax = "TRUNCATE TABLE departamentos_updated";
	$resultadox = mysqli_query($enlace, $consultax);

    $consultax = "TRUNCATE TABLE empresas_updated";
	$resultadox = mysqli_query($enlace, $consultax);

*/
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


/*
	$consultax = "CREATE TABLE empresas_updated AS (SELECT * FROM empresas)";
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
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//$truncar_legajos_updated = mysql_query("TRUNCATE TABLE legajos_updated");
//echo "Tabla legajos update truncada";


$file_legajos = fopen("/var/www/html/file_legajos.csv", "w");

//"AXV_LEGAJO",

        $dbname = array("AGROPECUARIA_SALADAS_SA_2015", "CADIS_SA", "CATEDRAL_I_SCS_BELLA_VISTA_2015", "CATEDRAL_RESISTENCIA_SCS", "CATEDRAL_SCS_CORRIENTES_2015", "CATEDRAL_SCS_GOYA", "CENTRAL_PHARMA_SRL",
        "CORTLE_SA", "DEL_HOSPITAL_SCS", "DEL_PUENTE_SCS", "DEL_SHOPPING_SCS", "ECONOMIA_SCS", "FACOR_SRL_2017", "IDEAL_SCS", "INTEGRAL_SERVICIOS_SIGLO_XXI_SRL", "JEMAN_SRL", "JUFEC_SA", 
        "LOGIST_SRL", "LOGTRAN_SRL", "LUIS_PASTEUR_SCS_GOYA", "MENDOZA_SCS", "NOFAR_SRL", "NUEVA_MAIPU_SCS", "NUEVA_NORTE_SCS", "NUEVA_SAN_JUAN_SCS", "PELLEGRINI_SCS", "SALTA_SCS", "SANTA_CRUZ_SCS", 
        "TOTAL_PHARMA_SRL", "TRES_DE_ABRIL_SCS", "VEDIA_SCS");

        $cantidad=count($dbname);
        echo "$cantidad de bases<p>";


        //echo "BASE DE DATOS: $dbname"."<br>";

      for ($x = 0; $x < $cantidad; $x++) {

        $connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "$dbname[$x]","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
        $conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

        if (! $conn2) {
		echo "[-] No puedo seleccionar DB $dbname[$x]\n";
		continue;
	    }else{
            echo "Conectado a $dbname[$x]<br>";
        }

                // Empresas
                echo "Empresas<p>";
                $consulta_object="SELECT nombre_comercial, calle, nro_domic, localidad, codigo_postal, cuit, CONVERT(VARCHAR(10), fecha_inicio_actividad,20) FROM empresa";
                $msresult_emp=sqlsrv_query( $conn2, $consulta_object);
                while( $row3 = sqlsrv_fetch_array( $msresult_emp, SQLSRV_FETCH_ASSOC) ) {
           
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
                        $id=$row4['id_departamento'];
                        $cod=$row4['cod_departamento'];
                        $desc=$row4['desc_departamento'];
                    echo "\t[+] Insertando departamento $cod\n";
                    //echo "$cod', '$dbname', $id, '$desc'";

                   $query_dptos = "INSERT INTO departamentos_updated (cod_departamento, cod_empresa, id_departamento, desc_departamento) VALUES('$cod', '$dbname[$x]', $id, '$desc')";
                   $resultado = mysqli_query($enlace , $query_dptos);

                 } // while departamento


                // Bancos
                
                 $consulta_bancos="SELECT b.id_banco, b.cod_banco, b.desc_banco FROM banco b";
                 $msresult5=sqlsrv_query( $conn2, $consulta_bancos);

                 while( $row5 = sqlsrv_fetch_array( $msresult5, SQLSRV_FETCH_ASSOC) ) {

                        $id=$row5['id_banco'];
                        $cod=$row5['cod_banco'];
                        $desc=$row5['desc_banco'];

                        $query_dptos = "INSERT INTO bancos_updated (id_banco, cod_banco, cod_empresa, desc_banco) VALUES($id, '$cod', '$dbname[$x]', '$desc')";
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

                            while( $row6 = sqlsrv_fetch_array( $msresult6, SQLSRV_FETCH_ASSOC) ) {

                                $nro_legajo=$row6['nro_legajo'];
                                $id_legajo=$row6['id_legajo'];
                                $apellido=$row6['apellido'];
                                $nombre=$row6['nombre'];
                                $nro_documento=$row6['nro_documento'];
                                $habilitado=$row6['habilitado'];
                                $desc_documento=$row6['desc_documento'];
                                @$fecha_ingreso=$row6['fecha_ingreso'];
                                @$fecha_nacimiento=$row6['fecha_nacimiento'];
                                $cuil=$row6['cuil'];
                                $sueldo=$row6['sueldo'];
                                @$fecha_egreso=$row6['fecha_egreso'];
                                $desc_motivo_egreso=$row6['desc_motivo_egreso'];
                                $cod_categoria=$row6['cod_categoria'];
                                $desc_categoria=$row6['desc_categoria'];
                                $id_banco=$row6['id_banco'];            
                                $id_obra_social=$row6['id_obra_social'];            
                                $forma_pago=$row6['forma_pago'];            
                                $id_departamento=$row6['id_departamento'];            
                                $id_convenio=$row6['id_convenio'];            
                                $sexo=$row6['sexo'];


                                $mi_idx=$dbname[$x].':'.$nro_legajo;
                                //echo "Mi idx: $mi_idx<br>";


                                $consultita_dptos= "SELECT cod_departamento FROM departamentos WHERE id_departamento like '$id_departamento' and cod_empresa like '$dbname[$x]'";
                                $dep_result = mysqli_query($enlace, $consultita_dptos);

                                if ($dep_result) list ($cod_departamento) = mysqli_fetch_row($dep_result);
                                else $cod_departamento = 'NONE';
                                if (!is_int($id_banco)) $id_banco = 'NULL';
                                if (!is_int($id_obra_social)) $id_obra_social = 'NULL';
                                if (!is_int($id_convenio)) $id_convenio = 'NULL';
                                if (!is_int($id_departamento)) $id_departamento = -1;

                                $apellido = mysqli_real_escape_string($enlace, $apellido);
                                $nombre = mysqli_real_escape_string($enlace, $nombre);                                

                                echo "INSERTANDO LEGAJO: $dbname[$x]:$nro_legajo, $nro_legajo, $id_legajo, $apellido, $nombre"."<br>";

                                $query_ins = "INSERT IGNORE INTO legajos_bk (`legajo_idx`, `nro_legajo`, `id_legajo`, `id_departamento`, `cod_departamento`, `cod_empresa`, `apellido`,
                                `nombre`, `tipo_documento`, `nro_documento`, `fecha_nacimiento`, `ingreso`, 
                                `egreso`, `motivo_egreso`, `cod_categoria`, `desc_categoria`, `habilitado`, `sueldo`, `cuil`, `forma_pago`, `id_banco`, `id_obra_social`, `id_convenio`, `sexo`) 
                                VALUES ('$mi_idx','$nro_legajo','$id_legajo','$id_departamento','$cod','$dbname[$x]','$apellido','$nombre','$desc_documento','$nro_documento','$fecha_nacimiento',
                                '$fecha_ingreso','$fecha_egreso','$desc_motivo_egreso','$cod_categoria','$desc_categoria','$habilitado','$sueldo','$cuil','$forma_pago','$id_banco','$id_obra_social','$id_convenio','$sexo')";
                                $resultado = mysqli_query($enlace , $query_ins);
                                
                                /////

$imprimir_legajos = $mi_idx.';'.$nro_legajo.';'.$id_legajo.';'.$id_departamento.';'.$cod_departamento.';'.$dbname[$x].';'.$apellido.';'.$nombre.';'.$desc_documento.';'.$nro_documento.';'.$fecha_nacimiento.';'.$fecha_ingreso.';'.$fecha_egreso.';'.$desc_motivo_egreso.';'.$cod_categoria.';'.$desc_categoria.';'.$habilitado.';'.$sueldo.';'.$cuil.';'.$forma_pago.';'.$id_banco.';'.$id_obra_social.';'.$id_convenio.';'.$sexo;
fwrite($file_legajos, $imprimir_legajos . PHP_EOL);

                            } // while recorrido de legajos

        }// for

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