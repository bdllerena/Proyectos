<?php
function conexion(){
	//$user = "php";
	//$password = "m3ywW5QFN7HLvJua";
	$user = "root";
	$password = "";
	$servidor = "localhost";
	$db = "land_conjunta";
	$c = mysqli_connect($servidor,$user,$password,$db);
	//mysqli_set_charset($c,"utf8");
	return $c;
}

function get_form($id){
	$c = conexion();
	if($id == NULL){
		$nombre = NULL;
		$cedula = NULL;
		$genero = NULL;
		$empleado=NULL;
	}else{
		$sql = "SELECT * FROM usuario WHERE ID_USU=$id;";
		$res = mysqli_query($c,$sql);
		$p = mysqli_fetch_assoc($res);
		$nombre = $p['nombre'];
		$empleado= $p['empleado']

	}
	
	$html = '
	<form name="usuario" action="" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="codigo" value="' . $id . '">
		<table align="center" border="0">
			<tr>
				<th colspan="2">DATOS DE USUARIO</th>
			</tr>
			<tr>
				<td>Nombre: </td>
				<td><input type="text" name="nombre" size="8" value="' . $nombre . '"><
			<tr>
				<td>Empleado: </td>
				<td>' . combo_empleado($empleado) . '</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="guardar" value="GUARDAR"></td>
			</tr>
		</table>
	</form>';
	return $html;
}

function get_list(){
	$sql = "SELECT u.ID_USU, u.NOMBRE_USU, u.CEDULA_USU,u.GENERO_USU, e.NOMBRE_EMP as empleado
			FROM usuario u, empleado e
			WHERE u.empleado=e.id";
	//		echo $sql;
	//		exit;
	$html = '
	<table border=1 align="center">
		<tr>
			<th colspan="5">LISTA DE USUARIOS</th>
		</tr>
		<tr>
			<th colspan="5"><a href="index.php?op=new"><img src="iconos/nuevo.png"></a></th>
		</tr>
		<tr>
			<th>Nombre</th>
			<th>EMPLEADO</th>
			<th colspan="3">Acciones</th>
		</tr>';
	$con = conexion();
	$res = mysqli_query($con,$sql);
	while($p = mysqli_fetch_assoc($res)){
		$html .= '
			<tr>
				<td>' . $p['nombre_usu'] . '</td>
				<td>' . $p['empleado'] . '</td>
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
	$sql = "DELETE FROM usuario WHERE ID_USU=$id;";
	if(mysqli_query($con,$sql)){
		$html = '
		<table border="0" align="center">
		<tr>
			<th>Se borró correctamnte</th>
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
	$nombre = $_POST['nombre'];
	$cedula = $_POST['cedula'];
	$genero = $_POST['genero'];
	$empleado=$_POST['empleado'];
	$id = $_POST['ID_USU'];
	if( $id == NULL){
		$sql = "INSERT INTO usuario VALUES(NULL,'$nombre','$cedula','$genero','$empleado');";
	}else{
		$sql = "UPDATE usuario SET NOMBRE_US='$nombre',CEDULA_USU='$cedula',GENERO_USU='$genero',empleado=$empleado WHERE ID_USU=$id;";
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

function combo_empleado($defecto){
	$cn = conexion();
	$sql = "SELECT * FROM continente;";
	$res = mysqli_query($cn,$sql);
	$html = '
	<select name="empleado">' . "\n";
	while($con = mysqli_fetch_assoc($res)){
			$html .= ($defecto == $con['id']) ?'<option value="' . $con['id'] . '" selected>' . $con['nombre'] . '</option>' . "\n":'<option value="' . $con['id'] . '">' . $con['nombre'] . '</option>' . "\n";
		
	}
	$html .= '</select>';
	return $html;
}
?>

