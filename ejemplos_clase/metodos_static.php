<?php

class MiClase
{
    public function __construct(
        private string $name
    ) {}

    public function getName() {
        return $this->name;
    }

    public static function saludar() {
        return "Hola desde un método estático";
    }
}

echo MiClase::saludar();