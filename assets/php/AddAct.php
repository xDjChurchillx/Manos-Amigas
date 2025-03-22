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
        $nombreActividad = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $fecha = trim($_POST['fecha'] ?? date('Y-m-d H:i:s'));

        // Validación de datos
        if (empty($nombreActividad) || empty($descripcion) || empty($fecha)) {
            echo json_encode(["status" => "error", "ex" => "Todos los campos son obligatorios."]);
            exit();
        }

        // Validar si hay imágenes antes de procesarlas
        if (!isset($_FILES['imagenes']) || empty($_FILES['imagenes']['name'][0])) {
            echo json_encode(["status" => "error", "ex" => "Debe subir al menos una imagen."]);
            exit();
        }

        $imagePaths = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Extensiones permitidas (incluyendo WebP)
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        // Crear carpeta temporal para las imágenes
        $tempDir = "../img/temp/";

        // Si la carpeta temporal ya existe, eliminarla y crearla de nuevo en blanco
        if (file_exists($tempDir)) {
            // Eliminar todos los archivos dentro de la carpeta temporal
            $files = glob($tempDir . '*'); // Obtener todos los archivos
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); // Eliminar cada archivo
                }
            }
            rmdir($tempDir); // Eliminar la carpeta temporal
        }

        // Crear la carpeta temporal en blanco
        mkdir($tempDir, 0777, true);

        foreach ($_FILES['imagenes']['name'] as $index => $fileName) {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validar tipo de archivo
            if (!in_array($extension, $allowedExtensions)) {
                echo json_encode(["status" => "error", "ex" => "Formato de imagen no permitido ($extension)."]);
                exit();
            }

            // Validar tamaño de archivo
            if ($_FILES['imagenes']['size'][$index] > $maxFileSize) {
                echo json_encode(["status" => "error", "ex" => "El archivo {$fileName} excede el tamaño permitido (5 MB)."]);
                exit();
            }

            $newFileName = "img_" . ($index + 1) . ".webp"; // Siempre guardamos como WebP
            $filePath = $tempDir . $newFileName;

            // Mover el archivo subido a la carpeta temporal
            $tmpFilePath = $_FILES['imagenes']['tmp_name'][$index];

            // Convertir a WebP si no lo es
            if ($extension !== 'webp') {
                // Cargar la imagen según su formato original
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

                // Convertir y guardar como WebP
                if ($image !== false) {
                    imagewebp($image, $filePath, 100); // Calidad 100 (ajustable)
                    imagedestroy($image); // Liberar memoria
                } else {
                    echo json_encode(["status" => "error", "ex" => "Error al procesar la imagen."]);
                    exit();
                }
            } else {
                // Si ya es WebP, simplemente mover el archivo
                if (!move_uploaded_file($tmpFilePath, $filePath)) {
                    echo json_encode(["status" => "error", "ex" => "Error al mover el archivo WebP."]);
                    exit();
                }
            }

            $imagePaths[] = $newFileName; // Solo guardamos el nombre de la imagen
        }

        // Convertir rutas de imágenes a JSON
        $imageJson = json_encode($imagePaths, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Insertar en la base de datos
        $stmt = $conn->prepare('CALL sp_CrearActividad(?, ?, ?, ?, ?, ?)');
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
            exit();
        }

        $stmt->bind_param('ssssss', $username, $token, $nombreActividad, $descripcion, $fecha, $imageJson);

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
                // Crear carpeta con el nombre del código de la actividad
                $finalDir = "../img/{$codigoActividad}/";
                if (!file_exists($finalDir)) {
                    mkdir($finalDir, 0777, true);
                }

                // Mover las imágenes de la carpeta temporal a la carpeta final
                foreach ($imagePaths as $imageName) {
                    rename($tempDir . $imageName, $finalDir . $imageName);
                }

                // Eliminar la carpeta temporal
                if (file_exists($tempDir)) {
                    $files = glob($tempDir . '*'); // Obtener todos los archivos
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file); // Eliminar cada archivo
                        }
                    }
                    rmdir($tempDir); // Eliminar la carpeta temporal
                }

                echo json_encode([
                    'status' => 'success'
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
