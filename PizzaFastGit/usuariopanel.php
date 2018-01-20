<?php
   include('session.php');
?>
<html>

   <head>
      <title>Usuario Panel </title>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
       <link rel="stylesheet" href="style.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
   </head>

   <body>
      <h1>Bienvenido <?php echo $login_session; ?></h1>
      <h2><a href = "logout.php"><button class="btn btn-danger">Salir</button></a></h2>
      <h2>Realizar Pedido</h2>
      <label>Código Cliente</label>
      <input type="number" class="form-control" id="clientVal">
      <br>
      <br>
      <label>Código pizza</label>
      <input type="number" class="form-control" id="pizzaVal">
      <br>
      <label>Cantidad</label>
      <input type="number" class="form-control" id="cantidadVal">
      <input type="submit" value="submit" class="btn btn-danger" id="clientSearch">
      <h2>Verificar Pedido</h2>
      <table class="table">
        <tbody id="realizarpedido"></tbody>
      </table>
   </body>
   <script>
   $(document).ready(function(){
     $("#clientSearch").click(function(){
       var x = document.getElementById("clientVal").value;
       var y = document.getElementById("pizzaVal").value;
       var z = document.getElementById("cantidadVal").value;
    /*   var x = document.getElementById("clientVal").value;
       var y = document.getElementById("pizzaVal").value;
       var z = document.getElementById("cantidadVal").value;*/
       $.get("http://ec2-54-201-234-64.us-west-2.compute.amazonaws.com:8080/View_pizzeria/webresources/Pedido/"+x+"/"+y+"/"+z,function(data) {
         $("#realizarpedido").append("<td>"+data.Pedido+"</td><td>"+data.pizza+"</td><td>"+data.cantidad+"</td><td>"+data.precio+"</td>");
       },"json");
     });
   });

   </script>
</html>
