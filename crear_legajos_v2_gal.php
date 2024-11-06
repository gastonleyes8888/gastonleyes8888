<?php


//recorrer bases tango


$dbname = array("AGROPECUARIA_SALADAS_SA_2015.dbo.AXV_LEGAJO", "CADIS_SA.dbo.AXV_LEGAJO", "CATEDRAL_I_SCS_BELLA_VISTA_2015.dbo.AXV_LEGAJO", "CATEDRAL_RESISTENCIA_SCS.dbo.AXV_LEGAJO", "CATEDRAL_SCS_CORRIENTES_2015.dbo.AXV_LEGAJO", "CATEDRAL_SCS_GOYA.dbo.AXV_LEGAJO", "CENTRAL_PHARMA_SRL.dbo.AXV_LEGAJO",
        "CORTLE_SA.dbo.AXV_LEGAJO", "DEL_HOSPITAL_SCS.dbo.AXV_LEGAJO", "DEL_PUENTE_SCS.dbo.AXV_LEGAJO", "DEL_SHOPPING_SCS.dbo.AXV_LEGAJO", "ECONOMIA_SCS.dbo.AXV_LEGAJO", "FACOR_SRL_2017.dbo.AXV_LEGAJO", "IDEAL_SCS.dbo.AXV_LEGAJO", "INTEGRAL_SERVICIOS_SIGLO_XXI_SRL.dbo.AXV_LEGAJO", "JEMAN_SRL.dbo.AXV_LEGAJO", "JUFEC_SA.dbo.AXV_LEGAJO", 
        "LOGIST_SRL.dbo.AXV_LEGAJO", "LOGTRAN_SRL.dbo.AXV_LEGAJO", "LUIS_PASTEUR_SCS_GOYA.dbo.AXV_LEGAJO", "MENDOZA_SCS.dbo.AXV_LEGAJO", "NOFAR_SRL.dbo.AXV_LEGAJO", "NUEVA_MAIPU_SCS.dbo.AXV_LEGAJO", "NUEVA_NORTE_SCS.dbo.AXV_LEGAJO", "NUEVA_SAN_JUAN_SCS.dbo.AXV_LEGAJO", "PELLEGRINI_SCS.dbo.AXV_LEGAJO", "SALTA_SCS.dbo.AXV_LEGAJO", "SANTA_CRUZ_SCS.dbo.AXV_LEGAJO", 
        "TOTAL_PHARMA_SRL.dbo.AXV_LEGAJO", "TRES_DE_ABRIL_SCS.dbo.AXV_LEGAJO", "VEDIA_SCS.dbo.AXV_LEGAJO");

//$dbname = array("JUFEC_SA.dbo.AXV_LEGAJO");
        $cantidad=count($dbname);
       // echo "Cantidad ------------------------------------- > $cantidad <p>";

// fin recorrer bases tango.

$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
$conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

// conexion ombu
//$enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");

$enlace = mysqli_connect("181.117.7.13", "galactus", "1qaz", "u551789018_portal");
$consultax = "TRUNCATE TABLE legajos_bk";

$resultadox = mysqli_query($enlace, $consultax);
echo "legajos_bk depurada<br>";

if (! $conn2) {
    echo "[-] No puedo seleccionar DB\n";
}else{
    echo "Conectado a tanguito<br>";
    $file_legajos = fopen("file_legajos.csv", "w");
}

                // Empresas
                echo "Empresas<p>";
                //CONVERT(VARCHAR(10),FECHA_NACIMIENTO,20)
                //CONVERT(VARCHAR(10),FECHA_INGRESO_TRAMO,20)
                //CONVERT(VARCHAR(10),FECHA_EGRESO_TRAMO,20)

              for ($x = 0; $x < $cantidad; $x++) {

                $basecita=$dbname[$x];          // tablita con los datos del legajo AXV_LEGAJO
                $empresita=explode('.dbo.AXV_LEGAJO',$basecita);

               //$consulta="SELECT CONCAT(COD_DEPARTAMENTO, ':', NRO_LEGAJO) AS legajo_idx, ID_LEGAJO, NRO_LEGAJO, APELLIDO, NOMBRE, ID_TIPO_DOCUMENTO, TIPO_DOCUMENTO, COD_CATEGORIA, DESC_MOTIVO_EGRESO, DESC_CATEGORIA, DESC_DOCUMENTO, 
               //NRO_DOCUMENTO, CUIL, CONVERT(VARCHAR(10),FECHA_NACIMIENTO,20) as naci, SEXO, HABILITADO_SU, ID_DEPARTAMENTO, COD_DEPARTAMENTO, DESC_DEPARTAMENTO, ID_CONVENIO, COD_CONVENIO, ID_OBRA_SOCIAL, SUELDO, FORMA_PAGO,
               //ID_BANCO, COD_BANCO, CONVERT(VARCHAR(10),FECHA_INGRESO_TRAMO,20) as ingreso, CONVERT(VARCHAR(10),FECHA_EGRESO_TRAMO,20) as egreso, ID_MOTIVO_EGRESO, COD_MOTIVO_EGRESO FROM $basecita";

                $consulta ="SELECT CONCAT(COD_DEPARTAMENTO, ':', NRO_LEGAJO) AS legajo_idx, ID_LEGAJO, NRO_LEGAJO, APELLIDO, NOMBRE, COD_DEPARTAMENTO, DESC_DEPARTAMENTO, ID_TIPO_DOCUMENTO, 
                    TIPO_DOCUMENTO, COD_CATEGORIA, DESC_MOTIVO_EGRESO, DESC_CATEGORIA, DESC_DOCUMENTO, 
                    NRO_DOCUMENTO, CUIL, CONVERT(VARCHAR(10),FECHA_NACIMIENTO,20) as naci, SEXO, HABILITADO_SU, ID_DEPARTAMENTO, ID_CONVENIO, 
                    COD_CONVENIO, ID_OBRA_SOCIAL, SUELDO, FORMA_PAGO,
                    ID_BANCO, COD_BANCO, CONVERT(VARCHAR(10),FECHA_INGRESO_TRAMO,20) as ingreso, CONVERT(VARCHAR(10),FECHA_EGRESO_TRAMO,20) as egreso, 
                    ID_MOTIVO_EGRESO, COD_MOTIVO_EGRESO FROM $basecita";

                //where NRO_DOCUMENTO = 34655043";
                

                $msresult_emp=sqlsrv_query( $conn2, $consulta);
                

                
                while( $row3 = sqlsrv_fetch_array( $msresult_emp, SQLSRV_FETCH_ASSOC) ) {
           
                        $legajo_idx=trim($row3['legajo_idx']);
                        $ID_LEGAJO=$row3['ID_LEGAJO'];
                        $NRO_LEGAJO=$row3['NRO_LEGAJO'];

                        $APELLIDO=$row3['APELLIDO'];
                        $NOMBRE=$row3['NOMBRE'];                        

                        $APELLIDO = mysqli_real_escape_string($enlace, $APELLIDO);
                        $NOMBRE = mysqli_real_escape_string($enlace, $NOMBRE);    

                        $ID_TIPO_DOCUMENTO=$row3['ID_TIPO_DOCUMENTO'];
                        $TIPO_DOCUMENTO=$row3['TIPO_DOCUMENTO'];
                        $DESC_DOCUMENTO=$row3['DESC_DOCUMENTO'];
                        
                        $NRO_DOCUMENTO=trim($row3['NRO_DOCUMENTO']);
                        $CUIL=$row3['CUIL'];
                        $FECHA_NACIMIENTO=$row3['naci'];
                        $SEXO=$row3['SEXO'];

                        $HABILITADO_SU=$row3['HABILITADO_SU'];
                        $ID_DEPARTAMENTO=$row3['ID_DEPARTAMENTO'];
                        $COD_DEPARTAMENTO=$row3['COD_DEPARTAMENTO'];
                        $DESC_DEPARTAMENTO=$row3['DESC_DEPARTAMENTO'];
                        $ID_CONVENIO=$row3['ID_CONVENIO'];
                        $COD_CONVENIO=$row3['COD_CONVENIO'];
                        $ID_OBRA_SOCIAL=$row3['ID_OBRA_SOCIAL'];
                        $SUELDO=$row3['SUELDO'];
                        $FORMA_PAGO=$row3['FORMA_PAGO'];
                        $ID_BANCO=$row3['ID_BANCO'];
                        $COD_BANCO=$row3['COD_BANCO'];
                        $FECHA_INGRESO_TRAMO=$row3['ingreso'];
                        $DESC_MOTIVO_EGRESO=$row3['DESC_MOTIVO_EGRESO'];                        
                        $FECHA_EGRESO_TRAMO=$row3['egreso'];
                        $ID_MOTIVO_EGRESO=$row3['ID_MOTIVO_EGRESO'];
                        $COD_MOTIVO_EGRESO=$row3['COD_MOTIVO_EGRESO'];

                        $COD_CATEGORIA=$row3['COD_CATEGORIA'];
                        $DESC_CATEGORIA=$row3['DESC_CATEGORIA'];
                        
                        //34655043 36316787 43275034 38877222

                        echo "[+] Estos son: $legajo_idx- $dbname[$x]- $NRO_LEGAJO- $APELLIDO- $COD_DEPARTAMENTO-<br>";

                        @$imprimir_legajos = $legajo_idx.';'.$NRO_LEGAJO.';'.$ID_LEGAJO.';'.$ID_DEPARTAMENTO.';'.$COD_DEPARTAMENTO.';'.$dbname[$x].';'.$APELLIDO.';'.$NOMBRE.';'.$DESC_DOCUMENTO.';'.$NRO_DOCUMENTO.';'.$FECHA_NACIMIENTO.';'.
                        $FECHA_INGRESO_TRAMO.';'.$FECHA_EGRESO_TRAMO.';'.$DESC_MOTIVO_EGRESO.';'.$COD_CATEGORIA.';'.$DESC_CATEGORIA.';'.$HABILITADO_SU.';'.$SUELDO.';'.$CUIL.';'.$FORMA_PAGO.';'.$ID_BANCO.';'.$ID_OBRA_SOCIAL.';'.$ID_CONVENIO.';'.$SEXO;
                        fwrite($file_legajos, $imprimir_legajos . PHP_EOL);
                        
                        /*
                        echo "[+] Estos son: $legajo_idx- $$dbname[$x]- $NRO_LEGAJO- $APELLIDO- $COD_DEPARTAMENTO- $SUELDO -<br>";
                        @$imprimir_legajos = $legajo_idx.';'.$NRO_LEGAJO.';'.$ID_LEGAJO.';'.$ID_DEPARTAMENTO.';'.$COD_DEPARTAMENTO.';'.$dbname[$x].';'.$APELLIDO.';'.$NOMBRE.';'.$DESC_DOCUMENTO.';'.$NRO_DOCUMENTO.';'.$FECHA_NACIMIENTO.';'.
                        $FECHA_INGRESO_TRAMO.';'.$FECHA_EGRESO_TRAMO.';'.$DESC_MOTIVO_EGRESO.';'.$COD_CATEGORIA.';'.$DESC_CATEGORIA.';'.$HABILITADO_SU.';'.$SUELDO.';'.$CUIL.';'.$FORMA_PAGO.';'.$ID_BANCO.';'.$ID_OBRA_SOCIAL.';'.$ID_CONVENIO.';'.$SEXO;
                        fwrite($file_legajos, $imprimir_legajos . PHP_EOL);                                                
                        */

//                        $query_ins = "INSERT INTO legajos_bk (`aid`, `nro_legajo`, `id_departamento`, `cod_departamento`, `cod_empresa`, `apellido`, `nombre`, `tipo_documento`, `nro_documento`, `habilitado`, `basecita`)
 //                       VALUES ('', '$ID_LEGAJO','$ID_DEPARTAMENTO','$COD_DEPARTAMENTO','$empresita[0]','$APELLIDO','$NOMBRE','$TIPO_DOCUMENTO','$NRO_DOCUMENTO','$HABILITADO_SU','$empresita[0]')";

$query_ins = "INSERT IGNORE INTO legajos_bk (`nro_legajo`, `id_departamento`, `cod_departamento`, `cod_empresa`, `apellido`, `nombre`, `tipo_documento`, `nro_documento`, `habilitado`, `basecita`)
VALUES ('$ID_LEGAJO','$ID_DEPARTAMENTO','$COD_DEPARTAMENTO','$empresita[0]','$APELLIDO','$NOMBRE','$TIPO_DOCUMENTO','$NRO_DOCUMENTO','$HABILITADO_SU','$empresita[0]')";

                        $resultado = mysqli_query($enlace , $query_ins);

                       
                    } // while empresa


                }// for
                echo "Fin!"

                //fclose($file_legajos);
?>

<?php

/*

SELECT DISTINCT cod_empresa FROM legajos_bk;

*/

?>