<?php
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;
  require 'vendor/autoload.php';
  require 'src/config/db.php';
  require 'src/routes/usuariorest.php';
  $app->run();
?>
