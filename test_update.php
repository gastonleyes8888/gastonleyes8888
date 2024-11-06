<?php
exit;

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//VEDIA_SCS	1185	40848667	Azcona, Jonathan Daniel	20-40848667-0	VEDIA SAENZ PEÑA	08/08/2024	CAD/AP AYU

// Departamentos
$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "VEDIA_SCS","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
$conn=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS


            ////////////////////////// de aca para abajo ////////////////////////    

			$consulta_bases="SELECT name, dbid FROM master..sysdatabases";
			$msresult1= sqlsrv_query( $conn, $consulta_bases);
//            $msresult1 =  mssql_query ("SELECT name, dbid FROM master..sysdatabases");

                //while (list ($dbname) = mssql_fetch_row ($msresult1)) {
                    while (list ($dbname) = $row = sqlsrv_fetch_array( $msresult1, SQLSRV_FETCH_ASSOC) ) {
                        echo $row['name']."---- ".$row['dbid']."<br />";


//                    if (! mssql_select_db($dbname, $msdb)) {
//                        echo "[-] No puedo seleccionar DB $dbname\n";
//                        continue;
//                    }
                }
                exit;

                
                    //$msresult2 = mssql_query ("IF OBJECT_ID('legajo') IS NOT NULL AND OBJECT_ID('legajo_su') IS NOT NULL BEGIN; SELECT 1; END; ELSE BEGIN SELECT 0; END;");
                    $msresult2 = mssql_query ("IF OBJECT_ID('legajo') IS NOT NULL AND OBJECT_ID('legajo_su') IS NOT NULL BEGIN; SELECT 1; END; ELSE BEGIN SELECT 0; END;");                    
                    //list ($isorg) = mssql_fetch_row ($msresult2);
                    list ($isorg) = mssql_fetch_row ($msresult2);                    
                
                    if ($isorg == 1) {
                        if ($rapido == 0) {
                
                            // Empresas
                            $msresult_emp = mssql_query("SELECT nombre_comercial, calle, nro_domic, localidad, codigo_postal, cuit, CONVERT(VARCHAR(10),
                                fecha_inicio_actividad,20) FROM empresa");
                            list ($desc_empresa, $calle, $numero, $localidad, $codigo_postal, $cuit, $inicio_actividad) = mssql_fetch_row($msresult_emp);
                            echo "[+] Insertando empresa $dbname\n";
                
                
                
                
                // Departamentos
                            $msresult3 = mssql_query ("SELECT d.id_departamento, d.cod_departamento, d.desc_departamento FROM departamento d");
                            while (list ($id, $cod, $desc) = mssql_fetch_row ($msresult3)) {
                                echo "\t[+] Insertando departamento $cod\n";
                
                            //$imprimir_departamentos = $cod.';'.$dbname.';'.$calle.';'.$numero.';'.$localidad.';'.$codigo_postal.';'.$cuit.';'.$inicio_actividad;
                            //fwrite($file_departamentos, $imprimir_departamentos . PHP_EOL);
                
                            //	mysql_query ("INSERT INTO departamentos (cod_departamento, cod_empresa, id_departamento, desc_departamento) VALUES('$cod', '$dbname', $id, '$desc')");
                            }
                
                            // Bancos
                                    $msresult3 = mssql_query ("SELECT b.id_banco, b.cod_banco, b.desc_banco FROM banco b");
                                    while (list ($id, $cod, $desc) = mssql_fetch_row ($msresult3)) {
                                            echo "\t[+] Insertando banco $desc\n";
                
                                            //$imprimir_bancos = $id.';'.$cod.';'.$dbname.';'.$desc;
                                            //fwrite($file_bancos, $imprimir_bancos . PHP_EOL);				                        	
                                            //mysql_query ("INSERT INTO bancos (id_banco, cod_banco, cod_empresa, desc_banco) VALUES($id, '$cod', '$dbname', '$desc')");
                                    }
                
                            // Obras sociales
                                    $msresult3 = mssql_query ("SELECT o.id_obra_social, o.cod_obra_social, o.desc_obra_social FROM obra_social o");
                                    while (list ($id, $cod, $desc) = mssql_fetch_row ($msresult3)) {
                                            echo "\t[+] Insertando obra social $desc\n";
                
                                            //mysql_query ("INSERT INTO obras_sociales (id_obra_social, cod_obra_social, cod_empresa, desc_obra_social) VALUES($id, '$cod', '$dbname', '$desc')");
                                       }
                        }
                        // Legajos
                
                        $msresult3 = mssql_query ("SELECT l.nro_legajo, l.id_legajo, l.apellido, l.nombre, l.nro_documento, ls.habilitado, td.desc_documento, CONVERT(VARCHAR(10),ie.fecha_ingreso,20), CONVERT(VARCHAR(10),l.fecha_nacimiento,20), l.cuil, ls.sueldo, CONVERT(VARCHAR(10),ie.fecha_egreso,20), me.desc_motivo_egreso, c.cod_categoria, c.desc_categoria, ls.id_banco, ls.id_obra_social, ls.forma_pago, lr.id_departamento, c.id_convenio, l.sexo FROM legajo l, legajo_su ls, legajo_resu lr, tipo_documento td, categoria c, ingreso_egreso ie LEFT JOIN motivo_egreso me ON ie.id_motivo_egreso = me.id_motivo_egreso WHERE l.id_legajo = ls.id_legajo AND td.id_tipo_documento = l.id_tipo_documento AND ie.id_legajo = l.id_legajo AND ls.id_categoria = c.id_categoria AND lr.id_legajo = l.id_legajo");
                
                        // necesito que este departamentos - este puede ser el que rompa todo
                        while (list ($nro_legajo, $id_legajo, $apellido, $nombre, $nro_documento, $habilitado, $desc_documento, $fecha_ingreso, $fecha_nacimiento, $cuil, $sueldo, $fecha_egreso, $desc_motivo_egreso, $cod_categoria, $desc_categoria, $id_banco, $id_obra_social, $forma_pago, $id_departamento, $id_convenio, $sexo) = mssql_fetch_row ($msresult3)) {
                            $dep_result = mysql_query("SELECT cod_departamento FROM departamentos WHERE id_departamento = $id_departamento AND cod_empresa = '$dbname'");
                
                            if ($dep_result) list ($cod_departamento) = mysql_fetch_row($dep_result);
                            else $cod_departamento = 'NONE';
                            if (!is_int($id_banco)) $id_banco = 'NULL';
                            if (!is_int($id_obra_social)) $id_obra_social = 'NULL';
                            if (!is_int($id_convenio)) $id_convenio = 'NULL';
                            if (!is_int($id_departamento)) $id_departamento = -1;
                            $apellido = mysql_escape_string($apellido);
                            $nombre = mysql_escape_string($nombre);
                
                            echo "\t[+] Insertando legajo $nro_legajo ($apellido, $nombre)\n";
                
                            $mi_idx=$dbname.':'.$nro_legajo;
                
                            $query_ins = mysql_query ("INSERT INTO legajos_updated (legajo_idx, nro_legajo, id_legajo, id_departamento, cod_departamento, cod_empresa, apellido, nombre, tipo_documento, nro_documento, fecha_nacimiento, ingreso, egreso, motivo_egreso, cod_categoria, desc_categoria, habilitado, sueldo, cuil, forma_pago, id_banco, id_obra_social, id_convenio, sexo) VALUES('$dbname:$nro_legajo', $nro_legajo, $id_legajo, $id_departamento, '$cod_departamento', '$dbname', '$apellido', '$nombre', '$desc_documento', '$nro_documento', '$fecha_nacimiento', '$fecha_ingreso', '$fecha_egreso', '$desc_motivo_egreso', '$cod_categoria', '$desc_categoria', '$habilitado', $sueldo, '$cuil', '$forma_pago', $id_banco, $id_obra_social, $id_convenio, '$sexo')");
                
                            if (mysql_affected_rows() != 1) {
                                //echo "\t[-] Error Insertando legajo VALUES('$dbname:$nro_legajo', $nro_legajo, $id_legajo, $id_departamento, '$cod_departamento', '$dbname', '$apellido', '$nombre', '$desc_documento', '$nro_documento', '$fecha_nacimiento', '$fecha_ingreso', '$fecha_egreso', '$desc_motivo_egreso', '$cod_categoria', '$desc_categoria', '$habilitado', $sueldo, '$cuil', '$forma_pago', $id_banco, $id_obra_social, $id_convenio, '$sexo')\n";
                
                
                                echo mysql_error() . PHP_EOL;
                            }
                        } // while con legajos
                
                        if ($rapido == 0) {
                            // Novedades
                
                            $msresult3 = mssql_query ("SELECT id_novedad, cod_novedad, desc_novedad, unidad_cantidad FROM novedad");
                            while (list ($id_novedad, $cod_novedad, $desc_novedad, $unidad_cantidad) = mssql_fetch_row ($msresult3)) {
                                echo "\t[+] Insertando novedad $desc_novedad\n";
                
                                 //mysql_query ("INSERT INTO tipos_novedades (tipo_novedad_idx, cod_novedad, id_novedad, cod_empresa, desc_novedad, unidad_cantidad) VALUES('$dbname:$cod_novedad', '$cod_novedad', $id_novedad, '$dbname', '$desc_novedad', '$unidad_cantidad')");
                        }
                            // Convenios
                                    $msresult3 = mssql_query ("SELECT id_convenio, cod_convenio, desc_convenio FROM convenio");
                                    while (list ($id_convenio, $cod_convenio, $desc_convenio) = mssql_fetch_row ($msresult3)) {
                                            echo "\t[+] Insertando convenio $desc_convenio\n";
                            // mysql_query ("INSERT INTO convenios(cod_empresa, cod_convenio, id_convenio, desc_convenio) VALUES ('$dbname', '$cod_convenio', $id_convenio, '$desc_convenio')");
                                    }
                        }
                    } else {
                        echo "[-] DB $dbname no tiene formato de empresa\n";
                    }
                
                    mssql_free_result($msresult2);

                //} // while principal de recorrido de bases




                //sqlsrv_close($conn);




?>