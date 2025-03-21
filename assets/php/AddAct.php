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

       // Crear carpeta aleatoria para las imágenes
        $randomFolderName = bin2hex(random_bytes(8));
        $uploadDir = "../img/{$randomFolderName}/";

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imagePaths = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Extensiones permitidas
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

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

            // Guardar imagen con nombre único
            $newFileName = $nombreActividad . "_" . ($index + 1) . ".webp";
            $filePath = $uploadDir . $newFileName;
            $tmpPath = $_FILES['imagenes']['tmp_name'][$index];

            // Convertir la imagen a WebP
            $image = null;
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image = imagecreatefromjpeg($tmpPath);
                    break;
                case 'png':
                    $image = imagecreatefrompng($tmpPath);
                    imagepalettetotruecolor($image); // Convertir a true color para evitar errores de transparencia
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'gif':
                    $image = imagecreatefromgif($tmpPath);
                    break;
            }

            if ($image) {
                imagewebp($image, $filePath, 80); // Guardar como WebP con calidad 80%
                imagedestroy($image); // Liberar memoria
                $imagePaths[] = $filePath;
            }
        }

        // Convertir rutas de imágenes a JSON
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
