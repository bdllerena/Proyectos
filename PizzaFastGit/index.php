<?php
   include("include/Dbconfig.php");
   session_start();
   $error = "Bienvenido";
   if($_SERVER["REQUEST_METHOD"] == "POST") {

      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']);

      $sql = "SELECT id_usuario FROM usuario WHERE nickname = '$myusername' and clave = '$mypassword'";
      $result = mysqli_query($db,$sql);
      //$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      /*$active = $row['active'];*/

      $count = mysqli_num_rows($result);

      // If result matched $myusername and $mypassword, table row must be 1 row

      if($count == 1) {
          if($myusername == 'admin')
          {
            $_SESSION['login_user'] = $myusername;
            header("location: adminpanel.php");
          }
          else
          {
            $_SESSION['login_user'] = $myusername;

            header("location: usuariopanel.php");
          }

      }else {
         $error = "Tu nickname o clave es invalida";
      }
   }
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- PAGE settings -->
  <link rel="icon" href="https://templates.pingendo.com/assets/Pingendo_favicon.ico">
  <title>FastPizza</title>
  <meta name="description" content="Free Bootstrap 4 Pingendo Elegant template for restaurant and food">
  <meta name="keywords" content="Pingendo restaurant food elegant free template bootstrap 4">
  <!-- CSS dependencies -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="theme.css" type="text/css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Script: Make my navbar transparent when the document is scrolled to top -->
  <script src="js/navbar-ontop.js"></script>
  <!-- Script: Animated entrance -->
  <script src="js/animate-in.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <style>
/* Full-width input fields */
input[type=text], input[type=password] {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

/* Set a style for all buttons */
button {
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}

button:hover {
    opacity: 0.8;
}

/* Extra styles for the cancel button */
.cancelbtn {
    width: auto;
    padding: 10px 18px;
    background-color: #f44336;
}

/* Center the image and position the close button */
.imgcontainer {
    text-align: center;
    margin: 24px 0 12px 0;
    position: relative;
}

img.avatar {
    width: 40%;
    border-radius: 50%;
}

.container {
    padding: 16px;
}

span.psw {
    float: right;
    padding-top: 16px;
}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    padding-top: 60px;
    overflow: hidden;
}
.modal-open {
  overflow-y: scroll;
}
/* Modal Content/Box */
.modal-content {
    background-color: #fefefe;
    margin: 5% auto 15% auto; /* 5% from the top, 15% from the bottom and centered */
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

/* The Close Button (x) */
.close {
    position: absolute;
    right: 25px;
    top: 0;
    color: #000;
    font-size: 35px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: red;
    cursor: pointer;
}

/* Add Zoom Animation */
.animate {
    -webkit-animation: animatezoom 0.6s;
    animation: animatezoom 0.6s
}

@-webkit-keyframes animatezoom {
    from {-webkit-transform: scale(0)}
    to {-webkit-transform: scale(1)}
}

@keyframes animatezoom {
    from {transform: scale(0)}
    to {transform: scale(1)}
}

/* Change styles for span and cancel button on extra small screens */
@media screen and (max-width: 300px) {
    span.psw {
       display: block;
       float: none;
    }
    .cancelbtn {
       width: 100%;
    }
}
</style>
  <script>
      $.getJSON("http://quotesondesign.com/wp-json/posts?filter[orderby]=rand&filter[posts_per_page]=1&callback=", function(a) {
      $("#quote").append(a[0].content + "<p>— " + a[0].title + "</p>")
    });
  </script>
  <script type="text/javascript">
  $(document).ready(function(){
    $.ajax({
        url: 'http://ec2-54-201-234-64.us-west-2.compute.amazonaws.com:8080/BusinessLogic_Pizza/pizzas',
        dataType: 'json',
        success: function (outputfromserver) {
          $.each(outputfromserver, function(index, el) {
            $("#pizzas").append("<tr><td>" +el.Nombre+ "</td><td>"+el.Categoria+"</td><td>$"+el.precio+"</td></tr>");
            });
            }
        });
    });
  </script>
  <script>
  $(document).ready(function(){
    $.get("http://ec2-54-201-234-64.us-west-2.compute.amazonaws.com:8080/View_pizzeria/webresources/Coupon",function(data) {
      $("#coupon").append("<strong>"+data.Nombre+" "+data.Apellido+"</strong>");
    },"json");
    });
  </script>
  <script type="text/javascript">
    function validarcedula()
    {
             var i;
             var cedula;
             var acumulado;
             cedula=document.getElementById("cedula").value;
             var instancia;
             acumulado=0;
             for (i=1;i<=9;i++)
             {
              if (i%2!=0)
              {
               instancia=cedula.substring(i-1,i)*2;
               if (instancia>9) instancia-=9;
              }
              else instancia=cedula.substring(i-1,i);
              acumulado+=parseInt(instancia);
             }
             while (acumulado>0)
              acumulado-=10;
             if (cedula.substring(9,10)!=(acumulado*-1))
             {
               document.getElementById("cedula").value = "Cédula no valida!";
                //alert("Cedula no valida!!");
             }
     }
  </script>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar-expand-md navbar-dark bg-dark navbar fixed-top">
    <div class="container">
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbar3SupportedContent" aria-controls="navbar3SupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
      <div class="collapse navbar-collapse text-center justify-content-center" id="navbar3SupportedContent">
        <ul class="navbar-nav">
          <li class="nav-item mx-3">
            <a class="nav-link text-light" href="#inicio"><b>FASTPIZZA</b></a>
          </li>
          <li class="nav-item mx-2">
            <a class="nav-link" href="#image"><b>GALERIA</b></a>
          </li>
        </ul>
        <a class="btn navbar-btn btn-secondary mx-2" href="#menu"><b>PIZZAS</b></a>
      </div>
    </div>
  </nav>
  <!-- Cover -->

  <div id="inicio" class="align-items-center d-flex photo-overlay py-5 cover" style="background-image: url(&quot;assets/restaurant/traditionalpizza.jpg&quot;);">
    <div class="container">
      <div class="row">
        <div class="col-lg-7 align-self-center text-lg-left text-center">
          <div class="alert alert-success">
            <?php echo $error; ?>
          </div>
          <h1 class="mb-0 mt-4 display-3">FastPizza</h1>
          <p class="mb-5 lead">La pizza que deseas al tiempo que ni te imaginas.</p>
        </div>
        <div class="col-lg-5 p-3">
          <!--<form class="p-4 bg-dark-opaque" method="post" action="#"></form>-->
            <h4 class="mb-4 text-center">Para llevar ?&nbsp;</h4>
            <button type="submit" class="btn btn-danger" onclick="document.getElementById('id01').style.display='block'"><b>Realizar pedido</b></button>
        </div>
      </div>
    </div>
  </div>
  <!-- Intro -->
  <div class="bg-dark py-5">
    <div class="container">
      <div class="row my-5 bg-secondary animate-in-down">
        <div class="p-4 col-md-6 bg-primary">
          <p class="mb-1">Mejores ingredientes, mejores historias</p>
          <h2 class="mb-1">Michel Comini</h2> <i class="fa d-inline-block fa-star text-white"></i><i class="fa d-inline-block fa-star mx-1 text-white"></i><i class="fa d-inline-block fa-star text-white"></i>
          <p class="my-4 text-center">Considerado uno de los chef más brillantes de los tiempos modernos.</p>
          <img class="img-fluid d-block" src="assets/restaurant/signature.png" width="300"> </div>
        <div class="p-0 col-md-6">
          <img class="img-fluid d-block" src="assets/restaurant/chef_dark.jpg"> </div>
      </div>
    </div>
  </div>
  <!-- Gallery -->
  <div class="">
    <div class="container-fluid">
      <div id="image">
        <div class="image i1"><img src="assets/pizza1.png"></div>
        <div class="image i2"><img src="assets/pizza2.jpg"></div>
        <div class="image i3"><img src="assets/pizza3.jpg"></div>
        <div class="image i4"><img src="assets/pizza4.jpg"></div>
        <div class="image i5"><img src="assets/pizza5.jpg"></div>
        <div class="image i6"><img src="assets/pizza6.jpg"></div>
        <div class="image i7"><img src="assets/pizza7.jpg"></div>
        <div class="image i8"><img src="assets/pizza8.jpg"></div>
      </div>
    </div>
  </div>
  <div class="py-5 text-center" id="bestcliente">
    <div class="container">
      <div class="row p-4 my-5 bg-primary animate-in-down">
        <div class="col-md-12">
          <h2 class="mt-3">Nuestro mejor cliente</h2>
          <div class="row">
            <center>
              <p> Felicitamos a nuestro mejor cliente <strong id="coupon"></strong> por hacerse acredor a un cupón </p>

            </center>
</div>
</div>
</div>
</div>
</div>

  <div class="py-5 text-center" id="menu">
    <div class="container">
      <div class="row p-4 my-5 bg-primary animate-in-down">
        <div class="col-md-12">
          <h2 class="mt-3">Menú</h2>
          <div class="row">
                <table class="table table-hover">
                  <thead>
                    <tr><th>Pizza</th>
                      <th>Ingredientes</th>
                      <th>Precio</th></tr>
                    </thead>
                <tbody id="pizzas">
                </tbody>
               </table>
</div>
</div>
</div>
</div>
</div>

<div id="id01" class="modal">

  <form class="modal-content animate" action="" method = "post">
    <div class="imgcontainer">
      <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
      <img src="img_avatar2.png" alt="Avatar" class="avatar">
    </div>

    <div class="container">
      <!--<form action = "" method = "post">
         <label>UserName  :</label><input type = "text" name = "username" class = "box"/><br /><br />
         <label>Password  :</label><input type = "password" name = "password" class = "box" /><br/><br />
         <input type = "submit" value = " Submit "/><br />
      </form>-->


      <label style="color:#000000"><b>Nickname</b></label>
      <input type="text" placeholder="Ingrese su nickname" name="username" required>

      <label style="color:#000000"><b>Contraseña</b></label>
      <input type="password" placeholder="Ingrese su contraseña" name="password" required>

      <!--<button type="submit">Entrar</button>-->
      <input class="btn btn-success" type ="submit" value ="Entrar"/>
      <a href="registrarse.html" class="btn btn-info">Registrarse</a>
      <!--<input class="btn btn-info" type ="submit" value ="Registrarse"/>-->
      <label style="color:#000000">
        <input  type="checkbox" checked="checked"> Recordar
      </label>


       <!--<div style = "font-size:11px; color:#cc0000; margin-top:10px"></div>-->
      <!--  <a href="#"><p>Registrarse</p></a>-->
      <strong><span class="psw" style="color:#000000">Olvido su <a href="#">contraseña?</a></span></strong>
    </div>
  </form>
</div>
  <center>
  <p><h2><strong>Frase del día </strong></h2></p>
  <p id="quote"></p>
</center>

  <!-- Carousel reviews -->
  <!-- Carousel venue -->
  <!-- Events -->
  <!-- Dark opaque section -->

  <!-- Footer -->
  <div class="text-center" id="direccion">
    <div class="container">
      <div class="row">
        <div class="col-md-4 p-4">
          <h2 class="mb-4">Contacto</h2>
          <p class="m-0">
            <a href="tel:+246 - 542 550 5462" class="text-white">+593 - 323 551 9462</a>
          </p>
          <p>
            <a href="mailto:info@pingendo.com" class="text-white">pizza@fastpizza.com</a>
          </p>
        </div>
        <div class="col-md-4 p-4">
          <h2 class="mb-4">Dirección</h2>
          <p>
            <a href="https://www.google.it/maps" target="_blank" class="text-white">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;6 Diciembre &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
              <br>&nbsp;Quito, EC 1723843</a>
          </p>
        </div>
        <div class="col-md-4 p-4">
          <h2 class="mb-4">Atención</h2>
          <p>11:00 - 15:00 &amp; 19:00 - 00:00 Tue/Fri
            <br>11:00 - 15:00 &amp; 19:00 - 02:00 Sat/Sun</p>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 mt-3">
          <p class="text-center text-muted">© Copyright 2018 ProgramaciónAvanzada - All rights reserved. </p>
        </div>
      </div>
    </div>
  </div>
  <!-- JavaScript dependencies -->
  <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" crossorigin="anonymous" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1"></script>
  <!-- Script: Smooth scrolling between anchors in the same page -->
  <script src="js/smooth-scroll.js"></script>
  <script src="js/main.js"></script>
  <script>
    var modal = document.getElementById('id01');
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>

</body>

</html>
