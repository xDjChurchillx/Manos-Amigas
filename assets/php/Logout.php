<?php
session_start();
setcookie("token", "", time() - 3600, "/");
session_unset(); // Limpia variables de sesi�n
session_destroy(); // Elimina la sesi�n
header("Location: ../../index.html");
exit();
?>