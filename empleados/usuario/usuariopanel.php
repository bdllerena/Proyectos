<?php
   include('session.php');
//including the database connection file
include_once("../include/funciones.php");

  $funciones = new funciones();

  //fetching data in descending order (lastest entry first)
  $query = "SELECT * FROM usuario ORDER BY id_usu DESC";
  $result = $funciones->getData($query);
  //echo '<pre>'; print_r($result); exit;
?>
<html>

   <head>
      <title>Usuario Panel </title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <style>
      #outer
      {
          width:100%;
          text-align: center;
      }
      .inner
      {
          display: inline-block;
      }
      </style>
   </head>

  <body>
  <h1>Bienvenido <?php echo $login_session; ?></h1>
  <div id="outer">
    <div class="inner"><a href="add.html"><button class="btn btn-info" autofocus>Añadir Usuario</button></a></div>
    <div class="inner"><a href = "logout.php"><button class="btn btn-warning">Salir</button></a></div>
  </div>
    <table width='80%' border=0 class="table table-dark">
    <tr bgcolor='#CCCCCC'>
		<td>Codigo</td>
    <td>Nombre</td>
		<td>Cedula</td>
    <td>Genero</td>
    <td>Acciones</td>
    </tr>
    <?php
    foreach ($result as $key => $res) {
    //while($res = mysqli_fetch_array($result)) {
        echo "<tr>";
	    	echo "<td>".$res['id_usu']."</td>";
        echo "<td>".$res['nombre_usu']."</td>";
        echo "<td>".$res['cedula_usu']."</td>";
        echo "<td>".$res['genero_usu']."</td>";
      //  echo "</tr>";
        echo "<td>|<a href=\"edit.php?id_usu=$res[id_usu]\"><button class=\"btn btn-primary\">Modificar</button></a> | <a href=\"delete.php?id_usu=$res[id_usu]\" onClick=\"return confirm('Esta seguro que desea borrar?')\"><button class=\"btn btn-danger\">Borrar</button></a>| </td>";
    }
    ?>
    </table>
  </body>
</html>