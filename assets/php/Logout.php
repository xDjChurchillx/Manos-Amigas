<?php
session_start();
session_unset(); // Limpia variables de sesi�n
session_destroy(); // Elimina la sesi�n
header("Location: index.html");
exit();
?>