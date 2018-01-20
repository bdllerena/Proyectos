<?php
// including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
//getting id from url
$id = $funciones->escape_string($_GET['id']);
 
//selecting data associated with this particular id
$result = $funciones->getData("SELECT * FROM product WHERE id=$id");
 
foreach ($result as $res) {
    $nombre = $res['stock'];
}
?>
<html>
<head>    
    <title>Editar Producto</title>
		        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
 
<body>
    <a href="index.php">Inicio</a>
    <br/><br/>
    <form name="form1" method="post" action="edictation.php">
<fieldset>
    <legend>Producto</legend>
<!-- ELEMENTO INPUT -->
  Stock:
  <br>
  <input type="text" name="txtStock" value="<?php echo $nombre;?>">
  <td><input type="hidden" name="id" value=<?php echo $_GET['id'];?>></td>
                <td><input type="submit" name="update" value="Update"></td>
  </fieldset>
</form> 
 <script src="http://code.jquery.com/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
</body>
</html>