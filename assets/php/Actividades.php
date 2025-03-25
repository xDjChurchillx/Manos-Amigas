<?php
require '../../../Private/Credentials/DataBase/connection.php';
header('Content-Type: application/json; charset=UTF-8');
try{
    $buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';
    // sanitizar
    $buscar = trim($buscar);
    $buscar = filter_var($buscar, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    $buscar = htmlspecialchars($buscar, ENT_QUOTES, 'UTF-8');


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
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        // Verificar si existe el campo Visible y su valor es 0
        if (isset($row['Visible']) && $row['Visible'] == 0) {
            continue; // Saltar este registro (no se agrega al array)
        }
        
        // Si el registro es visible, eliminamos solo la columna Visible
        if (isset($row['Visible'])) {
            unset($row['Visible']);
        }
        
        $rows[] = $row;
    }

    // Si pasa todas las validaciones, se puede mostrar el contenido
    echo json_encode([
        'status' => 'success',
        'filas' => $rows,
        'b'=> $buscar
    ]);
} catch (Exception $ex) {
     echo json_encode([
        'status' => 'error',
         'ex' => $ex
    ]);
    exit();
}
?>
