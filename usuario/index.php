<?php
//including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
//fetching data in descending order (lastest entry first)
$query = "SELECT * FROM usuarios ORDER BY codigo DESC";
$result = $funciones->getData($query);
//echo '<pre>'; print_r($result); exit;
?>
 
<html>
<head>    
    <title>Usuarios</title>
</head>
 
<body>
<a href="add.html">Añadir usuario</a><br/><br/>
 
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
        echo "<td><a href=\"edit.php?codigo=$res[codigo]\">Modificar</a> | <a href=\"delete.php?codigo=$res[codigo]\" onClick=\"return confirm('Esta seguro que desea borrar?')\">Borrar</a></td>";        
    }
    ?>
    </table>
<form name="form2" method="post" action="search.php">
  <p align="center" class="style1">USUARIOS</p>
  <p align="center"><strong>BUSQUEDAS PERSONALIZADAS</strong></p>
  <div align="center">
    <table width="779" border="1">
      <tr>
        <td width="196">&nbsp;</td>
        <td width="196"><label></label></td>
        <td width="365">&nbsp;</td>
      </tr>
      <tr>
        <td rowspan="5"><img src="IMG/2.jpg" width="111" height="124"></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>Seleccione su consulta </td>
        <td><select name="tipobusqueda" id="tipobusqueda">
          <option value="codigo">Codigo</option>
          <option value="clave">Clave</option>
          <option value="codigoMD5">CodigoMd5</option>
                        </select></td>
      </tr>
      <tr>
        <td>Digite el valor buscado </td>
        <td><input name="terminobusqueda" type="text" id="terminobusqueda"></td>
      </tr>
      <tr>
       <td><input name="btnEnviar" type="submit" id="btnEnviar" value="Enviar"></td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </table>
  </div>
  <p align="center">&nbsp;</p>
  </p>
</form>
</body>
</html>