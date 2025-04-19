<?php
// Configuracion de Base de datos
require '../../../Private/Credentials/DataBase/connection.php';
try{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
         //Variables del form
        $Nombre = isset($_POST['Nombre']) ? $_POST['Nombre'] : '';
        $Correo = isset($_POST['Correo']) ? $_POST['Correo'] : '';
        $Telefono = isset($_POST['Telefono']) ? $_POST['Telefono'] : '';
        $Institucion = isset($_POST['Institucion']) ? $_POST['Institucion'] : '';
        $Carrera = isset($_POST['Carrera']) ? $_POST['Carrera'] : '';
        $Propuesta = isset($_POST['Propuesta']) ? $_POST['Propuesta'] : ''; 
        $otraPropuesta = isset($_POST['otraPropuesta']) ? $_POST['otraPropuesta'] : '';

       // Validación de datos
        if (empty($Nombre)) {
             header("Location: /Voluntariado.html?error=1"); // Error en datos incompletos 
             exit();
        }
        if (empty($Correo) && empty($Telefono)) {
             header("Location: /Voluntariado.html?error=1"); // Error en datos incompletos 
             exit();
        }
       if (empty($Propuesta)) {
             header("Location: /Voluntariado.html?error=1"); // Error en datos incompletos 
             exit();
        }
        // Sanitizar TODOS los caracteres especiales 
        $Nombre = htmlentities($Nombre, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Telefono = htmlentities($Telefono, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Correo = htmlentities($Correo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Institucion = htmlentities($Institucion, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Carrera = htmlentities($Carrera, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Propuesta = htmlentities($Propuesta, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $otraPropuesta = htmlentities($otraPropuesta, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  
        if($Propuesta == 'otro'){
          $Propuesta = $otraPropuesta;
        }

        //Insertar en la base de datos
        $stmt = $conn->prepare('CALL sp_CrearVoluntario(?,?,?,?,?,?)');
        if (!$stmt) {
            header("Location: /Voluntariado.html?error=2"); // Error en BD
            exit();
        }

        $stmt->bind_param('ssssss', $Nombre, $Telefono,$Correo, $Institucion, $Carrera, $Propuesta);

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (array_key_exists('Success', $row)) {
                header("Location: /Voluntariado.html?error=0"); // Success
                exit();
        } else {
            header("Location: /Voluntariado.html?error=2"); // Error en BD
            exit();
        }
        $stmt->close();
        $conn->close();


    } else {
        // Si alguien intenta acceder directamente al script sin enviar el formulario
        header('Location: ../../index.html');
        exit;
    }
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => 'error en voluntariado php'
    ]);
    exit();
}
?>