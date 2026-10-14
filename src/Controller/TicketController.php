<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// Mi clase TicketController hereda de AbstractController de Symfony
final class TicketController extends AbstractController
{
    /* En este ejemplo, los tickets se definen de forma estática. 
        En un caso real, se obtendrían de una base de datos. */

    /* TICKETS es un array donde cada elemento es un ticket en particular
    Cada ticket es un array asociativo, que contiene los datos o propiedades de este. */
    // Esto es una constante de clase, que se puede acceder desde cualquier método de la clase TicketController.
    private const TICKETS = [
        ['id' => 'INC-1001', 'title' => 'No puedo iniciar sesión', 'priority' => 'urgent'],
        ['id' => 'INC-1002', 'title' => 'Error en la factura', 'priority' => 'high'],
        ['id' => 'INC-1003', 'title' => 'Actualizar datos de contacto', 'priority' => 'normal'],
    ];

    #[Route('/tickets', name: 'app_ticket_index', methods: ['GET'])]
    // El método que devuelve la lista de tickets por convención REST es index().
    public function index(): Response {
        return $this->render('ticket/index.html.twig', [
            // Las claves del arreglo se convierten en las variables que espera Twig
            'title' => 'Incidencias',
            'tickets' => self::TICKETS
        ]);
    }

    // Ruta dinámica para mostrar un ticket específico por su ID.
    #[Route('/tickets/{id}', name:"app_ticket_show", methods: ['GET'])]
    // El método que devuelve un ticket en particular por convención REST es show().
    // El parámetro $id identifica la ruta del ticket particular
    public function show(string $id) {

        /* La función array_find (PHP 8.4+) busca el $ticket en el arreglo TICKETS */
        $ticket = array_find(
            self::TICKETS,
            fn (array $ticket): bool => $ticket['id'] === $id // Función flecha
        );

        if ($ticket === null) {
            throw $this->createNotFoundException(
                "No existe la incidencia " . $id
            );
        }
        /* Si llego a está linea es porque no se lanzó el error, por lo
        tanto, el $ticket no es NULL */

        /* Envío a la plantilla correspondiente los datos
        del ticket encontrado: $ticket */
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket
        ]);
    }
}








