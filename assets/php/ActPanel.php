﻿<?php
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


$stmt = $conn->prepare('CALL sp_ListarActividades()');
if (!$stmt) {
     echo json_encode([
        'status' => 'error',
         'ex' => 'database error'
    ]);
    exit();
}
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
     echo json_encode([
        'status' => 'error',
         'ex' => 'database error'
    ]);
    exit();
}
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$navbar = '
        <li class="nav-item">
            <a class="nav-link" href="Panel.html">Panel</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Actividades.html">Actividades</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Donaciones.html">Donaciones</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="Suscripciones.html">Suscripciones</a>
        </li>
        <!-- Menú desplegable del usuario -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <svg fill="#FFFFFF" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M6.03531778,18.739764 C7.62329979,20.146176 9.71193925,21 12,21 C14.2880608,21 16.3767002,20.146176 17.9646822,18.739764 C17.6719994,17.687349 15.5693823,17 12,17 C8.43061774,17 6.32800065,17.687349 6.03531778,18.739764 Z M4.60050358,17.1246475 C5.72595131,15.638064 8.37060189,15 12,15 C15.6293981,15 18.2740487,15.638064 19.3994964,17.1246475 C20.4086179,15.6703183 21,13.9042215 21,12 C21,7.02943725 16.9705627,3 12,3 C7.02943725,3 3,7.02943725 3,12 C3,13.9042215 3.59138213,15.6703183 4.60050358,17.1246475 Z M12,23 C5.92486775,23 1,18.0751322 1,12 C1,5.92486775 5.92486775,1 12,1 C18.0751322,1 23,5.92486775 23,12 C23,18.0751322 18.0751322,23 12,23 Z M8,10 C8,7.75575936 9.57909957,6 12,6 C14.4141948,6 16,7.92157821 16,10.2 C16,13.479614 14.2180861,15 12,15 C9.76086382,15 8,13.4273743 8,10 Z M10,10 C10,12.2692568 10.8182108,13 12,13 C13.1777063,13 14,12.2983927 14,10.2 C14,8.95041736 13.2156568,8 12,8 C10.7337387,8 10,8.81582479 10,10 Z" />
                </svg>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#">Cambiar contraseña</a></li>
                <li><a class="dropdown-item" href="../assets/php/Logout.php">Cerrar sesión</a></li>
            </ul>
        </li>
';

$panel = '
    <div id="listpanel" class="container text-center mt-4">
        <h2 class="mb-4">Actividades</h2>
       <div class="d-flex justify-content-between align-items-center">
          <!-- Botón Crear Actividad a la izquierda -->
          <button class="btn btn-primary mb-3" onclick="create()">Crear Actividad</button>
  
          <!-- Contenedor para el TextBox y el botón de buscar a la derecha -->
          <div class="d-flex">
            <input type="text" class="form-control me-2" placeholder="Buscar...">
            <button class="btn btn-outline-secondary" onclick="search()">
               <img src="../assets/img/iconos/buscar.svg" alt="Buscar" class="icono">
            </button>
          </div>
        </div>

';

if (empty($rows)) {
    // Si no hay actividades, mostrar el mensaje
    $panel .= '
        <p>No hay actividades disponibles en este momento.</p>';
} else {
    // Si hay actividades, crear la tabla con los datos
    $panel .= '
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($rows as $actividad) {
        $panel .= '
            <tr>
                <td>' . htmlspecialchars($actividad['Fecha']) . '</td>
                <td>' . htmlspecialchars($actividad['Nombre']) . '</td>
                <td>' . htmlspecialchars($actividad['Descripcion']) . '</td>
                <td>
                    <button class="btn btn-primary btn-sm" onclick="edit(\'' . htmlspecialchars($actividad['Codigo']) . '\')">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="del(\'' . htmlspecialchars($actividad['Codigo']) . '\')">Eliminar</button>
                </td>
            </tr>';
    }

    $panel .= '
            </tbody>
        </table>';
}

$panel .= '</div>

<div id="creatediv" class="container d-none mt-5 ">
    <h2>Crear Actividad</h2>
    <form action="../assets/php/AddAct.php" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="codigo" class="form-label">Código de Actividad</label>
        <input type="datetime-local" class="form-control" id="codigo" name="codigo" required>
      </div>
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre de la Actividad</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
      </div>
      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
      </div>
      <div class="mb-3">
        <label for="fecha" class="form-label">Fecha de la Actividad</label>
        <input type="datetime-local" class="form-control" id="fecha" name="fecha" required>
      </div>
      <div class="mb-3">
        <label for="imagenes" class="form-label">Imágenes</label>
        <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple>
        <small class="text-muted">Selecciona varias imágenes si es necesario.</small>
      </div>     
      <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
  </div>


';

// Si pasa todas las validaciones, se puede mostrar el contenido
echo json_encode([
    'status' => 'success',
    'navbar' => $navbar,
    'panel' => $panel
]);
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex
    ]);
    exit();
}
?>
