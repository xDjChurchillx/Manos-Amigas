<?php
// Repetimos la misma configuraci�n de sesi�n para asegurar consistencia
ini_set('session.use_only_cookies', 1);
require '../../../Private/Credentials/DataBase/connection.php';
header('Content-Type: application/json; charset=UTF-8');
try{
session_set_cookie_params([
    'lifetime' => 0, // Hasta cerrar navegador
    'path' => '/',
    'domain' => '', // Cambia por tu dominio real
    'secure' => false, // Solo HTTPS (IMPORTANTE en producci�n)
    'httponly' => true, // No accesible desde JavaScript
    'samesite' => 'Strict', // Protecci�n contra CSRF
]);

session_start();

// Validaci�n de sesi�n
if (!isset($_COOKIE['token']) || !isset($_SESSION['username']) ||
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] ||
    $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    // No autenticado o sesi�n alterada
        setcookie('token', '', time() - 3600, '/');
        session_unset(); // Limpia variables de sesi�n
        session_destroy(); // Elimina la sesi�n

    
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

        // Validaci�n de datos
        if (empty($nombreActividad) || empty($descripcion) || empty($fecha)) {
            echo json_encode(["status" => "error", "ex" => "Todos los campos son obligatorios."]);
            exit();
        }

        // Validar si hay im�genes antes de procesarlas
        if (!isset($_FILES['imagenes']) || empty($_FILES['imagenes']['name'][0])) {
            echo json_encode(["status" => "error", "ex" => "Debe subir al menos una imagen."]);
            exit();
        }

        // Crear carpeta aleatoria para las im�genes
        $randomFolderName = bin2hex(random_bytes(8));
        $uploadDir = "../img/{$randomFolderName}/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imagePaths = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Extensiones permitidas (incluyendo WebP)
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        foreach ($_FILES['imagenes']['name'] as $index => $fileName) {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validar tipo de archivo
            if (!in_array($extension, $allowedExtensions)) {
                echo json_encode(["status" => "error", "ex" => "Formato de imagen no permitido ($extension)."]);
                exit();
            }

            // Validar tama�o de archivo
            if ($_FILES['imagenes']['size'][$index] > $maxFileSize) {
                echo json_encode(["status" => "error", "ex" => "El archivo {$fileName} excede el tama�o permitido (5 MB)."]);
                exit();
            }

            // Guardar imagen con nombre �nico
            $newFileName = $nombreActividad . "_" . ($index + 1) . ".webp"; // Siempre guardamos como WebP
            $filePath = $uploadDir . $newFileName;

            // Mover el archivo subido a la carpeta temporal
            $tmpFilePath = $_FILES['imagenes']['tmp_name'][$index];

            // Convertir a WebP si no lo es
            if ($extension !== 'webp') {
                // Cargar la imagen seg�n su formato original
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
                        echo json_encode(["status" => "error", "ex" => "Formato de imagen no soportado para conversi�n."]);
                        exit();
                }

                // Convertir y guardar como WebP
                if ($image !== false) {
                    imagewebp($image, $filePath, 80); // Calidad 80 (ajustable)
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

            $imagePaths[] = $filePath;
        }

        // Convertir rutas de im�genes a JSON
        $imageJson = json_encode(["name" => $randomFolderName, "imgs" => $imagePaths], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        // Insertar en la base de datos
        $stmt = $conn->prepare('CALL sp_CrearActividad(?, ?, ?, ?, ?, ?)');
        if (!$stmt) {
            echo json_encode(['status' => 'error', 'ex' => 'Error en la base de datos']);
            exit();
        }

        $stmt->bind_param('ssssss', $username, $token, $nombreActividad, $descripcion, $fecha, $imageJson);

        if ($stmt->execute()) {
          echo json_encode([
                'status' => 'success'
            ]);
        } else {
          echo json_encode([
                'status' => 'error'
            ]);
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
