<?php
// including the database connection file
include_once("include/funciones.php");

 
$funciones = new funciones();

if(isset($_POST['update']))
{  
	$id = $funciones->escape_string($_POST['id']);  
	$stock = $funciones->escape_string($_POST['txtStock']);
   
        //updating the table
        $result = $funciones->execute("UPDATE product SET stock='$stock' WHERE id=$id");
        
        //redirectig to the display page. In our case, it is index.php
        header("Location: index.php");
    
}
?>