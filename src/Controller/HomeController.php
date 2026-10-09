<?php

declare(strict_types= 1);

namespace App\Controller;

// Clase de symfony para construir las respuestas para el cliente
use Symfony\Component\HttpFoundation\Response;
// Clase de symfony para definir las rutas
use Symfony\Component\Routing\Attribute\Route;
/* Template: Clase de symfony para asociar un método del controlador con una plantilla Twig,
   de manera que el método devuelva un array con los datos a renderizar en la plantilla 
    Lo necesitamos para poder usar la anotación #[Template('ruta/plantilla.html.twig')] 
    en el método del controlador, y que Twig renderice la plantilla con los datos 
    devueltos por el método
*/
//use Symfony\Bridge\Twig\Attribute\Template;

/* Para poder usar el método render() de la clase AbstractController,
   necesitamos primero importar la clase AbstractController, 
   y luego extender nuestra clase HomeController de ella.
*/
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/* Clase controlador HomeController, extiende de AbstractController para 
poder usar el método render() */
final class HomeController extends AbstractController {

    /* Definimos una ruta para este controlador.
    La ruta será la de home: '/' -> 1er argumento
    Tendrá un nombre interno: 'app_home' -> 2do argumento
    Sólo para métodos GET -> 3er argumento 
    Luego de la ruta, definimos el método correspondiente a ella,
    el cual genera la respuesta para el cliente, que será
    un objeto de la clase Response
    */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    /* Comentado el #[Template('home/index.html.twig')] porque no lo necesitamos, 
    ya que estamos usando el método render() de la clase AbstractController */
    //#[Template('home/index.html.twig')]
    public function index(): Response { // En caso de usar #[Template('...')], el tipo de retorno sería array
        /* Retorno un objeto Response a través del método render().
        El primer argumento de render corresponde a la plantilla, 
        y el segundo al array de datos */
        return $this->render('home/index.html.twig', [
            'projectName' => 'IssueFlow',
            'message' => 'Symfony 8.1 ha resuelto la ruta y Twig ha construido la vista'
        ]);
    }

    /* Definimos una segunda ruta para este controlador.
    La ruta será la de /health: '/health' -> 1er argumento
    Tendrá un nombre interno: 'app_health' -> 2do argumento
    Sólo para métodos GET -> 3er argumento 
    Luego de la ruta, definimos el método correspondiente a ella,
    el cual genera la respuesta para el cliente, que será
    un objeto de la clase Response
    */
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function health(): Response {
        // Especifico que el contenido es text/plain y no un documento html
        return new Response('IssueFlow OK', Response::HTTP_OK, ['Content-Type' => 'text/plain']);
    }

    /* Estructura de una ruta con su método correspondiente:

    #[Route('/ruta', name: 'app_ruta', methods: ['GET'])]
    public function ruta(): Response {
        Aquí la lógica de la ruta
        ...
        ...
        ...
        
        return new Response('Contenido', 200);
    }

    */
}