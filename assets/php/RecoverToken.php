<?php
// Configuracion de la clase PHPMailer y Base de datos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../../Private/Credentials/DataBase/connection.php';
require '../../../Private/Credentials/mailCred.php';
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

try{
    //$dominio = "https://" . $_SERVER['HTTP_HOST'];
    $dominio = "http://" . $_SERVER['HTTP_HOST'];
    if ($_SERVER["REQUEST_METHOD"] == "POST") {    
        // Validacion de datos
        if (!isset($_POST["usernameR"])) {
            header("Location: /Gestion/ingreso.html?error=2"); // Falta de datos
            exit();
        }

        // Sanitización de entrada
        $username = trim($_POST["usernameR"]);
       

        if (empty($username)) {
            header("Location: /Gestion/ingreso.html?error=2"); // Campos vacíos
            exit();
        }

        // Obtener el hash de la base de datos
        $stmt = $conn->prepare("CALL sp_RecoverToken(?)");
        if (!$stmt) {
            header("Location: /Gestion/ingreso.html?error=3"); // Error en la base de datos
            exit();
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();       
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (array_key_exists('Success', $row)) {
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
                                        Este correo es para recuperar la cuenta para gestionar Centro Diurno Manos Amigas
                                    </p>
            
                                  
                                   <a href="'.$dominio.'/assets/php/Recover.php?error=8&correo='.urlencode(html_entity_decode($row['Correo'], ENT_QUOTES | ENT_HTML5, 'UTF-8')).'&token='.urlencode($row['Token']).'" 
                                  style="display: block; width: 60%; margin: 40px auto; padding: 15px; background: linear-gradient(135deg, #4F959D, #98D2C0); color: white; text-align: center; text-decoration: none; font-weight: bold; border-radius: 50px; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(79, 149, 157, 0.3);">Recuperar</a>
                                  
                                   <p style="
                                        margin: 30px 0 20px;
                                        font-size: 14px;
                                        color: #666;
                                    ">
                                        Si no solicitaste la recuperacion de cuenta coincidera cambiar la contraseña y el usuario lo mas pronto posible.
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
                header("Location: /Gestion/ingreso.html?error=6"); // success
                 exit();
            } else {
                header("Location: /index.html?error=4"); // Fallo inesperado
                exit();
            }  


           
        } else {   
            header("Location: /Gestion/ingreso.html?error=7".$username); // Error no coincide ni correo ni usuario
            exit();
        }
        $stmt->close();
        $conn->close();   
    }else {
	   header("Location: /Gestion/ingreso.html?error=4"); // Error inesperado
       exit();
    }

} catch (Exception $ex) {
    header("Location: /Gestion/ingreso.html?error=4".$ex->getMessage()); // Error inesperado
    exit();
}
?>