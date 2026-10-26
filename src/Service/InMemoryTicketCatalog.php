<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use App\Contract\TicketCatalogInterface;
use App\Entity\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;

#[AsAlias(TicketCatalogInterface::class)]
final class InMemoryTicketCatalog implements TicketCatalogInterface
{
    private array $tickets;

    public function __construct() {
        /* De momento seguimos usando datos en memoria; solo cambia la
        responsabilidad del objeto que los mantiene. */
        $this->tickets = [
            new Ticket(
                code: 'INC-1001',
                title: 'No puedo iniciar sesión',
                priority: TicketPriority::Urgent,
                status: TicketStatus::Open,
            ),
            new Ticket(
                code: 'INC-1002',
                title: 'Error en la factura',
                priority: TicketPriority::High,
                status: TicketStatus::InProgress,
            ),
            new Ticket(
                code: 'INC-1003',
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

    /* find() retorna un ticket dado su $code o null
    si no existe */
    public function findByCode(string $code): ?Ticket {
        return array_find(
            $this->tickets,
            fn (Ticket $ticket): bool => $ticket->getCode() === $code // Función flecha
        );
    }
}