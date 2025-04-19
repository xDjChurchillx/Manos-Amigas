<?php
// Configuracion de Base de datos 
require '../../../Private/Credentials/DataBase/connection.php';
header('Content-Type: application/json; charset=UTF-8');
try{
    $buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';
    // sanitizar
    $buscar = trim($buscar);
    $buscar = filter_var($buscar, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $buscar = htmlspecialchars($buscar, ENT_QUOTES, 'UTF-8');

    //Obtener actividades de la base de datos
    $stmt = $conn->prepare("CALL sp_ListarActividades(?)");
    $stmt->bind_param('s', $buscar);
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
        // Verificar si la actividad es visible
        if (isset($row['Visible']) && $row['Visible'] == 0) {
            continue; // Salta el registro en caso de que no sea visible
        }
        
        // Si el registro es visible, eliminamos solo la columna Visible
        if (isset($row['Visible'])) {
            unset($row['Visible']);
        }
        
        $rows[] = $row;
    }

    //Retorno de todos los valores necesarios para el panel
    echo json_encode([
        'status' => 'success',
        'actividades' => $rows,
        'b'=> $buscar
    ]);
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => 'error en actividades php'
    ]);
    exit();
}
?>
