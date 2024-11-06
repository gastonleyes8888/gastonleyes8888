<?php
//version 1.0

//recorrer bases tango

$dbname = array("AGROPECUARIA_SALADAS_SA_2015", "CADIS_SA", "CATEDRAL_I_SCS_BELLA_VISTA_2015", "CATEDRAL_RESISTENCIA_SCS", "CATEDRAL_SCS_CORRIENTES_2015", "CATEDRAL_SCS_GOYA", "CENTRAL_PHARMA_SRL",
        "CORTLE_SA", "DEL_HOSPITAL_SCS", "DEL_PUENTE_SCS", "DEL_SHOPPING_SCS", "ECONOMIA_SCS", "FACOR_SRL_2017", "IDEAL_SCS", "INTEGRAL_SERVICIOS_SIGLO_XXI_SRL", "JEMAN_SRL", "JUFEC_SA", 
        "LOGIST_SRL", "LOGTRAN_SRL", "LUIS_PASTEUR_SCS_GOYA", "MENDOZA_SCS", "NOFAR_SRL", "NUEVA_MAIPU_SCS", "NUEVA_NORTE_SCS", "NUEVA_SAN_JUAN_SCS", "PELLEGRINI_SCS", "SALTA_SCS", "SANTA_CRUZ_SCS", 
        "TOTAL_PHARMA_SRL", "TRES_DE_ABRIL_SCS", "VEDIA_SCS");

        $cantidad=count($dbname);
        echo "Cantidad ------------------------------------- > $cantidad <p>";

// fin recorrer bases tango.

$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
$conn2=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

// conexion ombu
$enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");


if (! $conn2) {
    echo "[-] No puedo seleccionar DB\n";
}else{
    echo "Conectado a tanguito<br>";

    $file_legajos = fopen("/var/www/html/file_legajos.csv", "w");

}

                // Empresas
                echo "Empresas<p>";
                //CONVERT(VARCHAR(10),FECHA_NACIMIENTO,20)
                //CONVERT(VARCHAR(10),FECHA_INGRESO_TRAMO,20)
                //CONVERT(VARCHAR(10),FECHA_EGRESO_TRAMO,20)

                $consulta="SELECT CONCAT(COD_DEPARTAMENTO, ':', NRO_LEGAJO) AS legajo_idx, ID_LEGAJO, NRO_LEGAJO, APELLIDO, NOMBRE, ID_TIPO_DOCUMENTO, TIPO_DOCUMENTO, COD_CATEGORIA, DESC_MOTIVO_EGRESO, DESC_CATEGORIA, DESC_DOCUMENTO, 
                NRO_DOCUMENTO, CUIL, CONVERT(VARCHAR(10),FECHA_NACIMIENTO,20) as naci, SEXO, HABILITADO_SU, ID_DEPARTAMENTO, COD_DEPARTAMENTO, DESC_DEPARTAMENTO, ID_CONVENIO, COD_CONVENIO, ID_OBRA_SOCIAL, SUELDO, FORMA_PAGO,
                ID_BANCO, COD_BANCO, CONVERT(VARCHAR(10),FECHA_INGRESO_TRAMO,20) as ingreso, CONVERT(VARCHAR(10),FECHA_EGRESO_TRAMO,20) as egreso, ID_MOTIVO_EGRESO, COD_MOTIVO_EGRESO FROM JEMAN_SRL.dbo.AXV_LEGAJO";

//where NRO_DOCUMENTO = 34655043";
                $msresult_emp=sqlsrv_query( $conn2, $consulta);
                while( $row3 = sqlsrv_fetch_array( $msresult_emp, SQLSRV_FETCH_ASSOC) ) {
           
                        $legajo_idx=$row3['legajo_idx'];
                        $ID_LEGAJO=$row3['ID_LEGAJO'];
                        $NRO_LEGAJO=$row3['NRO_LEGAJO'];
                        $APELLIDO=$row3['APELLIDO'];
                        $NOMBRE=$row3['NOMBRE'];                        
                        $ID_TIPO_DOCUMENTO=$row3['ID_TIPO_DOCUMENTO'];
                        $TIPO_DOCUMENTO=$row3['TIPO_DOCUMENTO'];
                        $DESC_DOCUMENTO=$row3['DESC_DOCUMENTO'];
                        
                        $NRO_DOCUMENTO=$row3['NRO_DOCUMENTO'];
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

                        echo "[+] Estos son: $legajo_idx- $ID_LEGAJO- $NRO_LEGAJO- $APELLIDO- $COD_DEPARTAMENTO- $SUELDO -<br>";

                        //$query_ins = "INSERT IGNORE INTO legajos_bk (`legajo_idx`, `nro_legajo`, `id_legajo`, `id_departamento`, `cod_departamento`, `cod_empresa`, `apellido`,
                        //`nombre`, `tipo_documento`, `nro_documento`, `fecha_nacimiento`, `ingreso`, 
                        //`egreso`, `motivo_egreso`, `cod_categoria`, `desc_categoria`, `habilitado`, `sueldo`, `cuil`, `forma_pago`, `id_banco`, `id_obra_social`, `id_convenio`, `sexo`) 
                        //VALUES ('$legajo_idx','$NRO_LEGAJO','$ID_LEGAJO','$ID_DEPARTAMENTO','$COD_DEPARTAMENTO','JEMAN_SRL','$APELLIDO','$NOMBRE','desc_documento','$NRO_DOCUMENTO','$FECHA_NACIMIENTO',
                        //'$FECHA_INGRESO_TRAMO','$FECHA_EGRESO_TRAMO','desc_motivo_egreso','cod_categoria','desc_categoria','$HABILITADO_SU','$SUELDO','$CUIL','$FORMA_PAGO','$ID_BANCO','$ID_OBRA_SOCIAL','$ID_CONVENIO','$SEXO')";
                        //$resultado = mysqli_query($enlace , $query_ins);    
                        
                        @$imprimir_legajos = $legajo_idx.';'.$NRO_LEGAJO.';'.$ID_LEGAJO.';'.$ID_DEPARTAMENTO.';'.$COD_DEPARTAMENTO.';'.'JEMAN_SRL'.';'.$APELLIDO.';'.$NOMBRE.';'.$DESC_DOCUMENTO.';'.$NRO_DOCUMENTO.';'.$FECHA_NACIMIENTO.';'.
                        $FECHA_INGRESO_TRAMO.';'.$FECHA_EGRESO_TRAMO.';'.$DESC_MOTIVO_EGRESO.';'.$COD_CATEGORIA.';'.$DESC_CATEGORIA.';'.$HABILITADO_SU.';'.$SUELDO.';'.$CUIL.';'.$FORMA_PAGO.';'.$ID_BANCO.';'.$ID_OBRA_SOCIAL.';'.$ID_CONVENIO.';'.$SEXO;
                        fwrite($file_legajos, $imprimir_legajos . PHP_EOL);                        

                 } // while empresa
                 fclose($file_legajos);




?>