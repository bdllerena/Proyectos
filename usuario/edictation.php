<?php
// including the database connection file
include_once("include/funciones.php");

 
$funciones = new funciones();

if(isset($_POST['update']))
{  
	$id = $funciones->escape_string($_POST['id']);  
	$nombre = $funciones->escape_string($_POST['txtNombre']);
	$clave = $funciones->escape_string($_POST['txtClave']);
	$estado = $funciones->escape_string($_POST['rbestado']);
	$codigoMD5 = $funciones->escape_string($_POST['txtMD5']);
    
   
        //updating the table
        $result = $funciones->execute("UPDATE usuarios SET nombre='$nombre',clave='$clave',estado='$estado',codigoMD5='$codigoMD5' WHERE codigo=$id");
        
        //redirectig to the display page. In our case, it is index.php
        header("Location: index.php");
    
}
?>