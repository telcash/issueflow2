<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contract\TicketSlaCalculatorInterface;
use App\Entity\Category;
use App\Entity\Ticket;
use App\Form\Model\TicketSearchCriteria;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class ApiTicketController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TicketRepository $ticketRepository,
        private readonly TicketSlaCalculatorInterface $ticketSlaCalculator,
    ) {}

    /**
     * API: devuelve el listado de tickets
     */
    #[Route('/api/tickets', name: 'app_api_ticket_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        // URL: /tickets?query=fallo&status=open&priority=urgent&page=1
        $criteria = new TicketSearchCriteria();

        // Extraer el valor de query desde la url
        $criteria->query = trim($request->query->getString('query'));

        // Extraer el valor de priority desde la url
        $priorityValue = trim($request->query->getString('priority'));

        // Los enum recibidos por URL son texto, tryFrom() permite validar
        // sin lanzar una excepción si el cliente envía un valor desconocido
        if ($priorityValue !== '') {
            $priority = TicketPriority::tryFrom($priorityValue);
            
            if ($priority === null) {
                return $this->json(
                    [
                        'error' => [
                            'code' => 'invalid_priority',
                            'message' => 'La prioridad indicada no es válida'
                        ]
                    ],
                    Response::HTTP_BAD_REQUEST,
                );
            }
            $criteria->priority = $priority;
        }

        // Extraer el valor de status desde la url
        $statusValue = trim($request->query->getString('status'));

        // Los enum recibidos por URL son texto, tryFrom() permite validar
        // sin lanzar una excepción si el cliente envía un valor desconocido
        if ($statusValue !== '') {
            $status = TicketStatus::tryFrom($statusValue);
            
            if ($status === null) {
                return $this->json(
                    [
                        'error' => [
                            'code' => 'invalid_status',
                            'message' => 'El estado indicado no es válido'
                        ]
                    ],
                    Response::HTTP_BAD_REQUEST,
                );
            }
            $criteria->status = $status;
        }

        // Extraer el valor de category de la url
        $categoryValue = trim($request->query->getString('category'));

        if ($categoryValue !== '') {
            if (filter_var($categoryValue, FILTER_VALIDATE_INT) === false || (int) $categoryValue < 1) {
                return $this->json(
                    [
                        'error' => [
                            'code' => 'invalid_category',
                            'message' => 'La categoría indicada no es válida'
                        ],
                    ],
                    Response::HTTP_BAD_REQUEST,
                );
            }
            // Busco la categoría en la base de datos, para ver si existe
            // NOTA: Lo ideal acá sería crear CategoryRepository
            $category = $this->entityManager
                ->getRepository(Category::class)
                ->find((int) $categoryValue);
            
            if ($category === null) {
                return $this->json(
                    [
                        'error' => [
                            'code' => 'invalid_category',
                            'message' => 'La categoría indicada no existe'
                        ],
                    ],
                    Response::HTTP_BAD_REQUEST,
                );
            }
            $criteria->category = $category;
        }

        $page = max(1, $request->query->getInt('page', 1));

        $ticketPage = $this->ticketRepository->search(
            criteria: $criteria,
            page: $page,
            pageSize: 4
        );

        // $ticketPage es un arreglo de entidades Ticket
        // array_map va a transformar / mapear cada elemento de ese arreglo usando la función ticketToArray
        // Cada elemento mapeado se va guardando en $items
        $items = array_map(
            fn (Ticket $ticket): array => $this->ticketToArray($ticket),
            $ticketPage->getItems()
        );

        return $this->json([
            'tickets' => $items
        ]);
    }

    /**
     * API: Devuelve un ticket concreto por su código de negocio
     */
    #[Route('/api/tickets/{code}', name: 'app_api_ticket_show', methods: ['GET'])]
    public function show(string $code): JsonResponse
    {
        // Verifico el formato de code, si es inválido respondo con el error 400
        if (preg_match('/^INC-\d{4}$/', $code) !== 1) {
            return $this->json(
                [
                    'error' => [
                        'code' => 'invalid_ticket_code',
                        'message' => 'El código debe tener el formato INC-0000'
                    ],
                ],
                Response::HTTP_BAD_REQUEST,
            );
        }

        // Busco el ticket solicitado
        $ticket = $this->ticketRepository->findByCode($code);

        // Si no existe respondo con el error correspondiente: 404
        if ($ticket === null) {
            return $this->json(
                [
                    'error' => [
                        'code' => 'ticket_not_found',
                        'message' => sprintf('No existe la incidencia %s', $code),
                    ],
                ],
                Response::HTTP_NOT_FOUND,
            );
        }

        return $this->json([
            'data' => $this->ticketToArray($ticket),
            'meta' => [
                'response_target_hours' => $this->ticketSlaCalculator->getResponseTargetHours($ticket),
            ]
        ]);
    }


    private function ticketToArray(Ticket $ticket): array
    {
        return [
            'code' => $ticket->getCode(),
            'title' => $ticket->getTitle(),
            'priority' => $ticket->getPriority()->value,
            'status' => $ticket->getStatus()->value,
            'category' => $ticket->getCategory() === null ? null : $ticket->getCategory()->getName(),
            'assignee' => $ticket->getAssignee() === null ? null : [
                'id' => $ticket->getAssignee()->getId(),
                'email' => $ticket->getAssignee()->getEmail()
            ]
        ];
    }
}