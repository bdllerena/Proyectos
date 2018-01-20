<?php
include $_SERVER['DOCUMENT_ROOT']."/empleados/include/Dbconfig.php";
   /*include('../include/Dbconfig.php');*/
   session_start();

   $user_check = $_SESSION['login_user'];

   $ses_sql = mysqli_query($db,"select nombre_usu from usuario where nombre_usu = '$user_check' ");

   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);

   $login_session = $row['nombre_usu'];

   if(!isset($_SESSION['login_user'])){
      header("location:index.php");
   }
?>
