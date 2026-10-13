<?php

$calificaciones = [5.5, 4.5, 9.8, 7.7];

// Buscar la primera calificación mayor de 7
$calificacion = array_find(
    $calificaciones,
    fn(float $calificacion): bool => $calificacion === 10
);

var_dump($calificacion);