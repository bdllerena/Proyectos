<?php
//including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
//getting id of the data from url
$id = $funciones->escape_string($_GET['codigo']);
 
//deleting the row from table
//$result = $funciones->execute("DELETE FROM usuarios WHERE codigo=$id");
$result = $funciones->delete($id, 'modalidad');
 
if ($result) {
    //redirecting to the display page (index.php in our case)
    header("Location:index.php");
		echo "<div class=\"alert alert-danger\" role=\"alert\">  <strong>Logrado!</strong> Se ingreso un registro al sistema.</div>";
}
?>