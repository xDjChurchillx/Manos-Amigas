<?php
// Repetimos la misma configuración de sesión para asegurar consistencia
ini_set('session.use_only_cookies', 1);
require '../../../Private/Credentials/DataBase/connection.php';
header('Content-Type: application/json; charset=UTF-8');

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
        'redirect' => '/Gestion/ingreso.html'
    ]);
    exit();
}
////////////////////////////////////////////////////////////////////////////////////////////
$token = $_COOKIE['token'] ;
$username = $_SESSION['username'];

$data1 = [];
$data2 = [];
$data3 = [];
$data4 = [];
$cat = [];
$sVisitas = 0;
$sSuscripciones = 0;
$sDonaciones = 0;
$sVoluntarios = 0;
$datos = json_decode(file_get_contents('php://input'), true);
if ($datos === null) {
    if (isset($_SESSION['datos'])) {
        $datos = $_SESSION['datos'];
    }	
}else{
    $_SESSION['datos'] = $datos;
}

// Validar y establecer las fechas de inicio y fin
$fechaDesde = isset($datos['fechaDesde']) && $datos['fechaDesde'] != '0000-00-00' ? $datos['fechaDesde'] : date('Y-m-d', strtotime('-7 days'));
$fechaHasta = isset($datos['fechaHasta']) && $datos['fechaHasta'] != '0000-00-00' ? $datos['fechaHasta'] : date('Y-m-d');

// Calcular la diferencia en días entre las dos fechas
$date1 = new DateTime($fechaDesde);
$date2 = new DateTime($fechaHasta);
$diff = $date1->diff($date2);
$diasDiferencia = $diff->days;


$stmt = $conn->prepare('CALL sp_ObtenerEstadisticas(?,?,?,?)');
if (!$stmt) {
     echo json_encode([
        'status' => 'error',
        'redirect' => '/Gestion/ingreso.html'
    ]);
    exit();
}

$stmt->bind_param('ssss', $username,$token,$datos['fechaDesde'],$datos['fechaHasta']);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
     echo json_encode([
        'status' => 'error',
        'redirect' => '/Gestion/ingreso.html'
    ]);
    exit();
}
$rows = [];
while ($row = $result->fetch_assoc()) {
    $sVisitas +=  $row['Visitas'];
    $sSuscripciones +=  $row['Suscripciones'];
    $sDonaciones +=  $row['Donaciones'];
    $sVoluntarios +=  $row['Voluntarios'];
    $rows[] = $row;
}
// Lógica para asignar valores a $cat según la diferencia de días
if ($diasDiferencia <= 7) {
    // Si la diferencia es menor o igual a 7 días, asignar días de la semana   
    foreach ($rows as $row) {
         $fecha = strtotime($row['Fecha']);
         // Obtener el día de la semana (1 = lunes, 2 = martes, etc.)
        $diaSemana = date('N', $fecha);
    
        // Agregar el día de la semana al arreglo $cat solo si no está ya presente
        $diaNombre = date('l', $fecha); // 'l' te da el nombre completo del día (por ejemplo, "Monday" o "Martes")
    
        // Si quieres los días en español puedes usar esta asignación:
        switch ($diaSemana) {
            case 1:
                $diaNombre = 'Lunes';
                break;
            case 2:
                $diaNombre = 'Martes';
                break;
            case 3:
                $diaNombre = 'Miércoles';
                break;
            case 4:
                $diaNombre = 'Jueves';
                break;
            case 5:
                $diaNombre = 'Viernes';
                break;
            case 6:
                $diaNombre = 'Sábado';
                break;
            case 7:
                $diaNombre = 'Domingo';
                break;
        }

        // Agregar el nombre del día al arreglo $cat
        if (!in_array($diaNombre, $cat)) {
            $data1[] = $row['Visitas'];
            $data2[] = $row['Suscripciones'];
            $data3[] = $row['Donaciones'];
            $data4[] = $row['Voluntarios'];
            $cat[] = $diaNombre;
        }
    }
} elseif ($diasDiferencia <= 90) {
    // Rango mayor a 7 y menor o igual a 90 - dividir en 7 categorías

    $numCategorias = 7; // Quieres 7 bloques
    $minFecha = min(array_column($rows, 'Fecha'));
    $maxFecha = max(array_column($rows, 'Fecha'));

    // Convertir fechas a timestamps
    $minTimestamp = strtotime($minFecha);
    $maxTimestamp = strtotime($maxFecha);

    // Calcular tamaño de cada bloque
    $rangoDias = ($maxTimestamp - $minTimestamp) / $numCategorias;

    // Crear etiquetas de las categorías (ej: "Del 01-01 al 07-01", etc.)
    $categorias = [];
    for ($i = 0; $i < $numCategorias; $i++) {
        $inicio = date('d-m', $minTimestamp + ($rangoDias * $i));
        $fin = date('d-m', $minTimestamp + ($rangoDias * ($i + 1) - 1));
        $categorias[] = "Del $inicio al $fin";
    }

    // Inicializar datos por cada categoría
    $data1 = array_fill(0, $numCategorias, 0);
    $data2 = array_fill(0, $numCategorias, 0);
    $data3 = array_fill(0, $numCategorias, 0);
    $data4 = array_fill(0, $numCategorias, 0);
    $cat = $categorias;

    // Recorrer filas y sumar datos a cada categoría según corresponda
    foreach ($rows as $row) {
        $fechaActual = strtotime($row['Fecha']);
        $index = floor(($fechaActual - $minTimestamp) / $rangoDias);
        if ($index >= $numCategorias) $index = $numCategorias - 1; // Ajuste por redondeo final

        $data1[$index] += $row['Visitas'];
        $data2[$index] += $row['Suscripciones'];
        $data3[$index] += $row['Donaciones'];
        $data4[$index] += $row['Voluntarios'];
    }
} else {
 
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
        <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
        <div class="container">	        
		    <div class="row text-center">
	            <div class="col">
	                <div class="counter">
                         <i class="fa fa-code fa-2x"></i>
                         <h2 class="timer count-title count-number" data-to="'.$sVisitas.'" data-speed="1500"></h2>
                          <p class="count-text ">Visitas</p>
                     </div>
	            </div>
                <div class="col">
                   <div class="counter">
                         <i class="fa fa-coffee fa-2x"></i>
                          <h2 class="timer count-title count-number" data-to="'.$sSuscripciones.'" data-speed="1500"></h2>
                         <p class="count-text ">Suscripciones</p>
                   </div>
                </div>
                <div class="col">
                     <div class="counter">
                       <i class="fa fa-lightbulb-o fa-2x"></i>
                       <h2 class="timer count-title count-number" data-to="'.$sDonaciones.'" data-speed="1500"></h2>
                       <p class="count-text ">Donaciones</p>
                      </div>
                 </div>
                 <div class="col">
                      <div class="counter">
                         <i class="fa fa-bug fa-2x"></i>
                         <h2 class="timer count-title count-number" data-to="'.$sVoluntarios.'" data-speed="1500"></h2>
                         <p class="count-text ">Voluntarios</p>
                      </div>
                  </div>
             </div>
           <div class="container">
                <div class="date-range-container">
                  <div>
                    <label for="desde">Desde:</label>
                    <input type="date" class="form-control" id="desde">
                  </div>
                  <div>
                    <label for="hasta">Hasta:</label>
                    <input type="date" class="form-control" id="hasta" disabled>
                  </div>
                  <div class="combobox-container">
                    <select class="form-select" id="opciones">
                      <option value=""></option>
                      <option value="hoy">Hoy</option>
                      <option value="semana">Semana</option>
                      <option value="mes">Mes</option>
                    </select>
                  </div>
                </div>
              </div>
             <div id="bsb-chart-3"></div>




        </div>
';


// Si pasa todas las validaciones, se puede mostrar el contenido
echo json_encode([
    'status' => 'success',
    'navbar' => $navbar,
    'panel' => $panel,
    'config' => $datos,
    'data1' => $data1,
    'data2' => $data2,
    'data3' => $data3,
    'data4' => $data4,
    'cat' => $cat,
    'rows'=> $rows
]);
?>
