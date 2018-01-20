<?php
//including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
	$buscar = $funciones->escape_string($_POST['tipobusqueda']);
	$valor = $funciones->escape_string($_POST['terminobusqueda']);
	//fetching data in descending order (lastest entry first)
	$query = "SELECT * FROM usuarios WHERE $buscar=$valor";
	$result = $funciones->getData($query);
	//echo '<pre>'; print_r($result); exit;



?>
<html>
<head>
    <title>Search Data</title>
</head>
 
<body>
    <table width='80%' border=0>
 
    <tr bgcolor='#CCCCCC'>
        <td>Nombre</td>
        <td>Clave</td>
        <td>Estado</td>
        <td>codigoMD5</td>
    </tr>
    <?php 
    foreach ($result as $key => $res) {
    //while($res = mysqli_fetch_array($result)) {         
        echo "<tr>";
        echo "<td>".$res['nombre']."</td>";
        echo "<td>".$res['clave']."</td>";
        echo "<td>".$res['estado']."</td>";  
		echo "<td>".$res['codigoMD5']."</td>"; 		       
    }
    ?>
    </table>
</body>
</html>