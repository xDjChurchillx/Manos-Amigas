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
        $Titulo = trim($_POST['titulo'] ?? '');
         $Mensaje = trim($_POST['mensaje'] ?? '');
        // Validación de datos
        if (empty($Asunto) || empty($Titulo) || empty($Mensaje)) {
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
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>'.$Titulo.'</title>
                </head>
                <body style="margin: 0; padding: 0; background-color: #f8f9fa; line-height: 1.6;">

                    <!-- Hero con forma diagonal mejorado -->
                    <header style="
                        background: linear-gradient(135deg, #205781 0%, #4F959D 100%);
                        min-height: 23vh;
                        clip-path: polygon(0 0, 100% 0, 100% 80%, 0 100%);
                        color: white;
                        display: flex;
                        align-items: center;
                        padding-bottom: 5rem;
                    ">
                        <div style="width: 100%; text-align: center; padding-top: 3rem;">
                             <h1 style="
                                font-size: 2.5rem;
                                letter-spacing: 0.05em;
                                margin-bottom: 1rem;
                                text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
                                font-weight: 300;
                            ">
                              Centro Diurno Manos Amigas
                            </h1>
                             <div style="width: 400px; height: 3px; background: #F6F8D5; margin: 0 auto;"></div>
                            <h1 style="
               
                                 margin: 0;
                                font-size: 28px;
                                font-weight: 600;
                                letter-spacing: 0.5px;
                            ">
                                '.$Titulo.'
                            </h1>
           
                        </div>
                    </header>

                    <!-- Contenedor principal mejorado -->
                    <div style="
                        background-color: white;
                        overflow: hidden;
                    ">
                        <!-- Contenido mejorado -->
                        <div style="padding: 40px 30px; color: #444;">

                            <p style="
                                margin: 0 0 20px;
                                font-size: 16px;
                                line-height: 1.7;
                            ">
                               '.$Mensaje.'
                            </p>

                            <p style="
                                margin: 30px 0 20px;
                                font-size: 14px;
                                color: #666;
                            ">
                                Si prefieres no recibir noticias sobre nosotros, puedes <a href="#" style="color: #4F959D; text-decoration: underline;">eliminar la suscripción</a>.
                            </p>

                            <p style="
                                margin: 40px 0 20px;
                                font-style: italic;
                                color: #555;
                            ">
                                Con gratitud,<br>
                                El equipo de <strong style="color: #205781;">Centro Diurno Manos Amigas</strong>
                            </p>
                        </div>

                        <!-- Pie de página mejorado -->
                        <div style="
                            padding: 20px;
                            text-align: center;
                            background-color: #f5f7fa;
                            color: #205781;
                            font-size: 12px;
                            border-top: 1px solid #eaeaea;
                        ">
                            © 2025 Manos Amigas. Todos los derechos reservados.<br>
                            <span style="font-size: 11px; opacity: 0.7;">Cuidando de nuestros adultos mayores con amor y dedicación</span>
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
