<?php
session_start();
session_unset(); // Limpia variables de sesión
session_destroy(); // Elimina la sesión
header("Location: index.html");
exit();
?>