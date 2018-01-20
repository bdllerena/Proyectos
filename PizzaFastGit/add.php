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
include_once("include/funciones.php");

$funciones = new funciones();

if(isset($_POST['Submit'])) {
  //id_usuario	nombre	apellido	cedula	nickname	clave	latitud	longitud
	$nombre = $funciones->escape_string($_POST['txtNombre']);
	$apellido = $funciones->escape_string($_POST['txtApellido']);
  $cedula = $funciones->escape_string($_POST['txtCedula']);
  $nickname = $funciones->escape_string($_POST['txtNickname']);
  $clave = $funciones->escape_string($_POST['txtClave']);
  $latitud = $funciones->escape_string($_POST['txtLatitud']);
  $longitud = $funciones->escape_string($_POST['txtLongitud']);
  $result = $funciones->execute("INSERT INTO usuario (nombre,apellido,cedula,nickname,clave,latitud,longitud) VALUES('$nombre','$apellido','$cedula','$nickname','$clave','$latitud','$longitud')");

        //display success message

   // echo "<font color='green'>Datos añadidos.";
    echo "<div class=\"alert alert-success\" role=\"alert\">  <strong>Logrado!</strong> Se registro con exito</div>";
    echo "<br/><a href='index.php'><button class=\"btn btn-success\">Continuar</button></a>";
    // checking empty fields
    /*
	if($msg != null) {
        echo $msg;
        //link to the previous page
        echo "<br/><a href='javascript:self.history.back();'>Go Back</a>";
    } elseif (!$check_age) {
        echo 'Please provide proper age.';
    } elseif (!$check_email) {
        echo 'Please provide proper email.';
    }
    else {
        // if all the fields are filled (not empty)

        //insert data to database
        $result = $crud->execute("INSERT INTO users(name,age,email) VALUES('$name','$age','$email')");

        //display success message
        echo "<font color='green'>Data added successfully.";
        echo "<br/><a href='index.php'>View Result</a>";
    }*/
}
?>
<script src="http://code.jquery.com/jquery.js"></script>
<!--<script src="js/bootstrap.min.js"></script>-->
</body>
</html>
