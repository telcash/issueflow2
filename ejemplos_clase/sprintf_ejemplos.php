<?php

/*
%s se refiere a string y se sustituye por el segundo argumento: Carlos.
%d se refiere a número entero, y se sustituye por 47
Si hay más de estos símbolos se sustituyen siguiendo ese orden
*/
$nombre = 'Carlos';
$edad = 47;
$ciudad = 'Madrid';
echo sprintf('Hola, soy %s y tengo %d años y vivo en %s, y por
                tener %2$d años soy viejo', $nombre, $edad, $ciudad);