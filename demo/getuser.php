<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <!--<link rel="stylesheet" href="style.css">-->
<!--
<style>
/*
table {
    
}

table, td, th {
  
}
*/
.tables td,th{
	margin: left;
	width: 100%;
    border-collapse: collapse;
	border: 1px solid black;
    padding: 5px;
	text-align: left;
	    font-size: 50%
	}
</style>-->
</head>
<body>

<?php
$q = intval($_GET['q']);

$con = mysqli_connect('localhost','root','','missionw');
if (!$con) {
    die('Could not connect: ' . mysqli_error($con));
}

$sql="SELECT destino,salidaBodegaFecha,fechaFacturacion,fechaCronograma FROM facturacions WHERE frt = '".$q."'";
$result = mysqli_query($con, $sql);
$sql2="SELECT diasRetraso,valorMulta,valorPagar FROM facturacions WHERE frt = '".$q."'";
$result2 = mysqli_query($con, $sql2);
if (mysqli_num_rows($result) > 0) {
echo "<table class=\"table table-resultado\">
<tr class=\"info\">
<th>DESTINO</th>
<th>SALIDA DE BODEGA MISSION</th>
<th>FECHA_FACTURACION</th>
<th>FECHA CRONOGRAMA</th>
</tr>";
    while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['destino'] . "</td>";
	echo "<td>" . $row['salidaBodegaFecha'] . "</td>";
    echo "<td>" . $row['fechaFacturacion'] . "</td>";
	echo "<td>" . $row['fechaCronograma'] . "</td>";
    echo "</tr>";   
	}
	echo "</table>";
} else {
    echo "0 results";
}
if (mysqli_num_rows($result2) > 0) {
echo "<table class=\"table table-resultado\">
<tr class=\"info\">
<th>Dias Retraso</th>
<th>Valor Multa</th>
<th>Valor Pagar</th>
</tr>";
    while($row = mysqli_fetch_assoc($result2)) {
    echo "<tr>";
    echo "<td>" . $row['diasRetraso'] . "</td>";
    echo "<td>" . $row['valorMulta'] . "</td>";
    echo "<td>" . $row['valorPagar'] . "</td>";
    echo "</tr>";   
	}
	echo "</table>";
} else {
    echo "0 results";
}
$sql3="SELECT SUM(`valorPagar`) as sumatoria, COUNT(*) as registros, estado FROM `facturacions` where estado = 'Multa Sin Cronograma'";
$result3 = mysqli_query($con, $sql3);
if (mysqli_num_rows($result3) > 0) {
    while($row = mysqli_fetch_assoc($result3)) {
		$sum = $row['sumatoria'];
	echo "<strong><center><p>Total valor a pagar: $".$sum." de: ".$row['registros']." registros con el estado: ".$row['estado']."</p></center></strong>";  
	}
} else {
    echo "0 results";
}
/*
if (mysqli_num_rows($result) > 0) {
    // output data of each row
	/*echo "<table class=\"table table-data\">*/
	/*echo "<table class=\"tables\">
<tr>
<th>FRT</th>
<th>CLIENTE</th>
<th>FRT.FECHA</th>
<th>DESCRIPCION DEL SERVICIO</th>
<th>DESTINO</th>
<th>ORDEN_VALOR</th>
<th>SumOfSubtotal</th>
<th>EVENTO</th>
<th>COD_SB</th>
<th>SALIDA DE BODEGA MISSION</th>
<th>FACTURA</th>
<th>FECHA_FACTURACION</th>
<th>SUB TOTAL</th>
<th>MULTAS Y DSCTOS</th>
<th>BASE IMPONIBLE</th>
<th>IVA 0%</th>
<th>IVA 12%</th>
<th>TOTAL</th>
<th>RET 2%</th>
<th>RET 1%</th>
<th>TOTAL A COBRAR</th>
<th>VALOR COBRADO</th>
<th>SALDO</th>
<th>FECHA DE COBRO</th>
<th>FECHA DE ENTREGA A PAM - PETROECUADOR</th>
<th>FECHA CRONOGRAMA</th>
<th>Dias Retraso</th>
<th>Valor Multa</th>
<th>Valor Pagar</th>
</tr>";
    while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
	echo "<td>" . $row['frt'] . "</td>";
	echo "<td>" . $row['cliente'] . "</td>";
    echo "<td>" . $row['frtFecha'] . "</td>";
    echo "<td>" . $row['descServicio'] . "</td>";
    echo "<td>" . $row['destino'] . "</td>";
	echo "<td>" . $row['ordenValor'] . "</td>";
    echo "<td>" . $row['sumOfSubtotal'] . "</td>";
    echo "<td>" . $row['evento'] . "</td>";
	echo "<td>" . $row['codSB'] . "</td>";
	echo "<td>" . $row['salidaBodegaFecha'] . "</td>";
    echo "<td>" . $row['factura'] . "</td>";
    echo "<td>" . $row['fechaFactura'] . "</td>";
    echo "<td>" . $row['subTotal'] . "</td>";
	echo "<td>" . $row['multasDescuentos'] . "</td>";
    echo "<td>" . $row['baseImponible'] . "</td>";
    echo "<td>" . $row['iva0'] . "</td>";
	echo "<td>" . $row['iva12'] . "</td>";
	echo "<td>" . $row['total'] . "</td>";
    echo "<td>" . $row['ret2'] . "</td>";
    echo "<td>" . $row['ret1'] . "</td>";
    echo "<td>" . $row['totalACobrar'] . "</td>";
	echo "<td>" . $row['valorCobrado'] . "</td>";
    echo "<td>" . $row['saldo'] . "</td>";
    echo "<td>" . $row['fechaCobro'] . "</td>";
	echo "<td>" . $row['fechaEntrega'] . "</td>";
	echo "<td>" . $row['fechaCronograma'] . "</td>";
    echo "<td>" . $row['diasRetraso'] . "</td>";
    echo "<td>" . $row['valorMulta'] . "</td>";
    echo "<td>" . $row['valorPagar'] . "</td>";
    echo "</tr>";   
	}
	echo "</table>";
} else {
    echo "0 results";
}*/
mysqli_close($con);
?>
</body>
</html>