<?php

// Ejemplo de scope (ámbito) en PHP
function example() {
    // Variable $local, solo accesible dentro de esta función
    $local = "Soy una variable local";
    echo $local;
}

example();

// Esto causará un error, ya que $local no está definida en este scope
echo $local;