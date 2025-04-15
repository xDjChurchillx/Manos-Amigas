<?php

    // Importa la clase PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../Private/Credentials/DataBase/connection.php';
require '../../../Private/Credentials/mailCred.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

// Repetimos la misma configuración de sesión para asegurar consistencia
ini_set('session.use_only_cookies', 1);
header('Content-Type: application/json; charset=UTF-8');
try{
session_set_cookie_params([
    'lifetime' => 0, // Hasta cerrar navegador
    'path' => '/',
    'domain' => '', // Cambia por tu dominio real
    'secure' => false, // Solo HTTPS (IMPORTANTE en producción)
    'httponly' => true, // No accesible desde JavaScript
    'samesite' => 'Strict', // Protección contra CSRF
]);

session_start();

// Validación de sesión
if (!isset($_COOKIE['token']) || !isset($_SESSION['username']) ||
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] ||
    $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    // No autenticado o sesión alterada
        setcookie('token', '', time() - 3600, '/');
        session_unset(); // Limpia variables de sesión
        session_destroy(); // Elimina la sesión

    
    // Retornar JSON con error
    echo json_encode([
        'status' => 'error',
        'redirect' => '/Gestion/ingreso.html?error=1'
    ]);
    exit();
}
////////////////////////////////////////////////////////////////////////////////////////////
$token = $_COOKIE['token'] ;
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Asunto = trim($_POST['asunto'] ?? '');
        $Mensaje = trim($_POST['mensaje'] ?? '');
        // Validación de datos
        if (empty($Asunto) || empty($Mensaje)) {
            echo json_encode(["status" => "error", "ex" => "Todos los campos son obligatorios."]);
            exit();
        }
        // 
        $stmt = $conn->prepare('CALL sp_ListarSusActivas(?, ?)');
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
            exit();
        }

        $stmt->bind_param('ss', $username, $token);

        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
             // Instancia un nuevo objeto PHPMailer
            $mail = new PHPMailer(true);
            // Configura el servidor SMTP
            //    $mail->isSMTP();
            //    $mail->Host       = 'smtp.hostinger.com';  // Cambia esto por tu servidor SMTP
            //    $mail->SMTPAuth   = true;
            //    $mail->Username   = $mail1; // Cambia esto por tu nombre de usuario SMTP
            //    $mail->Password   = $Pmail1; // Cambia esto por tu contraseña SMTP
            //    $mail->SMTPSecure = 'tls';
            //    $mail->Port       = 587;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $mail1; 
            $mail->Password   = $Pmail1;
            $mail->SMTPSecure = 'tls';                      // También podés usar 'ssl'
            $mail->Port       = 587;
            // Configura el remitente y el destinatario
            $mail->setFrom($mail1 , 'ManosAmigas');
                 
 
            while ($row = $result->fetch_assoc()) {
                if (array_key_exists('Error', $row)) {
                        echo json_encode([
                        'status' => 'error',
                        'redirect' => '/Gestion/ingreso.html?error=1'
                    ]);
                    exit();
                }
                foreach ($row as $key => $value) {
                   $row[$key] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
                $mail->addAddress( $row['Correo'] , ''); 
                $subscriptions[] = $row;
            }
                 // Configura el asunto y el cuerpo del correo
            $mail->Subject = $Asunto;
            $mail->isHTML(true);  
            $mail->Body = '
            <html>
            <head>
                <style type="text/css">
                    body {
                        margin: 0;
                        padding: 0;
                        background-color: #F6F8D5;
                        font-family: \'Arial\', sans-serif;
                        line-height: 1.6;
                    }
        
                    .email-container {
                        max-width: 600px;
                        margin: 20px auto;
                        background-color: white;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    }
        
                    .email-header {
                        background: linear-gradient(135deg, #205781, #4F959D);
                        padding: 30px 20px;
                        text-align: center;
                        color: white;
                    }
        
                    .email-header h1 {
                        margin: 0;
                        font-size: 28px;
                        font-weight: bold;
                    }
        
                    .email-body {
                        padding: 30px;
                        color: #333;
                    }
        
                    .email-footer {
                        padding: 20px;
                        text-align: center;
                        background-color: #f5f5f5;
                        color: #205781;
                        font-size: 12px;
                    }
        
                    .subscribe-btn {
                        display: block;
                        width: 60%;
                        margin: 40px auto;
                        padding: 15px;
                        background: linear-gradient(135deg, #4F959D, #98D2C0);
                        color: white;
                        text-align: center;
                        text-decoration: none;
                        font-weight: bold;
                        border-radius: 50px;
                        transition: all 0.3s ease;
                        box-shadow: 0 4px 8px rgba(79, 149, 157, 0.3);
                    }
        
                    .subscribe-btn:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 6px 12px rgba(79, 149, 157, 0.4);
                    }
        
                    .logo {
                        text-align: center;
                        margin-bottom: 20px;
                        font-family: \'Courier New\', Courier, monospace;
                        font-size: 24px;
                        color: #205781;
                        font-weight: bold;
                    }
                </style>
            </head>
            <body>
                <div class="email-container">
                    <div class="email-header">
                        <h1>Mensaje</h1>
                    </div>
        
                    <div class="email-body">
                        <div class="logo">Manos Amigas</div>
            
                        <p>Hola amigo/a,</p>
            
                        <p>'.$Mensaje.'</p>
            

                        <p>Si prefieres no recibir noticias sobre nosotros, puedes ignorar este mensaje.</p>
            
                        <p>Con gratitud,<br>
                        El equipo de <strong>Manos Amigas</strong></p>
                    </div>
        
                    <div class="email-footer">
                        © 2023 Manos Amigas. Todos los derechos reservados.
                    </div>
                </div>
            </body>
            </html>
            ';
            if ($mail->send()) {
                echo json_encode([
                        'status' => 'success',
                        'activos' => $subscriptions
                ]);
                exit();
            } else {
               echo json_encode([
                    'status' => 'error',
                    'ex' => 'Error enviando correo'
               ]);
                exit();
            }           
           
        } else {
             echo json_encode([
                    'status' => 'error',
                    'ex' => 'Error en base de datos'
              ]);
              exit();
        }
        $stmt->close();
        $conn->close();
}

} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex->getMessage()
    ]);
    exit();
}
?>
