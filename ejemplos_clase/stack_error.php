<?php

declare(strict_types=1);

function nivel1(): void// la función no retorna nada
{
    nivel2();
}

function nivel2(): void
{
    try {
        nivel3();
    } catch (Throwable $throwable) {
        echo "El error fue capturado en nivel 2" . PHP_EOL;
    }
}

function nivel3(): void
{
    throw new RuntimeException('Aquí hubo un fallo');
}

try {
    nivel1();
} catch (Throwable $throwable) {
    echo "Aquí sucedió un error" . PHP_EOL;
} finally {
    // Se ejecuta siempre, haya o no error
    echo "Bloque finally";
}