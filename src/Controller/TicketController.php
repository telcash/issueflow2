<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TicketController
{
    #[Route('/tickets', name: 'app_ticket_index', methods: ['GET'])]
    // El método que devuelve la lista de tickets por convención REST es index().
    public function index(): Response {

        /* En este ejemplo, los tickets se definen de forma estática. 
        En un caso real, se obtendrían de una base de datos. */

        /* $tickets es un array donde cada elemento es un ticket en particular
        Cada ticket es un array asociativo, que contiene los datos o propiedades de este. */
        $tickets = [
            ['id' => 'INC-1001', 'title' => 'No puedo iniciar sesión', 'priority' => 'urgent'],
            ['id' => 'INC-1002', 'title' => 'Error en la factura', 'priority' => 'high'],
            ['id' => 'INC-1003', 'title' => 'Actualizar datos de contacto', 'priority' => 'normal'],
        ];

        // Se inicializan las variables $items, $title y $total que contendrán los <li> de cada incidencia, el título y el total respectivamente.
        $items = '';
        $title = 'Incidencias';
        $total = count($tickets);

        foreach ($tickets as $ticket) {
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

        // TODO 2: construir el HTML final con un H1, total, UL y enlace a /.
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

        // TODO 3: devolver una Response HTTP 200.
        return new Response($html, Response::HTTP_OK);
    }
}
