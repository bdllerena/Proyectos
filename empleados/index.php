<?php
   include("include/Dbconfig.php");
   session_start();
   $error = "Bienvenido";
   if($_SERVER["REQUEST_METHOD"] == "POST") {

      $myusername = mysqli_real_escape_string($db,$_POST['username']);
      $mypassword = mysqli_real_escape_string($db,$_POST['password']);

      $sql = "SELECT id_usu FROM usuario WHERE nombre_usu = '$myusername' and clave_usu = '$mypassword'";
      $result = mysqli_query($db,$sql);
      //$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
      /*$active = $row['active'];*/

      $count = mysqli_num_rows($result);

      // If result matched $myusername and $mypassword, table row must be 1 row

      if($count == 1) {
          if($myusername == 'empleado')
          {
            $_SESSION['login_user'] = $myusername;
            header("location: empleado/empleado.php");
          }
          else
          {
            $_SESSION['login_user'] = $myusername;

            header("location: usuario/usuariopanel.php");
          }

      }else {
         $error = "Tu nombre o clave es invalida";
      }
   }
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href='https://fonts.googleapis.com/css?family=Ubuntu:500' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<style>
body {
  background:url('https://i1.wp.com/rwallpapers.com/wp-content/uploads/2017/11/moon-beautiful-hd-free-wallpapers.jpg?resize=1440%2C900&ssl=1');
  margin:0px;
  font-family: 'Ubuntu', sans-serif;
  color: #fff;

}
h1, h2, h3, h4, h5, h6, a {
  margin:0; padding:0;
  color: #fff;
}
.login {
  margin:0 auto;
  max-width:500px;
  color: #fff;
}
.login-header {
  color:#fff;
  text-align:center;
  font-size:300%;
}
/* .login-header h1 {
   text-shadow: 0px 5px 15px #000; */
}
.login-form {
  border:.5px solid #fff;
  background:#4facff;
  border-radius:10px;
  box-shadow:0px 0px 10px #000;
}
.login-form h3 {
  text-align:left;
  margin-left:40px;
  color:#fff;
}
.login-form {
  box-sizing:border-box;
  padding-top:15px;
	padding-bottom:10%;
  margin:5% auto;
  text-align:center;
}
.login input[type="text"],
.login input[type="password"] {
  max-width:400px;
	width: 80%;
  line-height:3em;
  font-family: 'Ubuntu', sans-serif;
  margin:1em 2em;
  border-radius:5px;
  border:2px solid #f2f2f2;
  outline:none;
  padding-left:10px;
}
.login-form input[type="button"] {
  height:30px;
  width:100px;
  background:#fff;
  border:1px solid #f2f2f2;
  border-radius:20px;
  color: slategrey;
  text-transform:uppercase;
  font-family: 'Ubuntu', sans-serif;
  cursor:pointer;
}
.sign-up{
  color:#f2f2f2;
  margin-left:-70%;
  cursor:pointer;
  text-decoration:underline;
}
.no-access {
  color:#E86850;
  margin:20px 0px 20px -57%;
  text-decoration:underline;
  cursor:pointer;
}
.try-again {
  color:#f2f2f2;
  text-decoration:underline;
  cursor:pointer;
}
.button {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}
.button2 {background-color: #008CBA;
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;} /* Blue */
/*Media Querie*/
@media only screen and (min-width : 150px) and (max-width : 530px){
  .login-form h3 {
    text-align:center;
    margin:0;
  }
  .sign-up, .no-access {
    margin:10px 0;
  }
  .login-button {
    margin-bottom:10px;
  }
}
</style>
</head>
<body>
  <div class="login">
  <div class="login-header">
    <h1>Login</h1>
  </div>
	<div class="alert alert-success">
            <?php echo $error; ?>
          </div>
	<form action="" method = "post">
    <div class="container">
      <label style="color:#fff"><b>Usuario</b></label>
      <input type="text" placeholder="Ingrese su usuario" name="username" required>

      <label style="color:#fff"><b>Contraseña</b></label>
      <input type="password" placeholder="Ingrese su contraseña" name="password" required>

      <!--<button type="submit">Entrar</button>-->
    <center>  <input class="button" type ="submit" value ="Entrar"/></center>
      <!--<input class="btn btn-info" type ="submit" value ="Registrarse"/>-->
    </div>
  </form>
</div>
</body>
