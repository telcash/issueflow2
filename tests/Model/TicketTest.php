<?php

declare(strict_types=1);

namespace App\Tests\Model;

use App\Model\Ticket;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TicketTest extends TestCase
{
    /* 
    Puedo realizar varios tests unitarios para la class Ticket.
    En general un test unitario tiene la siguiente estructura:
     - Arrange: ¿Qué necesito preparar? - Contexto del tests
     - Act: ¿Qué comportamiento ejecuto?
     - Assert: ¿Qué resultado debe cumplirse?
    */
     #[Test]
     public function it_normalizes_the_title_when_the_ticket_is_created(): void
     {
        $ticket = new Ticket(
            id: 'INC-1001',
            title: '            Mi ordenador no enciende          ',
            priority: TicketPriority::Urgent,
            status: TicketStatus::Open
        );
        /*
        Assert: verificamos el comportamiento observable esperado
        1er argumento: que resultado espero yo obtener
        */
        self::assertSame('Mi ordenador no enciende', $ticket->getTitle());
     }

     #[Test]
     public function it_rejects_an_invalid_identifier(): void
     {
        $this->expectException(InvalidArgumentException::class);
        new Ticket(
            id: 'Ticket0001',
            title: 'Incidencia válida',
            priority: TicketPriority::Low,
            status: TicketStatus::InProgress
        );
     }

     #[Test]
     public function it_rejects_an_empty_title(): void
     {
        $this->expectException(InvalidArgumentException::class);
        new Ticket(
            id: 'INC-0001',
            title: '',
            priority: TicketPriority::Low,
            status: TicketStatus::InProgress
        );
     }

     #[Test]
     public function it_reports_when_a_ticket_is_urgent(): void
     {
        $ticket = new Ticket(
            id: 'INC-1003',
            title: 'Servidor no disponible',
            priority: TicketPriority::Urgent,
            status: TicketStatus::Open
        );
        self::assertTrue($ticket->isUrgent());
     }
}

/* 
TDD: Test Driven Development
Para una implementación:
1ro: Escribo los tests: me van a servir como un listado
de las funcionalidades de lo quiero implementar. Me dicen como debe
funcionar mi implementación para los distintos casos posibles. Este es el
momento de imaginarme todos los escenarios posibles.
En principio todos los tests van a resultar en Fallas: están todos en rojo
2do: Escribo el código mínimo (calidad) que haga pasar los tests: los tests van a 
estar en verde
3ro: Refactor -> mejoras del código
4to: Todo en verde y el código optimizado
*/