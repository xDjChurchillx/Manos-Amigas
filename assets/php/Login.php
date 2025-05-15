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

try{
    //dominio de la pagina 
    $dominio = "https://" . $_SERVER['HTTP_HOST'];
    //$dominio = "http://" . $_SERVER['HTTP_HOST'];
    session_set_cookie_params([
        'lifetime' => 0, // Hasta cerrar navegador
        'path' => '/',
        'domain' => '', 
        'secure' => true, // Solo HTTPS 
        'httponly' => true, // No accesible desde JavaScript
        'samesite' => 'Strict', // Protección contra CSRF
    ]);

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {    
        // Validacion de datos
        if (!isset($_POST["username"]) || !isset($_POST["password"])) {
            header("Location: /Gestion/Ingreso.html?error=2"); // Falta de datos
            exit();
        }

        // Sanitización de entrada
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if (empty($username) || empty($password)) {
            header("Location: /Gestion/Ingreso.html?error=2"); // Campos vacíos
            exit();
        }

        // Obtener el hash de la base de datos
        $stmt = $conn->prepare("CALL sp_Login(?)");
        if (!$stmt) {
            header("Location: /Gestion/Ingreso.html?error=3"); // Error en la base de datos
            exit();
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (array_key_exists('Success', $row)) {
            $storedHash = $row["Success"]; // Hash almacenado en la base de datos

            // Verificar la contraseña usando password_verify()
            if (password_verify($password, $storedHash)) {
                session_regenerate_id(true); // Regenerar sesión para evitar fixation
                $_SESSION["username"] = $username;
                $_SESSION["user_agent"] = $_SERVER['HTTP_USER_AGENT']; // Asociar sesión al navegador
                $_SESSION["ip_address"] = $_SERVER['REMOTE_ADDR']; // Opcional: asociar a IP

                // Liberar los resultados de la primera consulta
                $result->free();
                $stmt->close();
                $activationToken = bin2hex(random_bytes(32));

                 //Actualizar la sesión de la base de datos
                $stmt = $conn->prepare("CALL sp_UpdateSession(?, ?)");
                if (!$stmt) {
                    header("Location: /Gestion/Ingreso.html?error=3"); // Error en la base de datos
                    exit();
                }

                $stmt->bind_param("ss", $username, $activationToken);
                $stmt->execute();

                setcookie("token", $activationToken, time() + 86400, "/");

                header("Location: /Gestion/Panel.html"); // Redirigir a dashboard
                exit();
            } else {
                header("Location: /Gestion/Ingreso.html?error=1"); // Usuario o contraseña incorrectos
                exit();
            }
        }elseif (array_key_exists('Correo', $row)) {
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
            $mail->addAddress( html_entity_decode($row['Correo'], ENT_QUOTES | ENT_HTML5, 'UTF-8'), '');

            // Configura el asunto y el cuerpo del correo
            $mail->Subject = 'Recuperacion de cuenta';
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
                                      Confirmar Suscripcion
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
                                        Este correo es para recuperar la cuenta para gestionar Centro Diurno Manos Amigas la cual se encuetra bloqueada debido a un exceso de intentos para ingresar en la cuenta.
                                    </p>
            
                                  
                                   <a href="'.$dominio.'/Gestion/Ingreso.html?error=8&correo='.urlencode(html_entity_decode($row['Correo'], ENT_QUOTES | ENT_HTML5, 'UTF-8')).'&token='.urlencode($row['Token']).'" 
                                  style="display: block; width: 60%; margin: 40px auto; padding: 15px; background: linear-gradient(135deg, #4F959D, #98D2C0); color: white; text-align: center; text-decoration: none; font-weight: bold; border-radius: 50px; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(79, 149, 157, 0.3);">Recuperar</a>
                                  
                                   <p style="
                                        margin: 30px 0 20px;
                                        font-size: 14px;
                                        color: #666;
                                    ">
                                        Si no fuistes tu el que la bloqueo coincidera cambiar la contraseña y el usuario lo mas pronto posible.
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
                header("Location: /Gestion/Ingreso.html?error=15"); // success
                 exit();
            } else {
                header("Location: /index.html?error=4"); // Fallo inesperado
                exit();
            }  
        }elseif (array_key_exists('Error', $row)) {
	      header("Location: /Gestion/Ingreso.html?error=1"); // Error en la base de datos
          exit();
        }else {
            header("Location: /Gestion/Ingreso.html?error=3"); // Error en la base de datos
            exit();
        }

        $stmt->close();
        $conn->close();
         
    }
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => 'error en login php'
    ]);
    exit();
}
?>