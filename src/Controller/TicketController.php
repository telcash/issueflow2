<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TicketController
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
        // Se inicializan las variables $items, $title y $total que contendrán los <li> de cada incidencia, el título y el total respectivamente.
        $items = '';
        $title = 'Incidencias';
        // self::TICKETS hace referencia a la constante de clase TICKETS, que contiene el array de incidencias.
        $total = count(self::TICKETS);

        foreach (self::TICKETS as $ticket) {
            $items .= "<li>{$ticket['id']}: {$ticket['title']} ({$ticket['priority']})</li>";
        }
        /*
        Esto es lo que sucede en cada iteración del bucle foreach:
        Inicialmente,
        $items = '';
        Primer ticket del ciclo foreach:
        $items = '<li>INC-1001: No puedo iniciar sesión (urgent)</li>';
        Segundo ticket del ciclo foreach:
        $items = '<li>INC-1001: No puedo iniciar sesión (urgent)</li><li>INC-1002: Error en la factura (high)</li>';
        Tercer ticket del ciclo foreach:
        $items = '<li>INC-1001: No puedo iniciar sesión (urgent)</li><li>INC-1002: Error en la factura (high)</li><li>INC-1003: Actualizar datos de contacto (normal)</li>'    
        */


        $html = <<<HTML
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$title}</title>
            </head>
            <body>
                <h1>{$title}</h1>
                <p>Total de incidencias: {$total}</p>
                <ul>
                    {$items}
                </ul>
                <p><a href="/">Volver a la página principal</a></p>
            </body>
            </html>
        HTML;

        return new Response($html, Response::HTTP_OK);
    }

    // Ruta dinámica para mostrar un ticket específico por su ID.
    #[Route('/tickets/{id}', name:"app_ticket_show", methods: ['GET'])]
    // El método que devuelve un ticket en particular por convención REST es show().
    public function show(string $id) {

        // Se inicializa la variable $ticket como null, que contendrá el ticket encontrado.    
        $ticket = null;

        // Se recorre el array de tickets para buscar el ticket con el ID proporcionado.
        foreach(self::TICKETS as $candidate) {
            if ($candidate['id'] === $id) {
                $ticket = $candidate;
                break;
            }
        }

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

        return new Response($html, Response::HTTP_OK);
    }
}
