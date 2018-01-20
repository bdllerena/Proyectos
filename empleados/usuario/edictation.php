<?php
// including the database connection file
include_once("../include/funciones.php");


$funciones = new funciones();

if(isset($_POST['update']))
{
	$id = $funciones->escape_string($_POST['id_usu']);
	$nombre = $funciones->escape_string($_POST['txtNombre']);
  $cedula = $funciones->escape_string($_POST['txtCedula']);
  $genero = $funciones->escape_string($_POST['txtGenero']);
  $clave = $funciones->escape_string($_POST['txtClave']);
        //updating the table
        $result = $funciones->execute("UPDATE usuario SET nombre_usu='$nombre',cedula_usu='$cedula',genero_usu='$genero',clave_usu='$clave' WHERE id_usu=$id");

        //redirectig to the display page. In our case, it is index.php
        header("Location: usuariopanel.php");

}
?>
