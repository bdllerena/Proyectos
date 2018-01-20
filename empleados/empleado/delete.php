<?php
include_once("../include/funciones.php");

$funciones = new funciones();

$id = $funciones->escape_string($_GET['id_emp']);

$result = $funciones->deleteEmpleado($id, 'empleado');

if ($result) {
    header("Location:empleado.php");
		//echo "<div class=\"alert alert-danger\" role=\"alert\">  <strong>Logrado!</strong> Se ingreso un registro al sistema.</div>";
}
?>
