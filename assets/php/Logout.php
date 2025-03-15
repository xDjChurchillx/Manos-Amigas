<?php
session_start();
setcookie("token", "", time() - 3600, "/");
session_unset(); // Limpia variables de sesión
session_destroy(); // Elimina la sesión
header("Location: ../../index.html");
exit();
?>