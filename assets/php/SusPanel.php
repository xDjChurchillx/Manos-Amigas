<?php
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

    //Variable para buscar elementos en especifico
    $buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';
    $buscar = trim($buscar);
    $buscar = filter_var($buscar, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $buscar = htmlspecialchars($buscar, ENT_QUOTES, 'UTF-8');

     //Obtener suscripciones de la base de datos
    $stmt = $conn->prepare("CALL sp_ListarSuscripciones(?,?,?)");
    $stmt->bind_param('sss',$username,$token, $buscar);
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
        foreach ($row as $key => $value) {
            if ($key !== 'Activo') {
                $row[$key] = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        $rows[] = $row;
    }
    $navbar = '
             <li class="nav-item mx-2 "><a class="nav-link vavbarItem" href="Panel.html">Panel</a></li>
             <li class="nav-item mx-2 "><a class="nav-link vavbarItem" href="Actividades.html">Actividades</a></li>
             <li class="nav-item mx-2 "><a class="nav-link vavbarItem" href="Donaciones.html">Donaciones</a></li>
             <li class="nav-item mx-2 "><a class="nav-link vavbarItem" href="Voluntarios.html">Voluntarios</a></li>
             <li class="nav-item mx-2 "><a class="nav-link vavbarItemActive active" href="Suscripciones.html">Suscripciones</a></li>
            
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
    $panel = '
       <section>
        <div id="listpanel" class="container text-center mt-4">
            <h2 class="mb-4">Suscripciones</h2>
           <div class="d-flex justify-content-between align-items-center">
              <!-- Botón msj Suscripciones a la izquierda -->
              <button class="btn btn-primary mb-3" onclick="msj()">Mensaje</button>
  
              <!-- Contenedor para el TextBox y el botón de buscar a la derecha -->
              <div class="d-flex mb-3">
                <input id="buscar" type="text" class="form-control me-2" placeholder="Buscar...">
                <button class="btn btn-outline-secondary" onclick="search()">
                   <img src="../assets/img/iconos/buscar.svg" alt="Buscar" class="icono">
                </button>
              </div>
            </div>

    ';

    if (empty($rows)) {
        // Si no hay Suscripciones, mostrar el mensaje
        $panel .= '
            <p>No hay Suscripciones para mostrar.</p>';
    } else {
        // Separar suscripciones activas e inactivas
        $activas = array_filter($rows, function($suscripcion) {
            return $suscripcion['Activo'] == 1;
        });
    
        $inactivas = array_filter($rows, function($suscripcion) {
            return $suscripcion['Activo'] == 0;
        });
    
        // Mostrar tabla de suscripciones activas
        $panel .= '
            <h3 class="mt-4 mb-3">Suscripciones Activas</h3>';
    
        if (!empty($activas)) {
            $panel .= '
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
        
            foreach ($activas as $Suscripcion) {
                $panel .= '
                    <tr>
                        <td>' . htmlspecialchars($Suscripcion['Codigo']) . '</td>
                        <td>' . htmlspecialchars($Suscripcion['Fecha']) . '</td>
                        <td>' . htmlspecialchars($Suscripcion['Correo']) . '</td>
                        <td>    
                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-primary btn-sm" onclick="edit(\'' . htmlspecialchars($Suscripcion['Codigo']) . '\')">Editar</button>
                                <button class="btn btn-danger btn-sm" onclick="del(\'' . htmlspecialchars($Suscripcion['Codigo']) . '\',\''. htmlspecialchars($Suscripcion['Correo']) .'\')">Eliminar</button>
                            </div>
                        </td>
                    </tr>';
            }
        
            $panel .= '
                    </tbody>
                </table>';
        } else {
            $panel .= '
                <p>No hay suscripciones activas.</p>';
        }
    
        // Mostrar tabla de suscripciones inactivas
        $panel .= '
            <h3 class="mt-5 mb-3">Suscripciones Inactivas</h3>';
    
        if (!empty($inactivas)) {
            $panel .= '
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';
        
            foreach ($inactivas as $Suscripcion) {
                $panel .= '
                    <tr>
                        <td>' . htmlspecialchars($Suscripcion['Codigo']) . '</td>
                        <td>' . htmlspecialchars($Suscripcion['Fecha']) . '</td>
                        <td>' . htmlspecialchars($Suscripcion['Correo']) . '</td>
                        <td>    
                            <div class="d-flex justify-content-center align-items-center">
                                <button class="btn btn-primary btn-sm" onclick="edit(\'' . htmlspecialchars($Suscripcion['Codigo']) . '\')">Editar</button>
                                <button class="btn btn-danger btn-sm" onclick="del(\'' . htmlspecialchars($Suscripcion['Codigo']) . '\',\''. htmlspecialchars($Suscripcion['Correo']) .'\')">Eliminar</button>
                            </div>
                        </td>
                    </tr>';
            }
        
            $panel .= '
                    </tbody>
                </table>';
        } else {
            $panel .= '
                <p>No hay suscripciones inactivas.</p>';
        }
    }

    $panel .= '</div>
    </section>
    <section>
        <div id="msjdiv" class="container d-none mt-5 position-relative col-6">
            <button class="btn btn-danger p-2 position-absolute" style="right: 0; top: 0;" onclick="closeDiv()">
                <svg class="closeicon-bg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                  <path d="M6.2253 4.81108C5.83477 4.42056 5.20161 4.42056 4.81108 4.81108C4.42056 5.20161 4.42056 5.83477 4.81108 6.2253L10.5858 12L4.81114 17.7747C4.42062 18.1652 4.42062 18.7984 4.81114 19.1889C5.20167 19.5794 5.83483 19.5794 6.22535 19.1889L12 13.4142L17.7747 19.1889C18.1652 19.5794 18.7984 19.5794 19.1889 19.1889C19.5794 18.7984 19.5794 18.1652 19.1889 17.7747L13.4142 12L19.189 6.2253C19.5795 5.83477 19.5795 5.20161 19.189 4.81108C18.7985 4.42056 18.1653 4.42056 17.7748 4.81108L12 10.5858L6.2253 4.81108Z" fill="white" />
                </svg>
             </button>
            <h2>Mandar Mensaje</h2>
            <form id="msjForm">
                <div class="mb-3">
                    <label for="asunto" class="form-label">Asunto del mensaje</label>
                    <input type="text" name="asunto" id="asunto" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="titulo" class="form-label">Titulo del mensaje</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="mensaje" class="form-label">Descripción</label>
                    <textarea name="mensaje" id="mensaje" class="form-control" required></textarea>
                </div>
                <span id="respuesta" class="text-danger"></span>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>

         </div>
         <div id="editdiv" class="container d-none mt-5 position-relative col-6">
            <button class="btn btn-danger p-2 position-absolute" style="right: 0; top: 0;" onclick="closeDiv()">
            <svg class="closeicon-bg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M6.2253 4.81108C5.83477 4.42056 5.20161 4.42056 4.81108 4.81108C4.42056 5.20161 4.42056 5.83477 4.81108 6.2253L10.5858 12L4.81114 17.7747C4.42062 18.1652 4.42062 18.7984 4.81114 19.1889C5.20167 19.5794 5.83483 19.5794 6.22535 19.1889L12 13.4142L17.7747 19.1889C18.1652 19.5794 18.7984 19.5794 19.1889 19.1889C19.5794 18.7984 19.5794 18.1652 19.1889 17.7747L13.4142 12L19.189 6.2253C19.5795 5.83477 19.5795 5.20161 19.189 4.81108C18.7985 4.42056 18.1653 4.42056 17.7748 4.81108L12 10.5858L6.2253 4.81108Z" fill="white" />
            </svg>
            </button>
            <h2>Detalle de Suscripcion</h2>
            <form id="editarForm">
                <div class="mb-3">
                    <label for="codigoS" class="form-label">Código</label>
                    <input type="text" id="codigoS" name="codigoS" class="form-control" value="Código" readonly>
                </div>
                <div class="mb-3">
                    <label for="fechaS" class="form-label">Fecha</label>
                    <input type="datetime-local" id="fechaS" name="fechaS" class="form-control" value="2023-10-01T12:00" readonly>
                </div>   
                <div class="mb-3">
                    <label for="correoS" class="form-label">Correo</label>
                    <input type="text" id="correoS" name="correoS" class="form-control" value="Correo" readonly>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" name="activoS" type="checkbox" id="activoS">
                    <label class="form-check-label" for="activoS">Activo</label>
                </div>
                <span id="respuestaS" class="text-danger"></span>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
         </div>
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
                        <input type="password" class="form-control" id="nuevaContrasena" name="nuevaContrasena" required>
                        </div>
                        <div class="mb-3">
                        <label for="confirmarContrasena" class="form-label">Confirmar nueva contraseña(opcional)</label>
                        <input type="password" class="form-control" id="confirmarContrasena" name="confirmarContrasena" required>
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
        'filas' => $rows,
        'b'=> $buscar,        
        'name0' => 'editarUsrForm',
        'name1' => 'rEditUser',
        'url1' => '../assets/php/UpdUsr.php',
        'name2' => 'msjForm',
        'url2' => '../assets/php/MsjSus.php',
        'name3' => 'editarForm',
        'url3' => '../assets/php/UpdSus.php',
        'url4' => '../assets/php/DelSus.php'
    ]);
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex->getMessage()
    ]);
    exit();
}
?>

