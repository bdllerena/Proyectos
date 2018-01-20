<?php
include_once("../include/funciones.php");

$funciones = new funciones();

$id = $funciones->escape_string($_GET['id_usu']);

$result = $funciones->delete($id, 'usuario');

if ($result) {
    header("Location:usuariopanel.php");
		//echo "<div class=\"alert alert-danger\" role=\"alert\">  <strong>Logrado!</strong> Se ingreso un registro al sistema.</div>";
}
?>
