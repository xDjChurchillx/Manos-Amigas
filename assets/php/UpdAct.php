<?php
// Repetimos la misma configuración de sesión para asegurar consistencia
ini_set('session.use_only_cookies', 1);
require '../../../Private/Credentials/DataBase/connection.php';
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
        $codigoActividad = trim($_POST['codigoE'] ?? '');
        $nombreActividad = trim($_POST['nombreE'] ?? '');
        $descripcion = trim($_POST['descripcionE'] ?? '');
        $fecha = trim($_POST['fechaE'] ?? date('Y-m-d H:i:s'));
        $imagenesExistentes = $_POST['imgE'] ?? [];
        $nuevasImagenes = $_FILES['newimgE'] ?? [];

        // Validación de datos
        if (empty($codigoActividad) || empty($nombreActividad) || empty($descripcion) || empty($fecha)) {
            echo json_encode(["status" => "error", "ex" => "Todos los campos son obligatorios."]);
            exit();
        }

        // Validar si hay imágenes existentes o nuevas
        if (empty($imagenesExistentes) && empty($nuevasImagenes['name'][0])) {
            echo json_encode(["status" => "error", "ex" => "Debe haber al menos una imagen."]);
            exit();
        }

        // Procesar imágenes existentes
        $aux = 1;
        $imagenesActualizadas = [];
        foreach ($imagenesExistentes as $imagen) {
            $imagenesActualizadas[] = $imagen;
            $num = preg_replace('/\D/', '', $imagen); 
            if($num >= $aux){
              $aux = $num + 1;
            }
        }

        // Procesar nuevas imágenes
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        $tempDir = "../img/temp/";
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }
        if(!empty($nuevasImagenes['name'][0])){
             foreach ($nuevasImagenes['name'] as $index => $fileName) {
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                // Validar tipo de archivo
                if (!in_array($extension, $allowedExtensions)) {
                    echo json_encode(["status" => "error","a"=> $imagenesExistentes,"b"=> $nuevasImagenes, "ex" => "Formato de imagen no permitido ($extension)."]);
                    exit();
                }
                 // Validar tamaño de archivo
                if ($nuevasImagenes['size'][$index] > $maxFileSize) {
                    echo json_encode(["status" => "error", "ex" => "El archivo {$fileName} excede el tamaño permitido (5 MB)."]);
                    exit();
                }

                $newFileName = "img_" . $aux . ".webp";
                $filePath = $tempDir . $newFileName;

                $tmpFilePath = $nuevasImagenes['tmp_name'][$index];

                if ($extension !== 'webp') {
                    switch ($extension) {
                        case 'jpg':
                        case 'jpeg':
                            $image = imagecreatefromjpeg($tmpFilePath);
                            break;
                        case 'png':
                            $image = imagecreatefrompng($tmpFilePath);
                            break;
                        case 'gif':
                            $image = imagecreatefromgif($tmpFilePath);
                            break;
                        default:
                            echo json_encode(["status" => "error", "ex" => "Formato de imagen no soportado para conversión."]);
                            exit();
                    }

                    if ($image !== false) {
                        imagewebp($image, $filePath, 100);
                        imagedestroy($image);
                    } else {
                        echo json_encode(["status" => "error", "ex" => "Error al procesar la imagen."]);
                        exit();
                    }
                } else {
                    if (!move_uploaded_file($tmpFilePath, $filePath)) {
                        echo json_encode(["status" => "error", "ex" => "Error al mover el archivo WebP."]);
                        exit();
                    }
                }

                $imagenesActualizadas[] = $newFileName;
                $aux = $aux + 1;
              }
        }
       

        // Convertir rutas de imágenes a JSON
        $imageJson = json_encode($imagenesActualizadas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
         
        // Actualizar en la base de datos
        $stmt = $conn->prepare('CALL sp_ActualizarActividad(?, ?, ?, ?, ?, ?, ?)');
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
            exit();
        }

        $stmt->bind_param('sssssss', $username, $token, $codigoActividad, $nombreActividad, $descripcion, $fecha, $imageJson);

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (array_key_exists('Error', $row)) {
            echo json_encode([
                'status' => 'error',
                'ex' => 'Usuario o token inválido.'
            ]);
        } else {
            if (array_key_exists('Codigo', $row)) {
                $codigoActividad = $row['Codigo'];
                $codigoActividad = preg_replace('/\D/', '', $codigoActividad);
                $finalDir = "../img/{$codigoActividad}/";

                if (!file_exists($finalDir)) {
                    mkdir($finalDir, 0777, true);
                }

                if (is_dir($finalDir)) {
                    // Obtener todos los archivos del directorio
                    $archivosEnDirectorio = scandir($finalDir);
    
                    // Filtrar archivos válidos (excluyendo "." y "..")
                    foreach ($archivosEnDirectorio as $archivo) {
                        if ($archivo !== "." && $archivo !== "..") {
                            // Verificar si el archivo está en $imagenesActualizadas
                            if (!in_array($archivo, $imagenesActualizadas)) {
                                $rutaArchivo = $finalDir . $archivo;
                
                                // Intentar eliminar el archivo
                                if (!unlink($rutaArchivo)) {
                                    echo json_encode([
                                            'status' => 'error',
                                            'ex' => 'Error borrando archivo pasado'
                                        ]);
                                } 
                            }
                        }
                    }
                }



                // Mover las imágenes de la carpeta temporal a la carpeta final
                foreach ($imagenesActualizadas as $imageName) {
                    if (file_exists($tempDir . $imageName)) {
                        rename($tempDir . $imageName, $finalDir . $imageName);
                    }
                }

                // Eliminar la carpeta temporal
                if (file_exists($tempDir)) {
                    $files = glob($tempDir . '*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    rmdir($tempDir);
                }

                echo json_encode([
                    'status' => 'success',
                    'a'=> $codigoActividad,
                    'b'=> $nombreActividad,
                    'c'=> $descripcion,
                    'd'=> $fecha
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'ex' => 'Error en base de datos'
                ]);
            }
        }

        $stmt->close();
        $conn->close();
    }


} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex
    ]);
    exit();
}
?>
