<?php
include_once("../include/funciones.php");

$funciones = new funciones();

$id = $funciones->escape_string($_GET['id_usu']);
$ide = $funciones->escape_string($_GET['id_emp']);
$result = $funciones->getData("SELECT * FROM empleado WHERE id_usu=$id AND id_emp=$ide");

foreach ($result as $res) {
    $nombre = $res['nombre_emp'];
    $ruc = $res['ruc_emp'];
}
?>
<html>
<head>
    <title>Editar Empleado</title>
    <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
  <form action="edictation.php" method="POST" name="form1">
	<div class="form-group">
	<label for="first_name">Nombre</label>
	<input type="text" class="form-control" name="txtNombre" value="<?php echo $nombre;?>">
	</div>
	<div class="form-group">
	<label for="last_name">Ruc</label>
	<input type="text" class="form-control" name="txtRuc" value="<?php echo $ruc;?>">
	</div>
  <input type="hidden" name="id_usu" value=<?php echo $_GET['id_usu'];?>>
  <input type="hidden" name="id_emp" value=<?php echo $_GET['id_emp'];?>>
	<input type="submit" class="btn btn-success" name="update" value="Update">

</form>
<a href="empleado.php"><button class="btn btn-warning">Regresar</button></a>
</body>
</html>
