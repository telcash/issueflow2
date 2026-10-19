<?php

declare(strict_types=1);

namespace App\Contract;

Use App\Model\Ticket;

/*
Contrato que necesita IssueFlow de cualquier catálogo de tickets.
la interfaz describe que operaciones deben existir, pero no como
se realizan.
*/
interface TicketCatalogInterface
{
    // Método que devuelve todas las incidencias
    public function all(): array;

    // Método que devuelve una incidencia dado su id, ni no encuentra devuelve null
    public function find(string $id): ?Ticket;
}