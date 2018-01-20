<?php
include("includes/login.php");
session_start();
if(isset($_POST['login'])){
	$password = sha1($_POST['password']);
	$user = valida_login($_POST['username'],$password);
	if($user != false){			// carga los datos del usuario en las variables de sesion en variables de sesion /c/wamp/tmp/ obervo cual variable por fecha
		$_SESSION['nivel'] = $user['nivel'];		// carga el nivel de la base de datos asignado al usuario en la sesion
		$_SESSION['nombre'] = $user['nombre'];		// carga el nombre del usuario de la bdd asociado a la sesion
		echo "Bienvenid@ " . $_SESSION['nombre'] . "</br>";
		echo display_menu($_SESSION['nivel']);		// asocia al menu y lo despliega segun la sesion del usuario
	}else{
		
		echo "No existe el usario o contraseña, vuelva a intentarlo";
	}
}elseif (isset($_GET['menu']) && $_SESSION['nivel'] != NULL){	// valida get menu, nivel de usuario sea diferente a null
	echo "Usuari@ " . $_SESSION['nombre'] . "</br>";			// despliega que usuario esta en sesion 
	echo display_menu($_SESSION['nivel']);						// aqui despliega el menu
}else
{
	echo get_form_login();
}
?>
