<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Ticket;
use App\Model\TicketPriority;
use App\Contract\TicketSlaCalculatorInterface;

/* Crear un servicio que se encargue de asignar a los distintos
tipos de priority de un ticket, un valor en horas de la siguiente manera:
Los tickets Urgent 4 horas, High 8 horas, Normal 24 horas, Low 72 horas.
El servicio solo va a tener un metodo getResponseTargetHours() que recibe
como argumento un Ticket y retorna el número de horas adecuado */
final class TicketSlaCalculator implements TicketSlaCalculatorInterface
{
    public function getResponseTargetHours (Ticket $ticket): int {
        return match($ticket->getPriority()) {
            TicketPriority::Urgent => 4,
            TicketPriority::High => 8,
            TicketPriority::Normal => 24,
            TicketPriority::Low => 72
        };
    }
}