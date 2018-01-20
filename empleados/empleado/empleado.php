<?php
   include('session.php');
//including the database connection file
include_once("../include/funciones.php");

  $funciones = new funciones();

  //fetching data in descending order (lastest entry first)
  $query = "SELECT * FROM empleado ORDER BY id_emp DESC";
  $result = $funciones->getData($query);
  //echo '<pre>'; print_r($result); exit;
?>
<html>

   <head>
      <title>Empleado Panel </title>
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
    <div class="inner"><a href = "logout.php"><button class="btn btn-warning">Salir</button></a></div>
  </div>
    <table width='80%' border=0 class="table table-dark">
    <tr bgcolor='#CCCCCC'>
		<td>Codigo Empleado</td>
    <td>Codigo Usuario</td>
		<td>Nombre Empleado</td>
    <td>Ruc Empleado</td>
    <td>Acciones</td>
    </tr>
    <?php
    foreach ($result as $key => $res) {
    //while($res = mysqli_fetch_array($result)) {
        echo "<tr>";
	    	echo "<td>".$res['id_emp']."</td>";
        echo "<td>".$res['id_usu']."</td>";
        echo "<td>".$res['nombre_emp']."</td>";
        echo "<td>".$res['ruc_emp']."</td>";
      //  echo "</tr>";
        echo "<td>|<a href=\"edit.php?id_emp=$res[id_emp]&id_usu=$res[id_usu]\"><button class=\"btn btn-primary\">Modificar</button></a> | <a href=\"delete.php?id_emp=$res[id_emp]\" onClick=\"return confirm('Esta seguro que desea borrar?')\"><button class=\"btn btn-danger\">Borrar</button></a>| </td>";
    }
    ?>
    </table>
  </body>
</html>
