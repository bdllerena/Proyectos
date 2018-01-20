<?php
   include('session.php');
   include_once("include/funciones.php");
    $funciones = new funciones();

    //fetching data in descending order (lastest entry first)
    $query = "SELECT * FROM usuario ORDER BY id_usuario DESC";
    $result = $funciones->getData($query);
    //echo '<pre>'; print_r($result); exit;
?>
<html>

   <head>
      <title>Admin Panel </title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
       <link rel="stylesheet" href="style.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
      <script>
      $(document).ready(function()
      {
        $.get("http://ec2-54-212-247-249.us-west-2.compute.amazonaws.com:8080/Proyecto_pizza/webresources/Client",function(data) {
          $.each(data, function(index,el) {
            $("#clientesTotal").append("<tr><td>"+el.ID+"</td><td>"+el.Nombre+"</td><td>"+el.Apellido+"</td><td>"+el.Fecha_de_nacimiento+"</td></tr>");
            });
          },"json");
        /*$.get("http://ec2-54-212-247-249.us-west-2.compute.amazonaws.com:8080/Proyecto_pizza/webresources/Client",function(data) {
            $("#clientesTotal").append("<tr><td>"+data.ID+"</td><td>"+data.Nombre.ID+"</td><td>"+data.Apellido+"</td><td>"+data.Fecha_de_nacimiento+"</td></tr>");
          },"json");*/
      });
      </script>
    <!--  //http://ec2-54-245-141-110.us-west-2.compute.amazonaws.com/pizzafast/public/index.php/repartidor-->
      <script>
      $(document).ready(function()
      {
        $.get("http://ec2-54-245-141-110.us-west-2.compute.amazonaws.com/pizzafast/public/index.php/repartidor",function(data) {
          $.each(data, function(index,el) {
            $("#repartidores").append("<tr><td>"+el.id_repartidor+"</td><td>"+el.nickname+"</td><td>"+el.cedula+"</td><td>"+el.sueldo+"</td><td>"+el.empleadoMes+"</td></tr>");
            });
          },"json");
        /*$.get("http://ec2-54-212-247-249.us-west-2.compute.amazonaws.com:8080/Proyecto_pizza/webresources/Client",function(data) {
            $("#clientesTotal").append("<tr><td>"+data.ID+"</td><td>"+data.Nombre.ID+"</td><td>"+data.Apellido+"</td><td>"+data.Fecha_de_nacimiento+"</td></tr>");
          },"json");*/
      });
      </script>

   </head>

   <body>
    <h1>Bienvenido <?php echo $login_session; ?></h1><h2><a href = "logout.php"><button class="btn btn-danger">Salir</button></a></h2>
    <center><h2>Usuarios</h2></center>
    <table width='80%' border=0 class="table table-dark">
   <tr bgcolor='#CCCCCC'>
   <td>id</td>
   <td>nombre</td>
   <td>apellido</td>
   <td>cedula</td>
   <td>nickname</td>
   <td>latitud</td>
   <td>longitud</td>
   </tr>
   <?php
   foreach ($result as $key => $res)
   {
   //while($res = mysqli_fetch_array($result)) {
       echo "<tr>";
       echo "<td>".$res['id_usuario']."</td>";
       echo "<td>".$res['nombre']."</td>";
       echo "<td>".$res['apellido']."</td>";
       echo "<td>".$res['cedula']."</td>";
       echo "<td>".$res['nickname']."</td>";
       echo "<td>".$res['latitud']."</td>";
       echo "<td>".$res['longitud']."</td>";
    }
   ?>
   </table>
   <center><h2>Facturas</h2></center>
   <input type="number" id="facturaVal" placeholder="Ingrese id factura">
   <button class="btn btn-info" id="facturae">Buscar factura</button>
   <table class="table table-striped">
     <thead>
       <tr>
         <th>ID_PEDIDO</th>
         <th>ID_PERSONA</th>
         <th>NOMBRE</th>
         <th>APELLIDO</th>
         <th>TOTAL_PEDIDO</th>
         <th>DESCUENTO</th>
         <th>CALCULO_IVA</th>
         <th>PRECIO_TOTAL</th>
       </tr>
     </thead>
     <tbody id="facturas">
     </tbody>
   </table>
   <center><h2>Clientes</h2></center>
   <table class="table table-striped">
     <thead>
       <tr>
         <th>ID_CLIENTE</th>
         <th>NOMBRE</th>
         <th>APELLIDO</th>
         <th>FECHANACIMIENTO</th>
       </tr>
     </thead>
     <tbody id="clientesTotal">
     </tbody>
   </table>
   <center><h2>Clientes Por ID</h2></center>
   <input type="number" id="clientVal" placeholder="Ingrese id">
   <button class="btn btn-info" id="clientSearch">Buscar cliente</button>
   <table class="table table-striped">
     <thead>
       <tr>
         <th>ID_CLIENTE</th>
         <th>NOMBRE</th>
         <th>APELLIDO</th>
         <th>FECHANACIMIENTO</th>
       </tr>
     </thead>
     <tbody id="clientes">
     </tbody>
   </table>
   <center><h2>Repartidores</h2></center>
   <table class="table table-striped">
     <thead>
       <tr>
         <th>ID_REPARTIDOR</th>
         <th>NICKNAME</th>
         <th>CEDULA</th>
         <th>SUELDO</th>
         <th>EMPLEADOMES</th>
       </tr>
     </thead>
     <tbody id="repartidores">
     </tbody>
   </table>
   <center><h2>Ingreso Repartidores</h2>
   <form id="testform">
     <label>nickname</label>
     <input type="text" class="form-control" name="nickname">
     <br>
     <label>cedula</label>
     <input type="text" class="form-control" name="cedula">
     <br>
     <input type="submit" value="submit" class="btn btn-warning" id="post-btn">
   </form>
   <form id="updateform" action="#">
     <label>IdRepartidor</label>
     <input type="number" id="repartidorVal" placeholder="Ingrese id">
     <br>
     <label>EmpleadoMes</label>
     <input type="text" class="form-control" name="empleadoMes">
     <br>
     <input type="submit" value="submit" class="btn btn-warning" id="update-btn">
   </form>
   </center>
   </body>
   <script>
   $(document).ready(function()
   {
     $("#facturae").click(function(){
             var x = document.getElementById("facturaVal").value;
             var url= "http://ec2-34-210-29-111.us-west-2.compute.amazonaws.com:8080/BusinessLogic_Pizza/factura/"+x;
         $.get(url,function(data) {
             $("#facturas").append("<tr><td>"+data.pedido.id_pedido+"</td><td>"+data.pedido.persona.ID+"</td><td>"+data.pedido.persona.Nombre+"</td><td>"+data.pedido.persona.Apellido+"</td><td>"+data.pedido.total_pedido+"</td><td>"+data.pedido.descuento+"</td><td>"+data.calculo_iva+"</td><td>"+data.precio_total_pagar+"</td></tr>");
           },"json");
       });
   });
   </script>
   <script>
   $(document).ready(function(){
     $("#clientSearch").click(function(){
       var x = document.getElementById("clientVal").value;
       $.get("http://ec2-54-212-247-249.us-west-2.compute.amazonaws.com:8080/Proyecto_pizza/webresources/Client/"+x,function(data) {
         $("#clientes").append("<td>"+data.ID+"</td><td>"+data.Nombre+"</td><td>"+data.Apellido+"</td><td>"+data.Fecha_de_nacimiento+"</td>");
       },"json");
     });
   });
   </script>
   <script>
   $(document).ready(function()
   {
     $("#post-btn").click(function()
     {
       $.post("http://ec2-54-245-141-110.us-west-2.compute.amazonaws.com/pizzafast/public/index.php/repartidor", $("#testform").serialize(), function(data) {
       event.preventDefault();
      });
     });
   });
   </script>
   <script>
   $(document).ready(function()
   {
     $("#update-btn").click(function()
     {
       //var myData = new FormData();
        //myData.append("key", "value");
        //var DataToSend = new Object();
        //DataToSend = $("#updateform");

        //Call jQuery ajax
        $.ajax({
          url: 'http://ec2-54-245-141-110.us-west-2.compute.amazonaws.com/pizzafast/public/index.php/repartidor/2',
          type: 'put',
          data: $("#updateform").serialize(),
          headers: {
              'x-auth-token': localStorage.accessToken,
              "Content-Type": "application/json"
          },
          dataType: 'json'
        })
        /*$.ajax({
        	url: 'http://ec2-54-245-141-110.us-west-2.compute.amazonaws.com/pizzafast/public/index.php/repartidor/2',
        	type: "PUT",
          data: $("#updateform"),
        	//data: $("#updateform").serialize(),
        	crossDomain: true,
        	//contentType: "multipart/form-data",
        	processData: false,
          dataType: "json",
          headers: {
            'x-auth-token': localStorage.accessToken,
            "Content-Type": "application/json"
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
              console.log(textStatus);
              console.log(errorThrown);
           }
        });*/
      /* var x = document.getElementById("repartidorVal").value;
       var url= "http://ec2-54-245-141-110.us-west-2.compute.amazonaws.com/pizzafast/public/index.php/repartidor/2";
        $.ajax({
           url: url,
           type: 'PUT',
           dataType: 'jsonp',
           data: $("#updateform").serialize(),
           success: function(response) {
             //...
           }
        });*/
    });
     /*var x = document.getElementById("repartidorVal").value;
     $("#update-btn").click(function()
     {

       $.post("http://ec2-54-245-141-110.us-west-2.compute.amazonaws.com/pizzafast/public/index.php/repartidor"+x, $("#updateform").serialize(), function(data) {
       event.preventDefault();
      });
    });*/
   });
   </script>
</html>
