<?php
// including the database connection file
include_once("include/funciones.php");

 
$funciones = new funciones();

if(isset($_POST['update']))
{  
	$id = $funciones->escape_string($_POST['id']);  
	$nombre = $funciones->escape_string($_POST['txtNombre']);
    
   
        //updating the table
        $result = $funciones->execute("UPDATE departamento SET nombre='$nombre' WHERE codigo=$id");
        
        //redirectig to the display page. In our case, it is index.php
        header("Location: index.php");
    
}
?>