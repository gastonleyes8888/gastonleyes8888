<?php




//$rapido = 0;
//if (!isset ($funciones)) include ("funcionesv2.php");
//if (openmydb() == NULL) die('Imposible conectar a MySQL');
//if (openmsdb() == NULL) die('Imposible conectar a MSSQL');

//unlink("/home/operac/ombu2/csv/control_tablas.csv");
//$file = fopen("/home/operac/ombu2/csv/control_tablas.txt", "w");


//$tablas = array("empresas", "departamentos", "legajos", "bancos", "obras_sociales", "tipos_novedades", "convenios");

//$tablas_rapido = array("legajos");

// Si o si porque cambian los idx

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
function procesar_tabla($table) // procesar tabla
{
	global $mydb; 
	echo "[+] Procesando tabla $table\n";
	$sufijo = date('w');
	$old_table = $table . "_" . $sufijo;
}
*/

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//VEDIA_SCS	1185	40848667	Azcona, Jonathan Daniel	20-40848667-0	VEDIA SAENZ PEÑA	08/08/2024	CAD/AP AYU

// Departamentos
$connectionInfo2 = array("UID" => "sa", "PWD" => "Axoft1988", "Database" => "VEDIA_SCS","Encrypt"=>"No","TrustServerCertificate"=>"No"); //USUARIO Y CONTRASEÑA DE TANGO
$conn=sqlsrv_connect("190.183.146.115 , 39342",$connectionInfo2); //INGRESAR ORIGEN BASE DE DATOS

			$consulta_dptos="SELECT d.id_departamento, d.cod_departamento, d.desc_departamento FROM departamento d";
			$msresult1= sqlsrv_query( $conn, $consulta_dptos);


			//while (list ($id, $cod, $desc) = mssql_fetch_row ($msresult1)) {
				//while( list ($id, $cod, $desc) = $row = sqlsrv_fetch_array( $msresult1, SQLSRV_FETCH_ASSOC) ) {

					while( $row = sqlsrv_fetch_array( $msresult1, SQLSRV_FETCH_NUMERIC) ) {
						echo $row[0].", ".$row[1].", ".$row[2]."<br />";
						}


				//echo "\t[+] Insertando departamento $cod\n";

			//$imprimir_departamentos = $cod.';'.$dbname.';'.$calle.';'.$numero.';'.$localidad.';'.$codigo_postal.';'.$cuit.';'.$inicio_actividad;
			//fwrite($file_departamentos, $imprimir_departamentos . PHP_EOL);

			//	mysql_query ("INSERT INTO departamentos (cod_departamento, cod_empresa, id_departamento, desc_departamento) VALUES('$cod', '$dbname', $id, '$desc')");


			// Bancos


sqlsrv_close($conn);
//closemsdb();
//closemydb();


echo "---------FUNCIONA OKA ------------------------------------------------------------------------------<br>";

/*
$enlace = mysqli_connect("181.15.193.68", "ombu", "1qazxsw2", "ombu", "3306");


$consulta = "SELECT n.novedad_id AS ID, CONCAT(l.apellido, ', ', l.nombre, ' (', l.nro_legajo, ')') AS legajo, n.nro_documento AS documento, 
CONCAT(tn.desc_novedad, ' (', tn.unidad_cantidad, ')') AS novedad, e.desc_empresa AS empresa, n.fecha AS fecha, 
n.unidad_cantidad_valor AS cantidad, n.observaciones AS observaciones, n.estado AS estado, desc_departamento departamento
FROM novedades n, empresas e, legajos l, tipos_novedades tn, departamentos d
WHERE n.cod_empresa = e.cod_empresa
AND n.legajo_idx = l.legajo_idx
AND n.cod_novedad_idx = tn.tipo_novedad_idx 
AND n.cod_departamento = d.cod_departamento
AND d.cod_empresa = e.cod_empresa
AND n.fecha > DATE_SUB(curdate(), INTERVAL 1 MONTH)
ORDER BY n.novedad_id DESC";
                                                  $resultado = mysqli_query($enlace , $consulta);
                                                  $row_cnt = mysqli_num_rows($resultado);
                                                  echo "<small>Total Consulta: $row_cnt registros</small><br>";
                                                  echo "Tablas: novedades, empresas, legajos, tipos_novedades, departamentos<br>";

 */
?>