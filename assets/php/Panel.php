<?php
// Repetimos la misma configuración de sesión para asegurar consistencia
ini_set('session.use_only_cookies', 1);

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
if (!isset($_SESSION["username"]) ||
    $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] ||
    $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    // No autenticado o sesión alterada
    session_unset();
    session_destroy();
    
    // Retornar JSON con error
    echo json_encode([
        'status' => 'error',
        'redirect' => '/Gestion/ingreso.html?error=5'
    ]);
    exit();
}

// Si pasa todas las validaciones, se puede mostrar el contenido
echo json_encode([
    'status' => 'success',
    'html' => '<section id="counter" class="counter">
            <div class="main_counter_area">
                <div class="overlay p-y-3">
                    <div class="container">
                        <div class="row">
                            <div class="main_counter_content text-center white-text wow fadeInUp">
                                <div class="col-md-3">
                                    <div class="single_counter p-y-2 m-t-1">
                                        <i class="fa fa-briefcase m-b-1"></i>
                                        <h2 class="statistic-counter">200</h2>
                                        <span></span>
                                        <p>Study</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="single_counter p-y-2 m-t-1">
                                        <i class="fa fa-check m-b-1"></i>
                                        <h2 class="statistic-counter">1000</h2>
                                        <p>Checked</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="single_counter p-y-2 m-t-1">
                                        <i class="fa fa-coffee m-b-1"></i>
                                        <h2 class="statistic-counter">500</h2>
                                        <p>Coffee </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="single_counter p-y-2 m-t-1">
                                        <i class="fa fa-beer m-b-1"></i>
                                        <h2 class="statistic-counter">400</h2>
                                        <p>Pizzas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>'
]);
?>
