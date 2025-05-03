<?php
// Configuracion de la clase PHPMailer y Base de datos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../Private/Credentials/DataBase/connection.php';
require '../../../Private/Credentials/mailCred.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


ini_set('session.use_only_cookies', 1);
header('Content-Type: application/json; charset=UTF-8');
try{
      //$dominio = "https://" . $_SERVER['HTTP_HOST'];
    $dominio = "http://" . $_SERVER['HTTP_HOST'];
    session_set_cookie_params([
        'lifetime' => 0, // Hasta cerrar navegador
        'path' => '/',
        'domain' => '', 
        'secure' => false, // Solo HTTPS 
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
        // Retornar error de credenciales invalidas
        echo json_encode([
            'status' => 'error',
            'redirect' => '/Gestion/ingreso.html?error=1'
        ]);
        exit();
    }
   
    //Sesion valida
    $token = $_COOKIE['token'] ;
    $username = $_SESSION['username'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        //Variables del form
        $correo = trim($_POST['correo'] ?? '');
        // Validación de datos
       
        if(strlen($correo) == 0){           
            echo json_encode(["status" => "error", "ex" => "No existe correo"]);
            exit();            
        }
        $cartas = [];
        for ($i = 0; $i < 5; $i++) {
            $cartas[] = rand(1, 99);
        }

        // Guardar en la sesión
        $_SESSION['cartas'] = $cartas;

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
            $mail->addAddress($correo, '');

            // Configura el asunto y el cuerpo del correo
            $mail->Subject = 'Cambio de Correo';
            $mail->isHTML(true);  
            $mail->Body = '
             <html>
                <head>
                          <meta charset="UTF-8">
                          <meta name="viewport" content="width=device-width, initial-scale=1.0">
                          <title>Recuperacion</title>
                </head>
                <body>
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
                                      Confirmar Correo
                                  </h1>
 
                              </div>
                          </header>
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
                                        Hola,
                                    </p>
                                    <p style="
                                        margin: 0 0 20px;
                                        font-size: 16px;
                                        line-height: 1.7;
                                    ">
                                        Este correo es para confirmar la direccion de respaldo para el usuario de gestion.
                                    </p>
                                   <div style="display: flex; justify-content: center; gap: 10px;">';

            
                              foreach ($cartas as $numero) {
                                $mail->Body .= '
                                <div style="
                                  width: 80px;
                                  height: 120px;
                                  background-color: white;
                                  border: 2px solid #333;
                                  border-radius: 10px;
                                  box-shadow: 2px 2px 6px rgba(0,0,0,0.2);
                                  display: flex;
                                  align-items: center;
                                  justify-content: center;
                                  font-size: 32px;
                                  font-weight: bold;
                                  color: #444;
                                ">
                                  ' . $numero . '
                                </div>';
                                }    
                                  
                                  
                                 
                                $mail->Body .= '
                                     </div>
                                    <p style="
                                        margin: 40px 0 20px;
                                        font-style: italic;
                                        color: #555;
                                    ">
                                        Con gratitud,<br>
                                        El equipo de <strong style="color: #205781;">Centro Diurno Manos Amigas</strong>
                                    </p>
                              </div>

                              <!-- Pie de página -->
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
                echo json_encode(['status' => 'success']);
                exit();
            } else {
                 echo json_encode(["status" => "error", "ex" => "Error al enviar correo"]);
                  exit();    
            }  
      
    }
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex->getMessage()
    ]);
    exit();
}
?>
