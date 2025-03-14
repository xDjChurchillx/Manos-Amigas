<?php
header('Content-Type: application/json');

// Simular "base de datos" de servicios/actividades
$activitys = [
    [
        "id" => 1,
        "title" => "Tour en Monta�a",
        "description" => "Disfruta de una caminata por la monta�a con gu�as expertos.",
        "cardImage" => "assets/images/montana.jpg",
        "image" => "assets/images/montana_grande.jpg",
        "details" => [
            "Gu�as certificados",
            "Duraci�n de 3 horas",
            "Incluye refrigerio",
            "Paisajes espectaculares"
        ]
    ],
    [
        "id" => 2,
        "title" => "Paseo en Kayak",
        "description" => "Navega por los r�os y disfruta de la naturaleza.",
        "cardImage" => "assets/images/kayak.jpg",
        "image" => "assets/images/kayak_grande.jpg",
        "details" => [
            "Equipos incluidos",
            "Gu�as profesionales",
            "Apto para principiantes",
            "Seguro incluido"
        ]
    ],
    // Agrega m�s actividades seg�n sea necesario
];

// Devolver como JSON
echo json_encode(["activitys" => $activitys], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>