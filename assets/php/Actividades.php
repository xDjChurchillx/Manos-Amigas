<?php
header('Content-Type: application/json');

// Simular "base de datos" de servicios/actividades
$activitys = [
    [
        "id" => 1,
        "title" => "Tour en Montaña",
        "description" => "Disfruta de una caminata por la montaña con guías expertos.",
        "cardImage" => "assets/images/montana.jpg",
        "image" => "assets/images/montana_grande.jpg",
        "details" => [
            "Guías certificados",
            "Duración de 3 horas",
            "Incluye refrigerio",
            "Paisajes espectaculares"
        ]
    ],
    [
        "id" => 2,
        "title" => "Paseo en Kayak",
        "description" => "Navega por los ríos y disfruta de la naturaleza.",
        "cardImage" => "assets/images/kayak.jpg",
        "image" => "assets/images/kayak_grande.jpg",
        "details" => [
            "Equipos incluidos",
            "Guías profesionales",
            "Apto para principiantes",
            "Seguro incluido"
        ]
    ],
    // Agrega más actividades según sea necesario
];

// Devolver como JSON
echo json_encode(["activitys" => $activitys], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>