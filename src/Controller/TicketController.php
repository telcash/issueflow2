<?php

declare(strict_types=1);

namespace App\Controller;
use App\Contract\TicketCatalogInterface;
use App\Contract\TicketSlaCalculatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Mi clase TicketController hereda de AbstractController de Symfony
final class TicketController extends AbstractController
{
    /* Inyección de dependencias */
    public function __construct(
        private readonly TicketCatalogInterface $ticketCatalog,
        private readonly TicketSlaCalculatorInterface $ticketSlaCalculator,
        private readonly LoggerInterface $logger
    ) {}

    #[Route('/tickets', name: 'app_ticket_index', methods: ['GET'])]
    // El método que devuelve la lista de tickets por convención REST es index().
    public function index(): Response {
        return $this->render('ticket/index.html.twig', [
            // Las claves del arreglo se convierten en las variables que espera Twig
            'title' => 'Incidencias',
            'tickets' => $this->ticketCatalog->all()
        ]);
    }

    // Ruta dinámica para mostrar un ticket específico por su ID.
    #[Route('/tickets/{code}', name:"app_ticket_show", methods: ['GET'])]
    // El método que devuelve un ticket en particular por convención REST es show().
    // El parámetro $code identifica la ruta del ticket particular
    public function show(string $code) {

        /* La función array_find (PHP 8.4+) busca el $ticket en el arreglo TICKETS */
        $ticket = $this->ticketCatalog->findByCode($code);

        if ($ticket === null) {
            /* Usamos logger para conservar en el log información
            sobre este error */
            $this->logger->warning("Se solicitó una incidencia inexistente.", [
                'ticket_code' => $code,
                'route' => 'app_ticket_show'
            ]);

            /* Lanzamos el error, no lo manejamos aquí, dejamos
            que Simfony lo capture y lo transforme en una página 404 */
            throw $this->createNotFoundException(
                "No existe la incidencia " . $code
            );
        }
        /* Si llego a está linea es porque no se lanzó el error, por lo
        tanto, el $ticket no es NULL */

        /* Envío a la plantilla correspondiente los datos
        del ticket encontrado: $ticket */
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'responseTargetHours' => $this->ticketSlaCalculator->getResponseTargetHours($ticket)
        ]);
    }
}








