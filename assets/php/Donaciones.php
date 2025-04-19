<?php
// Configuracion de Base de datos 
require '../../../Private/Credentials/DataBase/connection.php';
try{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Variables del form
        $Metodo = isset($_POST['Metodo']) ? $_POST['Metodo'] : '';
        $Destino= isset($_POST['Destino']) ? $_POST['Destino'] : '';
        $Nombre = isset($_POST['NombreD']) ? $_POST['NombreD'] : '';
        $Contacto = isset($_POST['ContactoD']) ? $_POST['ContactoD'] : '';
        $Mensaje = isset($_POST['MensajeD']) ? $_POST['MensajeD'] : '';

       // Validación de datos
        if (empty($Metodo)) {
             header("Location: /Donaciones.html?error=1"); // Error en datos incompletos
             exit();
        }
        if (empty($Destino)) {
             header("Location: /Donaciones.html?error=1"); // Error en datos incompletos 
             exit();
        }

        // Sanitizar TODOS los caracteres especiales 
        $Metodo = htmlentities($Metodo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Destino= htmlentities($Destino, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Nombre = htmlentities($Nombre, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Contacto = htmlentities($Contacto, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $Mensaje = htmlentities($Mensaje, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        //Insertar en la base de datos
        $stmt = $conn->prepare('CALL sp_CrearDonacion(?,?,?,?,?)');
        if (!$stmt) {
            header("Location: /Donaciones.html?error=2"); // Error en BD
            exit();
        }

        $stmt->bind_param('sssss', $Metodo, $Destino, $Nombre, $Contacto, $Mensaje);

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (array_key_exists('Success', $row)) {
                header("Location: /Donaciones.html?error=0"); // Success
                exit();
        } else {
            header("Location: /Donaciones.html?error=2"); // Error en BD
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
         'ex' => 'error en donaciones php'
    ]);
    exit();
}
?>