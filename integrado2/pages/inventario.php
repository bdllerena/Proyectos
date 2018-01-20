<?php
session_start();
if($_SESSION['nivel'] >= 3){
echo "Usuari@ " . $_SESSION['nombre'] . "</br>";
echo "Aquí va toda la información de <b>Inventario</b> </br>";
echo '<a style="color: #000000;  font-size: 20px" href="../index.php?menu=true">Regresar al Menú.</a>';
}else{
	echo "Usted no tiene permisos para acceder a este sitio";
}
?>
