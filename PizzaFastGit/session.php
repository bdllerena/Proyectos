<?php
   include('include/Dbconfig.php');
   session_start();

   $user_check = $_SESSION['login_user'];

   $ses_sql = mysqli_query($db,"select nickname from usuario where nickname = '$user_check' ");

   $row = mysqli_fetch_array($ses_sql,MYSQLI_ASSOC);

   $login_session = $row['nickname'];

   if(!isset($_SESSION['login_user'])){
      header("location:index.php");
   }
?>
