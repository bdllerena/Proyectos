<?php
//including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
//fetching data in descending order (lastest entry first)
$query = "SELECT * FROM modalidad ORDER BY codigo DESC";
$result = $funciones->getData($query);
//echo '<pre>'; print_r($result); exit;
?>
 
<html>
<head>    
    <title>Modalidad</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
   <link rel="stylesheet" href="style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
          <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
 
<body>

   <div id="particles-js">
        <div class="btext">
<!--<a href="add.html">Añadir Modalidad</a><br/><br/>-->
 
    <table width='80%' border=0 class="table table-dark">
    <tr bgcolor='#CCCCCC'>
		<td>Codigo</td>
        <td>Tipo</td>
		<td>Acciones</td>
    </tr>
    <?php 
    foreach ($result as $key => $res) {
    //while($res = mysqli_fetch_array($result)) {         
        echo "<tr>";
		echo "<td>".$res['codigo']."</td>";	
        echo "<td>".$res['tipo']."</td>";	
        echo "<td><a href=\"add.html\"><button class=\"btn btn-info\" autofocus>Añadir Modalidad</button></a>|<a href=\"edit.php?codigo=$res[codigo]\"><button class=\"btn btn-warning\">Modificar</button></a> | <a href=\"delete.php?codigo=$res[codigo]\" onClick=\"return confirm('Esta seguro que desea borrar?')\"><button class=\"btn btn-danger\">Borrar</button></a>| </td>";        
    }
    ?>
    </table>
</div>

</div>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

    <script>
        particlesJS.load('particles-js', 'particles.json',
        function(){
            console.log('particles.json loaded...')
        })
    </script>
	<script src="http://code.jquery.com/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
</body>
</html>