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
    'html' => '
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
<div class="container">
	<div class="row">
	    <br/>
	   <div class="col text-center">
		<h2>Bootstrap 4 counter</h2>
		<p>counter to count up to a target number</p>
		</div>
		
             
		
	</div>
		<div class="row text-center">
	        <div class="col">
	        <div class="counter">
      <i class="fa fa-code fa-2x"></i>
      <h2 class="timer count-title count-number" data-to="100" data-speed="1500"></h2>
       <p class="count-text ">Our Customer</p>
    </div>
	        </div>
              <div class="col">
               <div class="counter">
      <i class="fa fa-coffee fa-2x"></i>
      <h2 class="timer count-title count-number" data-to="1700" data-speed="1500"></h2>
      <p class="count-text ">Happy Clients</p>
    </div>
              </div>
              <div class="col">
                  <div class="counter">
      <i class="fa fa-lightbulb-o fa-2x"></i>
      <h2 class="timer count-title count-number" data-to="11900" data-speed="1500"></h2>
      <p class="count-text ">Project Complete</p>
    </div></div>
              <div class="col">
              <div class="counter">
      <i class="fa fa-bug fa-2x"></i>
      <h2 class="timer count-title count-number" data-to="157" data-speed="1500"></h2>
      <p class="count-text ">Coffee With Clients</p>
    </div>
              </div>
         </div>
</div>
'
]);
?>
