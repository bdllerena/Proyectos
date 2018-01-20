<!DOCTYPE html>
<html>
	<title>Datatable Demo1 | CoderExample</title>
	<head>
		<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
		<link rel="stylesheet" type="text/css" href="style.css">
		<title>MissionPetroleum | Filtros</title>
		<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="js/jquery.dataTables.js"></script>
		<script type="text/javascript" language="javascript">
			$(document).ready(function() {
				var dataTable = $('#employee-grid').DataTable( {
					"processing": true,
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
					"serverSide": true,
					"ajax":{
						url :"employee-grid-data.php", // json datasource
						type: "post",  // method  , by default get
						error: function(){  // error handling
							$(".employee-grid-error").html("");
							$("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
							$("#employee-grid_processing").css("display","none");
							
						}
					}
				} );
			} );

		</script>
    </script>
			
		<!--<script>
		$(document).ready(function() {
				$('#employee-grid').DataTable( {
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
				} );
			} );
		</script>-->
	<style>
	
			<!--body {
			    background: #f7f7f7;
			    color: #333;
			    font: 90%/1.45em "Helvetica Neue",HelveticaNeue,Verdana,Arial,Helvetica,sans-serif;
			}-->
		</style>
	</head>
	<body>
		<center><img src="logo.png"/></center>
		<div class="header"></div>
		
			<table id="employee-grid"  cellpadding="0" cellspacing="0" border="0" class="display" width="100%">
					<thead>
						<tr>
							<th>Factura</th>
							<th>Descripción</th>
							<th>Estado</th>
						</tr>
					</thead>
			</table>
	<!--<th>FechaCronograma</th>
							<th>Drive</th>-->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

	</body>
</html>
