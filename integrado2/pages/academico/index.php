<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">

<head>
	<title>Sistema de Alumnos</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>
<body>
<?php
//include("include/funciones_alumno.php");	// permite enlazar con el archivo de funciones que acabamos de elaborar
include("include/carrera.php");	// permite enlazar con el archivo de funciones que acabamos de elaborar
	function mostrar(){
		$html = 'hola';
		$response = new xajaxResponse(); 
		$response->assign('r','innerHTML',$html);
	}
	
session_start();
if($_SESSION['nivel'] >= 2){

if(isset($_GET['op']) && $_GET['op'] == 'del'){		// realiza las verificaciones si el usuario selecciono una opcion entonces a donde debe ir????
	echo eliminar($_GET['id']);		// falta por el momento la funcion eliminar
}elseif(isset($_GET['op']) && $_GET['op'] == 'nuevo'){
	echo conseguir_formulario(NULL);
}elseif(isset($_GET['op']) && $_GET['op'] == 'up'){
	echo conseguir_formulario($_GET['id']);
}else{
	echo listar_formulario();
}

if(isset($_POST['guardar'])){
	echo guardar();
}
	// vamoas a proceder a verificar la funcionalidad desde el index
}else{
	echo "Usted no tiene permisos para acceder a este sitio";
}
?>
</body>
</html>
