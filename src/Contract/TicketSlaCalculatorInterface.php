<?php

declare(strict_types=1);

namespace App\Contract;

Use App\Entity\Ticket;

interface TicketSlaCalculatorInterface
{
    public function getResponseTargetHours(Ticket $ticket): int;
}