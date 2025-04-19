<?php
require '../../../Private/Credentials/DataBase/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $email = trim($_POST["email"]);
     //regedex para correo
    $regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {      
         echo json_encode([
            'status' => 'error',
            'ex' => 'Todos los campos son obligatorios.'
        ]);
        exit();
    }
    if (!preg_match($regex, $email)) {
        echo json_encode([
            'status' => 'error',
            'ex' => 'Correo Invalido.'
        ]);
            exit();
    }  
    // Hashear la contraseña
    $hash = password_hash($password, PASSWORD_BCRYPT);


    $stmt = $conn->prepare('CALL sp_RegistrarUsuario(?, ?,?)');
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
        exit();
    }

    $stmt->bind_param('sss', $username, $hash,$email);

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