<?php

$url = 'http://localhost:80/Api/apiAlumno';

$data = [
    'nombre' => 'Prueba',
    'apellido1' => 'Test',
    'apellido2' => 'Ejemplo',
    'direccion' => 'Calle Falsa 123',
    'edad' => 25,
    'curriculum_url' => 'https://ejemplo.com/cv/prueba',
];


$ch = curl_init($url);


curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));


$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo 'Error en curl: ' . curl_error($ch);
} else {
    echo "HTTP Status: $httpcode\n";
    echo "Respuesta:\n$response\n";
}

curl_close($ch);