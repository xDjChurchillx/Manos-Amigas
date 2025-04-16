<?php
require '../../../Private/Credentials/DataBase/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        echo "Error: Todos los campos son obligatorios.";
        exit();
    }

    // Hashear la contraseña
    $hash = password_hash($password, PASSWORD_BCRYPT);


    $stmt = $conn->prepare('CALL sp_RegistrarUsuario(?, ?)');
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
        exit();
    }

    $stmt->bind_param('ss', $username, $hash);

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (array_key_exists('Success', $row)) {
        echo json_encode([
            'status' => 'Success'            
        ]);
        exit();
    }else {
	    echo json_encode([
            'status' => 'error',
            'ex' => 'Usuario o token inválido.'
        ]);
        exit();
   }  
   
}