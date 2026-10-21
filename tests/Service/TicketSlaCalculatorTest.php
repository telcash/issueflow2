<?php

declare(strict_types=1); 

namespace App\Tests\Service;

use App\Model\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use App\Service\TicketSlaCalculator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class TicketSlaCalculatorTest extends TestCase
{
    public static function priorityProvider(): iterable
    {
        /* yield emite cada uno de los casos */
        yield 'urgent' => [TicketPriority::Urgent, 4];
        yield 'high' => [TicketPriority::High, 8];
        yield 'normal' => [TicketPriority::Normal, 24];
        yield 'low' => [TicketPriority::Low, 72];
    }

    #[Test]
    #[DataProvider('priorityProvider')]
    public function it_calculates_the_response_target_for_each_priority(
        TicketPriority $priority,
        int $expectedHours
    ): void
    {
        /* Creo un ticket para la prueba del calculo de horas según prioridad */
        $ticket = new Ticket (
            id: 'INC-2001',
            title: 'Incidencia de prueba',
            priority: $priority,
            status: TicketStatus::InProgress
        );

        /* Instancio un TicketSlaCalculator para poder llamar al método
        getResponseTargetHours() */
        $calculator = new TicketSlaCalculator();

        /* Calculo la cantidad de horas correspondientes al ticket
        usando el método getResponseTargetHours */
        $hours = $calculator->getResponseTargetHours($ticket);

        /* Compruebo si las horas calculadas son iguales al valor esperado */
        self::assertSame($expectedHours, $hours);
    }
}