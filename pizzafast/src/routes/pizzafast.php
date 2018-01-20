<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app = new \Slim\App;
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
// Get All Customers
$app->get('/clientes', function(Request $request, Response $response){
    $sql = "SELECT * FROM cliente";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customers);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
$app->get('/repartidor', function(Request $request, Response $response){
    $sql = "SELECT * FROM repartidor";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($customers);
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
$app->post('/clientes', function(Request $request, Response $response){
	$name = $request->getParam('nombre');
	$lastname= $request->getParam('apellido');
	$birthday = $request->getParam('fechaNac');
	//$age = $request->getParam('edad');
	//$bonus = $request->getParam('bono'); 
	$sql = "INSERT INTO cliente (nombre,apellido,fechaNac,edad) values (:nombre,:apellido,:fechaNac,TIMESTAMPDIFF(YEAR,:fechaNac,CURDATE()))";
	$sql2 = "UPDATE cliente SET  bono=20 WHERE month(CURDATE()) = month(:fechaNac) and day(curdate()) = day(:fechaNac) AND nombre=:nombre";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nombre', $name);
        $stmt->bindParam(':apellido',$lastname);
        $stmt->bindParam(':fechaNac',$birthday);
       // $stmt->bindParam(':edad',      $age);
       // $stmt->bindParam(':bono',    $bonus);
        $stmt->execute();
		 $stmt = $db->prepare($sql2);
		 $stmt->bindParam(':fechaNac',$birthday);
		 $stmt->bindParam(':nombre', $name);
		 $stmt->execute();
        echo '{"notice": {"text": "Customer Added"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
$app->post('/repartidor', function(Request $request, Response $response){
	$nickname = $request->getParam('nickname');
	$cedula= $request->getParam('cedula');
	$sql = "INSERT INTO repartidor (nickname,cedula) values (:nickname,:cedula)";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nickname', $nickname);
        $stmt->bindParam(':cedula',$cedula);
		$stmt->execute();
        echo '{"notice": {"text": "Repartidor Añadido"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
// Update Customer
$app->put('/repartidor/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $empleadoMes= $request->getParam('empleadoMes');
	$sueldo= $request->getParam('sueldo');
    $sql = "UPDATE repartidor SET
				empleadoMes 	= :empleadoMes
			WHERE id_repartidor = $id";
			$sqlBonoSi = "UPDATE repartidor SET
				sueldo = 436
			WHERE id_repartidor = $id AND empleadoMes=\"Si\"";
			$sqlBonoNo = "UPDATE repartidor SET
				sueldo = 386
			WHERE id_repartidor = $id AND empleadoMes=\"No\"";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':empleadoMes', $empleadoMes);
       // $stmt->bindParam(':sueldo',  $sueldo);
        $stmt->execute();
		$stmt = $db->prepare($sqlBonoSi);
       // $stmt->bindParam(':sueldo',  $sueldo);
        $stmt->execute();
		$stmt = $db->prepare($sqlBonoNo);
       // $stmt->bindParam(':sueldo',  $sueldo);
        $stmt->execute();
        echo '{"notice": {"text": "Repartidor Actualizado"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
// Delete Customer
$app->delete('/clientes/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM cliente WHERE id_cliente = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Cliente Eliminado"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
$app->delete('/repartidor/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM repartidor WHERE id_repartidor = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Repartidor Eliminado"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
/*
// Add Customer
$app->post('/api/edades/add', function(Request $request, Response $response){
    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');
    $sql = "INSERT INTO edad (first_name,last_name,phone,email,address,city,state) VALUES
    (:first_name,:last_name,:phone,:email,:address,:city,:state)";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name',  $last_name);
        $stmt->bindParam(':phone',      $phone);
        $stmt->bindParam(':email',      $email);
        $stmt->bindParam(':address',    $address);
        $stmt->bindParam(':city',       $city);
        $stmt->bindParam(':state',      $state);
        $stmt->execute();
        echo '{"notice": {"text": "Customer Added"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
// Update Customer
$app->put('/api/customer/update/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $first_name = $request->getParam('first_name');
    $last_name = $request->getParam('last_name');
    $phone = $request->getParam('phone');
    $email = $request->getParam('email');
    $address = $request->getParam('address');
    $city = $request->getParam('city');
    $state = $request->getParam('state');
    $sql = "UPDATE customers SET
				first_name 	= :first_name,
				last_name 	= :last_name,
                phone		= :phone,
                email		= :email,
                address 	= :address,
                city 		= :city,
                state		= :state
			WHERE id = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name',  $last_name);
        $stmt->bindParam(':phone',      $phone);
        $stmt->bindParam(':email',      $email);
        $stmt->bindParam(':address',    $address);
        $stmt->bindParam(':city',       $city);
        $stmt->bindParam(':state',      $state);
        $stmt->execute();
        echo '{"notice": {"text": "Customer Updated"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});
// Delete Customer
$app->delete('/api/customer/delete/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $sql = "DELETE FROM customers WHERE id = $id";
    try{
        // Get DB Object
        $db = new db();
        // Connect
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo '{"notice": {"text": "Customer Deleted"}';
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});*/