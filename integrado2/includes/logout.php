<?php
session_start();
$parametros = session_get_cookie_params();
setcookie(session_name(), '', time() - 2000,
        $parametros["path"], $parametros["domain"],
        $parametros["secure"], $parametros["httponly"]
    );
session_unset();   
session_destroy();
header('Location: ../index.php');
?>
