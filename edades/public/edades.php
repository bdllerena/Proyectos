<?php
//including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
//fetching data in descending order (lastest entry first)
$query = "SELECT * FROM edad";
$result = $funciones->getData($query);
//echo '<pre>'; print_r($result); exit;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
   <link rel="stylesheet" href="style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script>
  $.ajax({
	  
  });
  $(document).ready(function(){
    $('#buttonclick').click(function(){
		$.get( "http://localhost/edades/public/index.php/api/edades/promedio", function( data ) {
		  $( "body" )
			.append( "<center>Edad Promedio de los registros: " + parseFloat(data.edadPromedio).toFixed(2) +"</center>" ) // John
		}, "json" );
	  /* $.get("http://localhost/edades/public/index.php/api/edades/promedio", function(data) {		
	    //alert("Valor obtenido del servicio:" + data);
		$( ".result" ).html(data);
	}).fail(function() {
	  alert("An error has occurred");
	});*/
	});
  });
</script>
</head>
<body>
<!-- <form>
 <label>Ingreso de datos</label>
  <p></p>
 <label>Nombre:</label>
 <input type="text">
 <p></p>
 <label>Edad:</label>
 <input type="text">
  <p></p>
  <p></p>
 <input type="submit" value="enviar">
 </form>-->
     <table width='80%' border=0 class="table table-dark">
    <tr bgcolor='#CCCCCC'>
		<td>id</td>
        <td>nombre</td>
		<td>edad</td>
    </tr>
	
    <?php 
    foreach ($result as $key => $res) 
		{     
        echo "<tr>";
		echo "<td>".$res['id']."</td>";	
        echo "<td>".$res['nombre']."</td>";	
		echo "<td>".$res['edad']."</td>";	
        }
    ?>
    </table>
	<center>
	<button onclick="doFunction();" id="buttonclick" class="btn btn-info">Obtener promedio</button>
	</center>
	<p></p>
	<div class="result"></div>
</body>
</html>