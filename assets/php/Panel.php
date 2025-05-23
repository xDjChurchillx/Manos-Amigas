﻿<?php
// Configuracion de Cookies y Base de datos 
ini_set('session.use_only_cookies', 1);
require '../../../Private/Credentials/DataBase/connection.php';
header('Content-Type: application/json; charset=UTF-8');
try{
    session_set_cookie_params([
        'lifetime' => 0, // Hasta cerrar navegador
        'path' => '/',
        'domain' => '', 
        'secure' => true, // Solo HTTPS 
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
            'redirect' => '/Gestion/Ingreso.html?error=1'
        ]);
        exit();
    }

    //Sesion valida
    $token = $_COOKIE['token'] ;
    $username = $_SESSION['username'];

    //variables de estaditica para el panel
    $data1 = [];
    $data2 = [];
    $data3 = [];
    $data4 = [];
    $cat = [];
    $sumas = [
        'a' => 0,
        'b' => 0,
        'c' => 0,
        'd' => 0
    ];

    //Verificar si hay inputs para el panel
    $datos = json_decode(file_get_contents('php://input'), true);
    if ($datos === null) {
        if (isset($_SESSION['datos'])) {
            $datos = $_SESSION['datos'];
        }	
    }else{
        $_SESSION['datos'] = $datos;
    }

    // Establecer fechas predeterminadas si no se proporcionan
    if (empty($datos['fechaDesde'])) {
        $datos['fechaDesde'] = date('Y-m-d', strtotime('-6 days'));
    }
    if (empty($datos['fechaHasta'])) {
        $datos['fechaHasta'] = date('Y-m-d');
    }
    $date1 = new DateTime($datos['fechaDesde']);
    $date2 = new DateTime($datos['fechaHasta']);

    //Obtener estadisticas de la base de datos
    $stmt = $conn->prepare('CALL sp_ObtenerEstadisticas(?,?,?,?)');
    if (!$stmt) {
         echo json_encode([
             'status' => 'error',
             'ex' => 'database error'
        ]);
        exit();
    }
    $stmt->bind_param('ssss', $username,$token,$datos['fechaDesde'],$datos['fechaHasta']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result === false) {
         echo json_encode([
           'status' => 'error',
             'ex' => 'database error'
        ]);
        exit();
    }

    //Iterar en los resultados de la base de datos
    $rows = [];
    while ($row = $result->fetch_assoc()) {
         if (array_key_exists('Error', $row)) {
             echo json_encode([
                'status' => 'error',
                'redirect' => '/Gestion/Ingreso.html?error=1'
            ]);
            exit();
        }
        $sumas['a'] += $row['Visitas'];
        $sumas['b'] += $row['Suscripciones'];
        $sumas['c'] += $row['Donaciones'];
        $sumas['d'] += $row['Voluntarios'];
        $rows[] = $row;
    }

    // Calcular la diferencia en días entre las dos fechas
    $diff = $date1->diff($date2);
    $diasDiferencia = $diff->days + 1;

    //crear los rangos
    if ($diasDiferencia <= 7) {
        //por semana
       $diasSemana = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $period = new DatePeriod($date1, new DateInterval('P1D'), $date2->modify('+1 day'));
        foreach ($period as $date) {
            $cat[] = $diasSemana[$date->format('w')]; // Obtener el nombre en español
        }
    } elseif ($diasDiferencia <= 90) {
        //por rangos
        $numBloques = 7;
        $intervalo = ($date2->getTimestamp() - $date1->getTimestamp()) / $numBloques;
        for ($i = 0; $i < $numBloques; $i++) {
             $inicio = date('d/m', $date1->getTimestamp() + $intervalo * $i); 
            if ($i == $numBloques - 1) {
                $fin = date('d/m', $date2->getTimestamp()); 
            } else {
                $fin = date('d/m', $date1->getTimestamp() + $intervalo * ($i + 1));
            }    
            $cat[] = "Del $inicio al $fin";
        }
    } else {
        //por mes
        $period = new DatePeriod($date1, new DateInterval('P1M'), $date2->modify('+1 month'));
        foreach ($period as $date) {
            $cat[] = $date->format('M Y');
        }
    }

    // Inicializar datos 
    $data1 = array_fill(0, count($cat), 0);
    $data2 = array_fill(0, count($cat), 0);
    $data3 = array_fill(0, count($cat), 0);
    $data4 = array_fill(0, count($cat), 0);
    foreach ($rows as $row) {
        $fecha = new DateTime($row['Fecha']);
        $index = null;

        if ($diasDiferencia <= 7) {
            $diffRow = $date1->diff($fecha);
            $index = $diffRow->days; 
        } elseif ($diasDiferencia <= 90) {
            $timestamp = $fecha->getTimestamp();
            $index = floor(($timestamp - $date1->getTimestamp()) / $intervalo);
            $index = min($index, $numBloques - 1);
        } else {
            $monthYear = $fecha->format('M Y');
            $index = array_search($monthYear, $cat);
        }

        if ($index !== false && isset($data1[$index])) {
            $data1[$index] += $row['Visitas'];
            $data2[$index] += $row['Suscripciones'];
            $data3[$index] += $row['Donaciones'];
            $data4[$index] += $row['Voluntarios'];
        }
    }

    //Navbar para el html
    $navbar = '
             <li class="nav-item mx-2 "><a class="nav-link vavbarItemActive active" href="Panel.html">Panel</a></li>
             <li class="nav-item mx-2 "><a class="nav-link vavbarItem" href="Actividades.html">Actividades</a></li>
             <li class="nav-item mx-2 "><a class="nav-link vavbarItem" href="Donaciones.html">Donaciones</a></li>
             <li class="nav-item mx-2 "><a class="nav-link vavbarItem" href="Voluntarios.html">Voluntarios</a></li>
             <li class="nav-item mx-2 "><a class="nav-link vavbarItem" href="Suscripciones.html">Suscripciones</a></li>
            
            <!-- Menú desplegable del usuario -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg fill="#FFFFFF" width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M6.03531778,18.739764 C7.62329979,20.146176 9.71193925,21 12,21 C14.2880608,21 16.3767002,20.146176 17.9646822,18.739764 C17.6719994,17.687349 15.5693823,17 12,17 C8.43061774,17 6.32800065,17.687349 6.03531778,18.739764 Z M4.60050358,17.1246475 C5.72595131,15.638064 8.37060189,15 12,15 C15.6293981,15 18.2740487,15.638064 19.3994964,17.1246475 C20.4086179,15.6703183 21,13.9042215 21,12 C21,7.02943725 16.9705627,3 12,3 C7.02943725,3 3,7.02943725 3,12 C3,13.9042215 3.59138213,15.6703183 4.60050358,17.1246475 Z M12,23 C5.92486775,23 1,18.0751322 1,12 C1,5.92486775 5.92486775,1 12,1 C18.0751322,1 23,5.92486775 23,12 C23,18.0751322 18.0751322,23 12,23 Z M8,10 C8,7.75575936 9.57909957,6 12,6 C14.4141948,6 16,7.92157821 16,10.2 C16,13.479614 14.2180861,15 12,15 C9.76086382,15 8,13.4273743 8,10 Z M10,10 C10,12.2692568 10.8182108,13 12,13 C13.1777063,13 14,12.2983927 14,10.2 C14,8.95041736 13.2156568,8 12,8 C10.7337387,8 10,8.81582479 10,10 Z" />
                    </svg>
                </a>
                <ul class="dropdown-menu bg1color dropdown-menu-dark dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item text-white" href="#" data-bs-toggle="modal" data-bs-target="#ModalEditUsr">Editar Perfil</a></li>
                    <li><a class="dropdown-item text-white" href="../assets/php/Logout.php">Cerrar sesión</a></li>
                </ul>
            </li>
    ';
    //panel para el html
    $tituloheader = 'Panel de Gestion';
    $panel = '   
        <section class="w-100">
                <div class="row text-center mtop justify-content-center">
	                <div class="col-6 col-md-3 mb-3">
	                    <div class="counter c1">
                            <img class="icono-big" src="../assets/img/iconos/ojo.svg" alt="icono" >
                                <h2 id="a" class="timer count-title count-number" data-to="'.$sumas['a'].'" data-speed="1500"></h2>
                                <p class="count-text ">Visitas</p>
                            </div>
	                </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="counter c2">
                                <img class="icono-big" src="../assets/img/iconos/suscribir.svg" alt="icono" >
                                <h2 id="b" class="timer count-title count-number" data-to="'.$sumas['b'].'" data-speed="1500"></h2>
                                <p class="count-text ">Suscripciones</p>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                            <div class="counter c3">
                            <img class="icono-big" src="../assets/img/iconos/dinero.svg" alt="icono">
                            <h2 id="c" class="timer count-title count-number" data-to="'.$sumas['c'].'" data-speed="1500"></h2>
                            <p class="count-text ">Donaciones</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="counter c4">
                                <img class="icono-big" src="../assets/img/iconos/voluntario.svg" alt="icono">
                                <h2 id="d" class="timer count-title count-number" data-to="'.$sumas['d'].'" data-speed="1500"></h2>
                                <p class="count-text ">Voluntarios</p>
                            </div>
                        </div>
                </div>
        </section>
        <section class="w-100">
            <div class="container p-4">
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
        </section>        
         <!-- modals -->
        <section>
            <div class="modal modal-site fade" id="ModalEditUsr" tabindex="-1" aria-labelledby="Editarlabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
    
                        <div class="modal-header">
                        <h5 class="modal-title" id="Editarlabel">Editar Perfil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
      
                        <form id="editarUsrForm">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="UserActual" class="form-label">Usuario</label>
                                    <input type="text" class="form-control" id="UserActual" name="UserActual" required value="'.$username.'">
                                </div>
                                <div class="mb-3">
                                    <label for="contrasenaActual" class="form-label">Contraseña actual</label>
                                    <input type="password" class="form-control" id="contrasenaActual" name="contrasenaActual" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nuevaContrasena" class="form-label">Nueva contraseña(opcional)</label>
                                    <input type="password" class="form-control" id="nuevaContrasena" name="nuevaContrasena">
                                </div>
                                <div class="mb-3">
                                    <label for="confirmarContrasena" class="form-label">Confirmar nueva contraseña(opcional)</label>
                                    <input type="password" class="form-control" id="confirmarContrasena" name="confirmarContrasena">
                                </div>
                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo(opcional)</label>
                                    <input type="email" class="form-control" id="correo" name="correo">
                                </div>
                                <div id="codigoVerificacion" class="mb-3 d-none">
                                    <label for="correo" class="form-label">Codigo de Confirmacion de Correo</label>
                                    <div class="d-flex gap-2">
                                        <input type="text" name="code1" class="form-control text-center border rounded shadow-sm" maxlength="1" style="width: 3rem; height: 3rem;" />
                                        <input type="text" name="code2" class="form-control text-center border rounded shadow-sm" maxlength="1" style="width: 3rem; height: 3rem;" />
                                        <input type="text" name="code3" class="form-control text-center border rounded shadow-sm" maxlength="1" style="width: 3rem; height: 3rem;" />
                                        <input type="text" name="code4" class="form-control text-center border rounded shadow-sm" maxlength="1" style="width: 3rem; height: 3rem;" />
                                        <input type="text" name="code5" class="form-control text-center border rounded shadow-sm" maxlength="1" style="width: 3rem; height: 3rem;" />
                                    </div>
                                </div>
                               
                            </div>
                            <span id="rEditUser" class="text-danger"></span>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                            </div>
                        </form>
      
                    </div>
                </div>
            </div>
        </section> 
    ';

    //Retorno de todos los valores necesarios para el panel
    echo json_encode([
        'status' => 'success',
        'navbar' => $navbar,
        'panel' => $panel,
        'config' => $datos,
        'sumas' => $sumas,
        'data1' => $data1,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'cat' => $cat,        
        'name' => 'ModalEditUsr',
        'name0' => 'editarUsrForm',
        'name1' => 'rEditUser',
        'url0' => '../assets/php/UpdCorreo.php',
        'url1' => '../assets/php/UpdUsr.php',
        'name2' => 'Visitas',
        'name3' => 'Suscripciones',
        'name4' => 'Donaciones',
        'name5' => 'Voluntarios'
    ]);
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex->getMessage()
    ]);
    exit();
}
?>
