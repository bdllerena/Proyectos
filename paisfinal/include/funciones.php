<?php


function conexion(){
	//$user = "php";
	//$password = "m3ywW5QFN7HLvJua";
	$user = "root";
	$password = "root";
	$servidor = "localhost";
	$db = "pais";
	$c = mysqli_connect($servidor,$user,$password,$db);
	//mysqli_set_charset($c,"utf8");
	return $c;
}

function get_form($id){
	$c = conexion();
	if($id == NULL){
		$nombre = NULL;
		$continente = NULL;
		$bandera = NULL;
		$f = '<input type="file" name="bandera">';
	}else{
		$sql = "SELECT * FROM pais WHERE id=$id;";
		$res = mysqli_query($c,$sql);
		$p = mysqli_fetch_assoc($res);
		$nombre = $p['nombre'];
		$continente = $p['continente'];
		$f = '<img src="' . $p['bandera'] . '" width="200px">';
	}
	
	$html = '
	<form name="pais" action="" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="id" value="' . $id . '">
		<table align="center" border="0">
			<tr>
				<th colspan="2">DATOS DE PAÍS</th>
			</tr>
			<tr>
				<td>Nombre: </td>
				<td><input type="text" name="nombre" size="8" value="' . $nombre . '"></td>
			</tr>
			<tr>
				<td>Continente: </td>
				<td>' . combo_continente($continente) . '</td>
			</tr>
			<tr>
				<td>Bandera: </td>
				<td>' . $f . '</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="guardar" value="GUARDAR"></td>
			</tr>
		</table>
	</form>';
	return $html;
}

function get_list(){
	$sql = "SELECT p.id, p.nombre, p.bandera, c.nombre as continente 
			FROM pais p, continente c
			WHERE p.continente=c.id";
	//		echo $sql;
	//		exit;
	$html = '
	<table border=1 align="center">
		<tr>
			<th colspan="5">LISTA DE PAÍSES</th>
		</tr>
		<tr>
			<th colspan="5"><a href="index.php?op=new"><img src="iconos/nuevo.png"></a></th>
		</tr>
		<tr>
			<th>Nombre</th>
			<th>Continente</th>
			<th colspan="3">Acciones</th>
		</tr>';
	$con = conexion();
	$res = mysqli_query($con,$sql);
	while($p = mysqli_fetch_assoc($res)){
		$html .= '
			<tr>
				<td>' . $p['nombre'] . '</td>
				<td>' . $p['continente'] . '</td>
				<td><a href="index.php?op=del&id=' . $p['id'] . '&bandera=' . $p['bandera'] . '"><img src="iconos/borrar.png"></a></td>
				<td><a href="index.php?op=up&id=' . $p['id'] . '"><img src="iconos/editar.png"></a></td>
				<td><a href="' . $p['bandera'] . '" target="_blank"><img src="iconos/imagen.jpg"></a></td>
			</tr>';
	}
	
	$html .= '
	</table>';
	return $html;
	
}

function eliminar($id,$bandera){
	$con = conexion();
	$sql = "DELETE FROM pais WHERE id=$id;";
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
	unlink($bandera);
	return $html;
	
	
}

function guardar(){
	$nombre = $_POST['nombre'];
	$continente = $_POST['continente'];
	$id = $_POST['id'];
	if( $id == NULL){
	$bandera = "flags/" . nombre_bandera($_FILES['bandera']['name']);
	if(!move_uploaded_file($_FILES['bandera']['tmp_name'],$bandera)){
		echo "error al subir el archivo";
	}
		$sql = "INSERT INTO pais VALUES(NULL,'$nombre',$continente,'$bandera');";
	}else{
		$sql = "UPDATE pais SET nombre='$nombre',continente=$continente WHERE id=$id;";
	}
	$con = conexion();
	//echo $sql;
	//exit;
	if(mysqli_query($con,$sql)){
		$html = '
		<table border="0" align="center">
		<tr>
			<th>Se guardó correctamnte</th>
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

function nombre_bandera($nombre){
	$a = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
				"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
				"0","1","2","3","4","5","6","7","8","9");
	$tmp_name = explode(".", $nombre);
	$n = count($tmp_name);
	$ext = $tmp_name[$n-1];
	$nom=NULL;		
	for($i=0;$i<15;$i++){
		$r = rand(0,61);
		$nom .= $a[$r];
	}			
	return $nom . "." . $ext;
}


function combo_continente($defecto){
	$cn = conexion();
	$sql = "SELECT * FROM continente;";
	$res = mysqli_query($cn,$sql);
	$html = '
	<select name="continente">' . "\n";
	while($con = mysqli_fetch_assoc($res)){
			$html .= ($defecto == $con['id']) ?'<option value="' . $con['id'] . '" selected>' . $con['nombre'] . '</option>' . "\n":'<option value="' . $con['id'] . '">' . $con['nombre'] . '</option>' . "\n";
		
	}
	$html .= '</select>';
	return $html;
}
?>

