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
    public function show(string $id, Request $request) {

        // Se inicializa la variable $ticket como null, que contendrá el ticket encontrado.    
        $ticket = null;

        // Se recorre el array de tickets para buscar el ticket con el ID proporcionado.
        foreach(self::TICKETS as $candidate) {
            if ($candidate['id'] === $id) {
                $ticket = $candidate;
                break;
            }
        }

        // En este punto del código, si $ticket sigue siendo null, significa que no se encontró ningún ticket con el ID proporcionado.
        // Si es null, voy a lanzar un error / excepción 404: Not found
        if ($ticket === null) {
            //throw $this->createNotFoundException('La incidencia no existe');
            $html = <<<HTML
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Ticket no existe</title>
                </head>
                <body>
                    <h1>Ups... la página solicitada no existe</h1>
                    <h2>************ Error 404 ************</h2>
                    <p><a href="/tickets">Ver incidencias</a></p>
                </body>
                </html>
            HTML;

            return new Response($html, Response::HTTP_NOT_FOUND);
        }

        /* Voy a chequear si tengo parámetros en la petición / request (query)
        Esto recupera el valor de view si existe en la URL:
        http://localhost:8000/tickets/INC-1001?view=compact 
        Si no existe, el asigna a $view el valor por defecto */
        $view = $request->query->get('view', 'full');

        if ($view === "compact") {
            $html = <<<HTML
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Ticket {$id}</title>
                </head>
                <body>
                    <h1>Ticket {$id}</h1>
                </body>
                </html>
            HTML;
        } else {
            $html = <<<HTML
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Ticket {$id}</title>
                </head>
                <body>
                    <h1>Ticket {$id}</h1>
                    <p>Title: {$ticket['title']}</p>
                    <p>Priority: {$ticket['priority']}</p>
                </body>
                </html>
            HTML;
        }
        return new Response($html, Response::HTTP_OK);
    }
}
