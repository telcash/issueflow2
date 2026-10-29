<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Ticket;
use App\Contract\TicketCatalogInterface;
use App\Contract\TicketSlaCalculatorInterface;
use Psr\Log\LoggerInterface;
use App\Form\TicketType;
use App\Form\Model\TicketCreateData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use InvalidArgumentException;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;

// Mi clase TicketController hereda de AbstractController de Symfony
final class TicketController extends AbstractController
{
    /* Inyección de dependencias */
    public function __construct(
        private readonly TicketCatalogInterface $ticketCatalog,
        private readonly TicketSlaCalculatorInterface $ticketSlaCalculator,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
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

    /**
     * GET: muestra el formulario vacío.
     * POST: Recibe el formulario cuando se envíe con sus valores,
     * estos se procesan y se persisten en la base de datos si son correctos.
     * Los datos del formulario vienen en el objeto Request
     */
    #[Route('/tickets/new', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // Crear un objeto TicketCreateData para recibir la información del formulario
        $data = new TicketCreateData();

        /*
            Creamos un formulario con el método createForm() heredado de AbstractController
            1er parámetro corresponde a la clase que define el formulario
            2do parámetro corresponde a el objeto (DTO) que recibe la información del formulario
        */
        $form = $this->createForm(TicketType::class, $data);

        /*
            handleRequest() hace lo siguiente:
            - Detectar el envío del formulario
            - Extraer los datos correspondientes del request
            - Transformar los valores al tipo esperado
            - Asigna los datos al objeto TicketCreateData
        */
        $form->handleRequest($request);

        /* 
            Chequeo si el estado del formulario es enviado y además si el formulario es válido.
            isValid() puede ejecutar las reglas de validación definidas en el DTO TicketCreateData
        */
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Construimos la entidad ticket con los datos ya validados del DTO
                $ticket = new Ticket(
                    code: $data->code,
                    title: $data->title,
                    priority: $data->priority,
                    status: $data->status
                );
                // Las relaciones son opcionales y se asignan mediante la API de Ticket
                $ticket->setCategory($data->category);
                $ticket->assignTo($data->assignee);
            } catch (InvalidArgumentException $exception) {
                /*
                    Si hay un error lo agregamos al formulario, y volvemos a cargar
                    la plantilla del formulario
                */
                $form->addError(
                    new FormError($exception->getMessage())
                );
                return $this->render('ticket/new.html.twig', [
                    'form' => $form,
                ]);
            }
            // Solo llegamos aquí si el Ticket se construyó correctamente
            // Usamos el entityManager para persistir el ticket en la base de datos
            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            // Luego de guardar el ticket, redirigimos a la página de detalle de ticket
            return $this->redirectToRoute('app_ticket_show', [
                'code' => $ticket->getCode()
            ]);
        }
        // Si el formulario no fue enviado o no es válido, es un GET, mostramos el formulario
        return $this->render('ticket/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Ruta dinámica para mostrar un ticket específico por su ID.
    #[Route('/tickets/{code}', name:"app_ticket_show", methods: ['GET'])]
    // El método que devuelve un ticket en particular por convención REST es show().
    // El parámetro $code identifica la ruta del ticket particular
    public function show(string $code): Response {

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