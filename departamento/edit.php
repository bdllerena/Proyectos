<?php
// including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
//getting id from url
$id = $funciones->escape_string($_GET['codigo']);
 
//selecting data associated with this particular id
$result = $funciones->getData("SELECT * FROM departamento WHERE codigo=$id");
 
foreach ($result as $res) {
    $nombre = $res['nombre'];
}
?>
<html>
<head>    
    <title>Edit Departamento</title>
</head>
 
<body>
    <a href="index.php">Inicio</a>
    <br/><br/>
    <form name="form1" method="post" action="edictation.php">
<fieldset>
    <legend>DEPARTAMENTO</legend>
<!-- ELEMENTO INPUT -->
  Nombre:
  <br>
  <input type="text" name="txtNombre" value="<?php echo $nombre;?>">
  <td><input type="hidden" name="id" value=<?php echo $_GET['codigo'];?>></td>
                <td><input type="submit" name="update" value="Update"></td>
  </fieldset>
</form> 
   
</body>
</html>