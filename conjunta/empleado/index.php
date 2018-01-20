<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">

<head>
	<title>Sistema País</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
</head>
<body>
<?php
include("include/funciones.php");

if(isset($_GET['op']) && $_GET['op'] == 'del'){
	echo eliminar($_GET['id']);
}elseif(isset($_GET['op']) && $_GET['op'] == 'new'){
	echo get_form(NULL);
}elseif(isset($_GET['op']) && $_GET['op'] == 'up'){
	echo get_form($_GET['id']);
}else{
	echo get_list();
}

if(isset($_POST['guardar'])){
	echo guardar();
}
?>
</body>
</html>
