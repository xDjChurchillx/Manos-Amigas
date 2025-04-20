<?php
// Configuracion de la Base de datos
require '../../../Private/Credentials/DataBase/connection.php';

try{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {    
        // Validacion de datos
       if (!isset($_POST["correoR"]) || !isset($_POST["tokenR"]) || !isset($_POST["nuevaContrasena"]) || !isset($_POST["confirmarContrasena"])) {
            header("Location: /Gestion/ingreso.html?error=10"); // Falta de datos
            exit();
        }

        // Sanitización de entrada
        $correo = trim($_POST["correoR"]);
        $token = trim($_POST["tokenR"]);
        $nuevaContrasena = trim($_POST["nuevaContrasena"]);
        $confirmarContrasena = trim($_POST["confirmarContrasena"]);

        if (empty($correo) || empty($token) || empty($nuevaContrasena) || empty($confirmarContrasena)) {
            header("Location: /Gestion/ingreso.html?error=10"); // Campos vacíos
            exit();
        }
        if (strlen($nuevaContrasena) < 10 || strlen($nuevaContrasena) > 20 ) {
            header("Location: /Gestion/ingreso.html?error=14"); // Fortmato incorrecto
            exit();
        }
        if ($nuevaContrasena != $confirmarContrasena) {
            header("Location: /Gestion/ingreso.html?error=11"); // contraseña diferente
            exit();
        }
        // Recuperar cuenta de la base de datos
        $stmt = $conn->prepare('CALL sp_Recover(?, ?, ?)');
        if (!$stmt) {
           header("Location: /Gestion/ingreso.html?error=3"); // Base de datos
            exit();
        }
        $hash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);
        $stmt->bind_param('sss', $correo, $token, $hash);

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (array_key_exists('Success', $row)) {
           header("Location: /Gestion/ingreso.html?error=12"); // Success
           exit();
        
        } else {
           header("Location: /Gestion/ingreso.html?error=13"); // Error en token o Credentials
           exit();
        }

        $stmt->close();
        $conn->close();
    }else {
	   header("Location: /Gestion/ingreso.html?error=4"); // Error inesperado
       exit();
    }

} catch (Exception $ex) {
    header("Location: /Gestion/ingreso.html?error=4"); // Error inesperado
    exit();
}
?>