<html>
<head>
    <title>Añadir Datos</title>
</head>
 
<body>
<?php
//including the database connection file
include_once("include/funciones.php");
 
$funciones = new funciones();
 
if(isset($_POST['Submit'])) {   
	$nombre = $funciones->escape_string($_POST['txtNombre']);
	$clave = $funciones->escape_string($_POST['txtClave']);
	$estado = $funciones->escape_string($_POST['rbestado']);
	$codigoMD5 = $funciones->escape_string($_POST['txtMD5']);
    $result = $funciones->execute("INSERT INTO usuarios(nombre,clave,estado,codigoMD5) VALUES('$nombre','$clave','$estado','$codigoMD5')");
        
        //display success message
    echo "<font color='green'>Datos añadidos.";
    echo "<br/><a href='index.php'>Ver resultado</a>";
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
</body>
</html>