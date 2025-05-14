<?php
// Configuracion de la Base de datos
require '../../../Private/Credentials/DataBase/connection.php';

try{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {   
        $correo = isset($_POST["correoR"]) ? trim($_POST["correoR"]) : "";
        $token = isset($_POST["tokenR"]) ? trim($_POST["tokenR"]) : "";
        $nuevaContrasena = isset($_POST["nuevaContrasena"]) ? trim($_POST["nuevaContrasena"]) : "";
        $confirmarContrasena = isset($_POST["confirmarContrasena"]) ? trim($_POST["confirmarContrasena"]) : "";
        // Validacion de datos
        if (empty($correo) || empty($token) || empty($nuevaContrasena) || empty($confirmarContrasena)) {
            header("Location: /Gestion/Ingreso.html?error=10"."&correo=".$correo."&token=".$token); // Campos vacíos
            exit();
        }
        if (strlen($nuevaContrasena) < 10 || strlen($nuevaContrasena) > 20 ) {
            header("Location: /Gestion/Ingreso.html?error=14"."&correo=".$correo."&token=".$token); // Fortmato incorrecto
            exit();
        }
        if ($nuevaContrasena != $confirmarContrasena) {
            header("Location: /Gestion/Ingreso.html?error=11"."&correo=".$correo."&token=".$token); // contraseña diferente
            exit();
        }
        $correo = htmlentities($correo, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Recuperar cuenta de la base de datos
        $stmt = $conn->prepare('CALL sp_Recover(?, ?, ?)');
        if (!$stmt) {
           header("Location: /Gestion/Ingreso.html?error=3"); // Base de datos
            exit();
        }
        $hash = password_hash($nuevaContrasena, PASSWORD_BCRYPT);
        $stmt->bind_param('sss', $correo, $token, $hash);

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (array_key_exists('Success', $row)) {
           header("Location: /Gestion/Ingreso.html?error=12"); // Success
           exit();
        
        }elseif (array_key_exists('Token', $row)) {
             header("Location: /Gestion/Ingreso.html?error=16"); // Success
           exit();
        }else {
           header("Location: /Gestion/Ingreso.html?error=13"."&correo=".$correo."&token=".$token); // Error en token o Credentials
           exit();
        }

        $stmt->close();
        $conn->close();
    }else {
	   header("Location: /Gestion/Ingreso.html?error=4"."&correo=".$correo."&token=".$token); // Error inesperado
       exit();
    }

} catch (Exception $ex) {
    header("Location: /Gestion/Ingreso.html?error=4"); // Error inesperado
    exit();
}
?>