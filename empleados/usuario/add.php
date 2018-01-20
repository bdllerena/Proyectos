<html>
<head>
    <title>Usuario añadido</title>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
<?php
//including the database connection file
include_once("../include/funciones.php");

$funciones = new funciones();

if(isset($_POST['Submit'])) {
  //id_usuario	nombre	apellido	cedula	nickname	clave	latitud	longitud
	$nombre = $funciones->escape_string($_POST['txtNombre']);
  $cedula = $funciones->escape_string($_POST['txtCedula']);
  $genero = $funciones->escape_string($_POST['txtGenero']);
  $clave = $funciones->escape_string($_POST['txtClave']);
  $result = $funciones->execute("INSERT INTO usuario (nombre_usu,cedula_usu,genero_usu,clave_usu) VALUES('$nombre','$cedula','$genero','$clave')");

        //display success message

   // echo "<font color='green'>Datos añadidos.";
    echo "<div class=\"alert alert-success\" role=\"alert\">  <strong>Logrado!</strong> Se registro con exito</div>";
    echo "<br/><a href='usuariopanel.php'><button class=\"btn btn-success\">Continuar</button></a>";
  }
  ?>
  <script src="http://code.jquery.com/jquery.js"></script>
  <!--<script src="js/bootstrap.min.js"></script>-->
  </body>
  </html>
