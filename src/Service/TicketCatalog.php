<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;

final class TicketCatalog{
    private array $tickets;

    public function __construct() {
        /* De momento seguimos usando datos en memoria; solo cambia la
        responsabilidad del objeto que los mantiene. */
        $this->tickets = [
            new Ticket(
                id: 'INC-1001',
                title: 'No puedo iniciar sesión',
                priority: TicketPriority::Urgent,
                status: TicketStatus::Open,
            ),
            new Ticket(
                id: 'INC-1002',
                title: 'Error en la factura',
                priority: TicketPriority::High,
                status: TicketStatus::InProgress,
            ),
            new Ticket(
                id: 'INC-1003',
                title: 'Actualizar datos de contacto',
                priority: TicketPriority::Normal,
                status: TicketStatus::Closed,
            ),
        ];
    }

    /* all() retorna el listado de los tickets */
    public function all(): array {
        return $this->tickets;
    }

    /* find() retorna un ticket dado su $id o null
    si no existe */
    public function find(string $id): ?Ticket {
        return array_find(
            $this->tickets,
            fn (Ticket $ticket): bool => $ticket->getId() === $id // Función flecha
        );
    }
}