<?php


//recorrer bases tango


$dbname = array("AGROPECUARIA_SALADAS_SA_2015.dbo.novedad", "CADIS_SA.dbo.novedad", "CATEDRAL_I_SCS_BELLA_VISTA_2015.dbo.novedad", "CATEDRAL_RESISTENCIA_SCS.dbo.novedad", "CATEDRAL_SCS_CORRIENTES_2015.dbo.novedad", "CATEDRAL_SCS_GOYA.dbo.novedad", "CENTRAL_PHARMA_SRL.dbo.novedad",
        "CORTLE_SA.dbo.novedad", "DEL_HOSPITAL_SCS.dbo.novedad", "DEL_PUENTE_SCS.dbo.novedad", "DEL_SHOPPING_SCS.dbo.novedad", "ECONOMIA_SCS.dbo.novedad", "FACOR_SRL_2017.dbo.novedad", "IDEAL_SCS.dbo.novedad", "INTEGRAL_SERVICIOS_SIGLO_XXI_SRL.dbo.novedad", "JEMAN_SRL.dbo.novedad", "JUFEC_SA.dbo.novedad", 
        "LOGIST_SRL.dbo.novedad", "LOGTRAN_SRL.dbo.novedad", "LUIS_PASTEUR_SCS_GOYA.dbo.novedad", "MENDOZA_SCS.dbo.novedad", "NOFAR_SRL.dbo.novedad", "NUEVA_MAIPU_SCS.dbo.novedad", "NUEVA_NORTE_SCS.dbo.novedad", "NUEVA_SAN_JUAN_SCS.dbo.novedad", "PELLEGRINI_SCS.dbo.novedad", "SALTA_SCS.dbo.novedad", "SANTA_CRUZ_SCS.dbo.novedad", 
        "TOTAL_PHARMA_SRL.dbo.novedad", "TRES_DE_ABRIL_SCS.dbo.novedad", "VEDIA_SCS.dbo.novedad");

        $cantidad=count($dbname);

// fin recorrer bases tango.

$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
$conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

// conexion ombu
$enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");
$consultax = "TRUNCATE TABLE tipos_novedades_updated";

$resultadox = mysqli_query($enlace, $consultax);
echo "Tipos novedades depurada<br>";

if (! $conn2) {
    echo "[-] No puedo seleccionar DB\n";
}else{
    echo "Conectado a tanguito<br>";
    $file_novedades_tipos = fopen("/var/www/html/tipos_novedades_updated.csv", "w");
}

                echo "tipo Novedades<p>";

              for ($x = 0; $x < $cantidad; $x++) {

                $basecita=$dbname[$x];          // tablita con los datos del legajo novedad
                $novedadcita=explode('.dbo.novedad',$basecita);

                $consulta ="SELECT ID_NOVEDAD, COD_NOVEDAD, DESC_NOVEDAD, UNIDAD_CANTIDAD FROM $basecita";

                $msresult_emp=sqlsrv_query( $conn2, $consulta);
                $row_count = sqlsrv_num_rows( $msresult_emp );
   
                if ($row_count === false)
                echo "Error in retrieveing row count.";
                else
                echo "$dbname -- CANTIDAD REGS: $row_count<p>";

                
                while( $row3 = sqlsrv_fetch_array( $msresult_emp, SQLSRV_FETCH_ASSOC) ) {
           
                        $ID_NOVEDAD=$row3['ID_NOVEDAD'];
                        $COD_NOVEDAD=$row3['COD_NOVEDAD'];

                        $DESC_NOVEDAD=$row3['DESC_NOVEDAD'];
                        $UNIDAD_CANTIDAD=$row3['UNIDAD_CANTIDAD'];                        


                        echo "[+] Estos son: ID_NOVEDAD: $ID_NOVEDAD- BASE: $basecita- COD_NOVEDAD: $COD_NOVEDAD- DESC_NOVEDAD: $DESC_NOVEDAD- UNIDAD: $UNIDAD_CANTIDAD-<br>";

                        $codigo_novedad=trim($novedadcita[0].':'.$COD_NOVEDAD);

                        @$imprimir_legajos = $codigo_novedad.';'.$COD_NOVEDAD.';'.$ID_NOVEDAD.';'.$novedadcita[0].';'.$DESC_NOVEDAD.';'.$UNIDAD_CANTIDAD;

                        fwrite($file_novedades_tipos, $imprimir_legajos . PHP_EOL);
                        
                        $query_ins = "INSERT INTO tipos_novedades_updated (`tipo_novedad_idx`, `cod_novedad`, `id_novedad`, `cod_empresa`, `desc_novedad`, `unidad_cantidad`) 
                        VALUES ('$codigo_novedad','$COD_NOVEDAD','$ID_NOVEDAD','$novedadcita[0]','$DESC_NOVEDAD','$UNIDAD_CANTIDAD')";

                        $resultado = mysqli_query($enlace , $query_ins);
                        
                    } // while empresa


                }// for

                fclose($file_novedades_tipos);
?>

