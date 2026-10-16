<?php

declare(strict_types=1);

namespace App\Model;

/*
Definir con un enum las prioridades permitidas.
*/
enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    public function getLabel(): string
    {
        return match($this) {
            self::Open => 'Abierto',
            self::InProgress => 'En progreso',
            self::Closed => 'Cerrado'
        };
    }
    /* $this será el status que se quiere comparar,
    en este caso contra el valor constante del enum
    self::Open */
    public function isOpen():bool {
        return $this === self::Open;
    }
}