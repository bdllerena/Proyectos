<?php
   include("includes/Dbconfig.php");
   session_start();
   $error = "Bienvenido";
function conecta(){
	//$cn = mysql_connect("localhost","login","login2354");
	$cn = mysqli_connect("localhost","root","","login");
}

function get_form_login(){
	$retval = '
	<form name="login" action="" method="POST">
		<table align="center" border=0>
			<tr>
				<th colspan="2">FORMUALRIO DE REGISTRO</th>
			</tr>
			<tr>
				<th>Username: </th>
				<td><input type="text" name="username" size=10></td>
			</tr>
			<tr>
				<th>Password:</th>
				<td><input type="password" name="password" size=10></td>
			</tr>
			<tr>
				<td colspan=2 align="center"><input type="submit" name="login"></td>
			</tr>
		</table>
	</form>';

	return $retval;
}

function valida_login($username,$password){
	conecta();
	$sql = 'SELECT * FROM usuarios WHERE username="' . $username . '" AND password="' . $password . '";';
	//$result = mysql_query($sql);
	$result = mysqli_query($db,$sql);
	if($user=mysql_fetch_array($result)){

		return $user;

	}else{
		return false;
	}
	mysql_close($cn);
}

function display_menu($level){
	conecta();
	$sql = "SELECT * FROM menu WHERE nivel BETWEEN 0 AND $level;";
	$res = mysql_query($sql);
	$retval = '
	<table align="center" style="border-collapse: separate;border-spacing:  10px;">';
	while($item = mysql_fetch_array($res)){
		$retval .= '
			<tr>
				<th style="background-color: #F3E2A9";"><a style="text-decoration:none; color: #000000;  font-size: 35px" href="'. $item['ruta'] .'">'. $item['etiqueta'] .'</a></th>
			</tr>';
	}
	$retval .= '
			<tr>
				<th style="background-color: #F3E2A9";"><a style="text-decoration:none; color: #000000;  font-size: 35px" href="includes/logout.php">Cerrar Sesión</a></th>
			</tr>
	</table>';

	return $retval;
	mysql_close();
}
?>
