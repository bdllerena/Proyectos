<?php
// including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
//getting id from url
$id = $funciones->escape_string($_GET['codigo']);
 
//selecting data associated with this particular id
$result = $funciones->getData("SELECT * FROM modalidad WHERE codigo=$id");
 
foreach ($result as $res) {
    $nombre = $res['tipo'];
}
?>
<html>
<head>    
    <title>Edit Departamento</title>
		        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
 
<body>
    <a href="index.php">Inicio</a>
    <br/><br/>
    <form name="form1" method="post" action="edictation.php">
<fieldset>
    <legend>Modalidad</legend>
<!-- ELEMENTO INPUT -->
  Tipo:
  <br>
  <input type="text" name="txtNombre" value="<?php echo $nombre;?>">
  <td><input type="hidden" name="id" value=<?php echo $_GET['codigo'];?>></td>
                <td><input type="submit" name="update" value="Update"></td>
  </fieldset>
</form> 
 <script src="http://code.jquery.com/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
</body>
</html>