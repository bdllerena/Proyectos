<?php
	//require('cursophp/integrado/pages/academico/include/xajax/xajax_core/xajax.inc.php');
	require('include/xajax/xajax_core/xajax.inc.php');
	// generar funcion para conectarse con la base de datos
	// funcion 1.- conectarse a la base de datos
	function conexion_bdd()
	{
		
	$user = "root";
	$password = "";
	$servidor = "localhost";
	$db = "login";
	$c = mysqli_connect($servidor,$user,$password,$db); // cadena de conexion`
	mysqli_set_charset($c,"utf8");  // instruccion para que se visualice tildes, caracteres especiales 
	return $c;
		
	}

	// 2.- funcion para construir el formulario ---------------------------------------
	// generar funcion para crear formulario en tiempo de ejecion 
	function conseguir_formulario($id)
	{
			// ---- llamar a la conexion con la base de datos------
		//echo "<br> va a ejecutar la cadena de conexion ";
		$c=conexion_bdd();
		//echo "<br> debe haber ejecutado la cadena de conexion con la base de datos ";
		// a continuacion encerar las variables verificando si se ha cargado o no los registros y su estado (null o not null)
		if ($id == NULL){	// VERIFICA estado de los atributos si son NULL o si estan en NO  NULL en memoria
			$carrera=NULL;
			//$apellidos=NULL;
		}
		else{
			$sql = "SELECT * FROM carrera where cod_car=$id;";
			//$sql = "SELECT * FROM alumno WHERE codigo=$id;"; // realiza un select para cargar todos los registros cuyo ID sea igual al id de la bdd
			$res = mysqli_query($c,$sql);		// ejecuta el query
			$p = mysqli_fetch_assoc($res);		// asigna el resultado del query  y lo asocia 
			$carrera = $p['nom_car'];			// toma del record set y lo asigna a la variable de la 1ra columna del record set`
			//$apellidos = $p['apellidos'];		// toma del record set y lo asinga a la variable de la 2da columna del record set
		}
		// a continuacion empieza el proceso de codificacion para el formulario
		$html='			// inicia la variable que contendra todo el formulario
		<form name="alumnos" action="" method="POST">
		<input type="hidden" name="id" value="' . $id . '">
			<table border="3" align="center">
				<tr>
					<th colspan="2"> DATOS DEL ALUMNO </th>
				</tr>
				<tr>
					<td> Nombres : </td>
					<td><input type="text" name="nom_car" size="14" value="' . $carrera . '"> </td>
				</tr>
				
				<tr>
				<td colspan="2" align="center"><input type="submit" name="guardar" value="GUARDAR" ></td>
			</tr>
			<tr>
				<td id="r"></td>
			</table>
		</form>
		';			// aqui termina la variable que contiene todo el formulario
		// a continuacion retorna el formulario completo a traves de la variable que lo contiene
		return $html;   // ok ok que tal hasta ahi??????
		
	}
	// 3.- funcion para listar los registros del formulario ---------------------------------------------------------
	// bien very good a continuacion vamos a generar la siguiente funcion para listar todos los registros ok ????
	function listar_formulario(){
			$xajax = new xajax(); 
			$xajax->register(XAJAX_FUNCTION, 'mostrar'); 
			$xajax->processRequest(); 
			$xajax->printJavascript('include/xajax/');
		
		
		// iniciamos generando una sentencia sql para cargar los registros
		$sql = "select * from carrera ";
		// a continuacion generamos el formulario completo tambien en una variable de instancia
		$html='
			<table border="3" align="center">
				<tr>
					<th colspan="5"> LISTADO DE CARRERAS </th>
				</tr>
				<tr>
					<th colspan="5"><a href="index.php?op=nuevo"> <img src="iconos/nuevo.png"></a> </th>
				</tr>
				<tr>
					<th> Nombres : </th>
					
					<th colspan="3"> Acciones </th>
				</tr>';
	
		// hasta aqui termina el formato de tabla con los titulos de la tabla nombres y apellidos
		// a continuacion la inter accion con la base de datos
		$con = conexion_bdd(); //conectar con la funcion para la base de datos
		$res = mysqli_query($con, $sql); // ejecuta la sentencia sql con la conexion respectiva
		// a continuacion se recorre todo el cursor que se encuentra en memoria RAM 
		while($p = mysqli_fetch_assoc($res))
		{
				
				$html .= '
					<tr>
						<td> ' . $p['nom_car'] . ' </td>
						
						<td> <a href="index.php?op=del&id=' .  $p['cod_car'] . '"><img src="iconos/borrar.png"> </a> </td> 
						<td> <a href="index.php?op=up&id=' . $p['cod_car'] . '"><img src="iconos/editar.png"> </a> </td> 
						
					</tr>
				';
		}
		// concatenar la variable del formulario
		$html .='
		<tr>
		  <td colspan="3" align="center"><a href="../../index.php?menu=true">Regresar Menu principal</a></td>
		</tr>
		<tr>
		   <td>Ajax  de carrera</td>
		  <td><input type="button" id="ejecajax"   name="ejecajax" value="EJECUTAR AJAX" onclick="xajax_mostrar()"></td>
		  
		  <td>info de verificacion<br></td>
		  <br>
		</tr>
		<tr>
		<td id="r"> </td>
		
		</tr>
		
		
		<table>
		';
		return $html;
		
	} // finaliza la funcion listar formulario
	
	//----------------------------------- a continuacion la funcion para guardar ----------------------------------------
	// 4.- formulario para guardar un nuevo registro -----------------------------------------------------
	
	function guardar(){
		$id = $_POST['cod_car'];
		$nombres = $_POST['nom_car'];
		//$apellidos = $_POST['apellidos'];
		
		if( $id == NULL)
		{
			$sql = "INSERT INTO carrera VALUES (NULL, '$nombres');";
			//echo $sql;exit;
		}
		else
		{
			echo "<br> Actualizar informacion de la carrera";
			$sql = "UPDATE carrera SET nom_car='$nombres' WHERE cod_car=$id;";
		}
		$con = conexion_bdd();
		if(mysqli_query($con, $sql)){
				$html='
				<table border="0" align="center">
					<tr>
						<th>Se guardó correctamnte</th>
					</tr>
					<tr>
						<th><a href="index.php">Regresar</a></th>
					</tr>
				</table>';
		}
		else{
			$html='
			<table border="0" align="center">
				<tr>
					<th>No se guardo el registro. Por favor contactar al admnistrador</th>
				</tr>
				<tr>
					<th><a href="index.php">Regresar</a></th>
				</tr>
			</table>';
		}
	return $html;		
	}	// fin del metodo guardar()
	
	// 5.- la funcion para el boton eliminar
	function eliminar($id)
	{	
		$con = conexion_bdd(); // conectarse con la bdd
		$sql="DELETE FROM carrera WHERE cod_car=$id;";	// sql para eliminar DML
		echo "<br> verificar la funcion sql para eliminar " . $sql;
		if(mysqli_query($con, $sql)){
			$html='
				<table border=3 align="center">
					<tr>
						<th>Se borro correctamente </th>
					</tr>
					<tr>
						<th><a href="index.php"> Regresar </a> </th>
					</tr>
				</table>
			';
			
		}
		else{
				$html='
					<table border="3" align="center">
						<tr>
							<th> No se elimino el registro Revisar </th>
						</tr>
						<tr>
							<th> <a href="index.php"> Regresar </a> </th>
						</tr>
					</table>
				';
		}
		return $html;
		
	}
	
?>