<?php

declare(strict_types=1);

namespace App\Repository;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use App\Contract\TicketCatalogInterface;
use App\Entity\Ticket;
use App\Form\Model\TicketSearchCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use Doctrine\ORM\Tools\Pagination\Window;
use Doctrine\ORM\Tools\Pagination\WindowPage;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository Doctrine de Ticket
 * Implementa TicketCatalogInterface
 */
#[AsAlias(TicketCatalogInterface::class)]
final class TicketRepository extends ServiceEntityRepository implements TicketCatalogInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        // Usamos el constructor del padre para registrar la entidad Ticket
        parent::__construct($registry, Ticket::class);
    }

    // Método que devuelve todas las incidencias
    public function all(): array
    {
        return $this->findBy([], ['id' => 'ASC']);
    }

    // Método que devuelve una incidencia dado su code, si no encuentra devuelve null
    public function findByCode(string $code): ?Ticket
    {
        return $this->findOneBy(['code' => $code]);
    }

    /**
     * Construye una consulta con los filtros y devuelve una página de resultados
     */
    public function search(TicketSearchCriteria $criteria, int $page, int $pageSize): WindowPage
    {
        /**
         * Construimos una query que pide todos los tickets, agrega
         * la información de category y assignee y ordena por id
         * de manera descendente
         */
        $queryBuilder = $this->createQueryBuilder('ticket')
            ->leftJoin('ticket.category', 'category')
            ->addSelect('category')
            ->leftJoin('ticket.assignee', 'assignee')
            ->addSelect('assignee')
            ->orderBy('ticket.id', 'DESC');
        
        // Normalizamos el primer criterio de búsqueda (título o código)
        $term = $criteria->query ? trim($criteria->query) : '';
        
        // Si hay criterio de búsqueda por título o código lo agregamos a la query
        if ($term !== '') {
            $queryBuilder
                // andWhere agrega un filtro o condición a la query
                // LOWER hace que se ignoren mayúsculas y minúsculas al comparar
                // LIKE compara las cadenas de texto
                ->andWhere(
                    'LOWER(ticket.code) LIKE :term OR LOWER(ticket.title) LIKE :term'
                )
                // Evita concatenar directamente la entrada del usuario
                // Evita inyecciones SQL
                ->setParameter('term', '%'.strtolower($term).'%');
        }

        // Aplicamos filtro por prioridad si no es null
        if ($criteria->priority !== null) {
            $queryBuilder
                ->andWhere('ticket.priority = :priority')
                ->setParameter('priority', $criteria->priority->value);
        }

        // Aplicamos filtro por status si no es null
        if ($criteria->status !== null) {
            $queryBuilder
                ->andWhere('ticket.status = :status')
                ->setParameter('status', $criteria->status->value);
        }

        // Aplicamos filtro por categoría si no es null
        if ($criteria->category !== null) {
            $queryBuilder
                ->andWhere('ticket.category = :category')
                ->setParameter('category', $criteria->category);
        }
        
        // La página nunca puede ser menor que 1
        $page = max(1, $page);

        /**
         * Del listado de resultados posibles solo trae los correspondientes
         * a la página definida por $page, que serían $pageSize resultados
         */
        return (new OffsetPaginator(fetchJoinCollection: false))->paginate(
            $queryBuilder,
            Window::fromPageNumberAndSize($page, $pageSize)
        );
    }
}