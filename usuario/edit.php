<?php
// including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
//getting id from url
$id = $funciones->escape_string($_GET['codigo']);
 
//selecting data associated with this particular id
$result = $funciones->getData("SELECT * FROM usuarios WHERE codigo=$id");
 
foreach ($result as $res) {
    $nombre = $res['nombre'];
	$clave = $res['clave'];
	$estado  = $res['estado'];
	$codigoMD5  = $res['codigoMD5'];
}
?>
<html>
<head>    
    <title>Edit Data</title>
</head>
 
<body>
    <a href="index.php">Inicio</a>
    <br/><br/>
    <form name="form1" method="post" action="edictation.php">
<fieldset>
    <legend>USUARIO</legend>
<!-- ELEMENTO INPUT -->
  Nombre:
  <br>
  <input type="text" name="txtNombre" value="<?php echo $nombre;?>">
  <br>
  Clave:
  <br>
  <input type="text" name="txtClave" value="<?php echo $clave;?>">
  <br>
  Seleccione estado:
  <br>
  <input type="radio" name="rbestado" value="<?php echo $estado;?>"> Disponible
  <br>
  <input type="radio" name="rbestado" value="<?php echo $estado;?>" checked> No Disponible
  <br>
  Codigo MD5:
  <br>
  <input type="text" name="txtMD5" value="<?php echo $codigoMD5;?>">
  <br>
  <td><input type="hidden" name="id" value=<?php echo $_GET['codigo'];?>></td>
                <td><input type="submit" name="update" value="Update"></td>
  </fieldset>
</form> 
   
</body>
</html>