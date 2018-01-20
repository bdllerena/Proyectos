<?php
// including the database connection file
include_once("../include/funciones.php");


$funciones = new funciones();

if(isset($_POST['update']))
{
	$id = $funciones->escape_string($_POST['id_usu']);
	$ide = $funciones->escape_string($_POST['id_emp']);
	$nombre = $funciones->escape_string($_POST['txtNombre']);
  $ruc= $funciones->escape_string($_POST['txtRuc']);
        //updating the table
        $result = $funciones->execute("UPDATE empleado SET nombre_emp='$nombre',ruc_emp='$ruc' WHERE id_usu=$id AND id_emp=$ide");

        //redirectig to the display page. In our case, it is index.php
        header("Location: empleado.php");

}
?>
