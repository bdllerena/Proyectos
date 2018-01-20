<?php
function conexion(){
	//$user = "php";
	//$password = "m3ywW5QFN7HLvJua";
	$user = "root";
	$password = "";
	$servidor = "localhost";
	$db = "landazuri_conjunta";
	$c = mysqli_connect($servidor,$user,$password,$db);
	//mysqli_set_charset($c,"utf8");
	return $c;
}

function get_form($id){
	$c = conexion();
	if($id == NULL){
		$NOMBRE_EMP = NULL;
		$RUC_EMP= NULL;
	}else{
		$sql = "SELECT * FROM empleado WHERE ID_EMP=$id;";
		$res = mysqli_query($c,$sql);
		$p = mysqli_fetch_assoc($res);
		$NOMBRE_EMP = $p['NOMBRE_EMP'];
		$RUC_EMP = $p['RUC_EMP'];

	}
	
	$html = '
	<form name="empleado" action="" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="id" value="' . $id . '">
		<table align="center" border="0">
			<tr>
				<th colspan="2">DATOS DE EMPLEADO</th>
			</tr>
			<tr>
				<td>Nombre: </td>
				<td><input type="text" name="NOMBRE_EMP" size="8" value="' . $NOMBRE_EMP . '"></td>
			</tr>
			<tr>
				<td>RUC: </td>
				<td><input type="text" name="RUC_EMP" size="8" value="' . $RUC_EMP . '"></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="guardar" value="GUARDAR"></td>
			</tr>
		</table>
	</form>';
	return $html;
}

function get_list(){
	$sql = "SELECT  NOMBRE_EMP,RUC FROM empleado";
	//		echo $sql;
	//		exit;
	$html = '
	<table border=1 align="center">
		<tr>
			<th colspan="5">LISTA DE EMPLEADOS</th>
		</tr>
		<tr>
			<th colspan="5"><a href="index.php?op=new"><img src="iconos/nuevo.png"></a></th>
		</tr>
		<tr>
			<th>Nombre</th>
			<th>RUC</th>
			<th colspan="3">Acciones</th>
		</tr>';
	$con = conexion();
	$res = mysqli_query($con,$sql);
	while($p = mysqli_fetch_assoc($res)){
		$html .= '
			<tr>
				<td>' . $p['NOMBRE_EMP'] . '</td>
				<td>' . $p['RUC_EMP'] . '</td>
				<td><a href="index.php?op=del&id=' . $p['id'] . '"><img src="iconos/borrar.png"></a></td>
				<td><a href="index.php?op=up&id=' . $p['id'] . '"><img src="iconos/editar.png"></a></td>

			</tr>';
	}
	
	$html .= '
	</table>';
	return $html;
	
}

function eliminar($id){
	$con = conexion();
	$sql = "DELETE FROM empleado WHERE ID_EMP=$id;";
	if(mysqli_query($con,$sql)){
		$html = '
		<table border="0" align="center">
		<tr>
			<th>Se borró correctamente</th>
		</tr>
		<tr>
			<th><a href="index.php">Regresar</a></th>
		</tr>
		</table>';
	}else{
		$html = '
		<table border="0" align="center">
		<tr>
			<th>No se borró el registro. Por favor contactar al administrador a dbadillo@ecualinux.com</th>
		</tr>
		<tr>
			<th><a href="index.php">Regresar</a></th>
		</tr>
		</table>';
	}
	return $html;
	
	
}

function guardar(){
	$NOMBRE_EMP = $_POST['nombre_'];
	$id = $_POST['id'];
	if( $id == NULL){
		$sql = "INSERT INTO empleado VALUES(NULL,'$NOMBRE_EMP','$RUC_EMP');";
	}else{
		$sql = "UPDATE empleado SET NOMBRE_EMP='$NOMBRE_EMP',RUC_EMP='$clave', WHERE ID_EMP=$id;";
	}
	$con = conexion();
	//echo $sql;
	//exit;
	if(mysqli_query($con,$sql)){
		$html = '
		<table border="0" align="center">
		<tr>
			<th>Se guardó correctamente</th>
		</tr>
		<tr>
			<th><a href="index.php">Regresar</a></th>
		</tr>
		</table>';
	}else{
		$html = '
		<table border="0" align="center">
		<tr>
			<th>No se guardo el registro. Por favor contactar al administrador a dbadillo@ecualinux.com</th>
		</tr>
		<tr>
			<th><a href="index.php">Regresar</a></th>
		</tr>
		</table>';
	}
	return $html;
}
?>

