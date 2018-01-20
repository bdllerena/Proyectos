<html>
<head>
    <title>Añadir Datos</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
 
<body>
<?php
//including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
if(isset($_POST['Submit'])) {   
	$nombre = $funciones->escape_string($_POST['txtNombre']);
    $result = $funciones->execute("INSERT INTO modalidad (tipo) VALUES('$nombre')");
        
        //display success message
		
   // echo "<font color='green'>Datos añadidos.";
    echo "<div class=\"alert alert-success\" role=\"alert\">  <strong>Logrado!</strong> Se ingreso un registro al sistema.</div>";
    echo "<br/><a href='index.php'><button class=\"btn btn-sucess\">Ver resultado</button></a>";
    // checking empty fields
    /*
	if($msg != null) {
        echo $msg;        
        //link to the previous page
        echo "<br/><a href='javascript:self.history.back();'>Go Back</a>";
    } elseif (!$check_age) {
        echo 'Please provide proper age.';
    } elseif (!$check_email) {
        echo 'Please provide proper email.';
    }    
    else { 
        // if all the fields are filled (not empty) 
            
        //insert data to database    
        $result = $crud->execute("INSERT INTO users(name,age,email) VALUES('$name','$age','$email')");
        
        //display success message
        echo "<font color='green'>Data added successfully.";
        echo "<br/><a href='index.php'>View Result</a>";
    }*/
}
?>
<script src="http://code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>