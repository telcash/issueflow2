<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\TicketCatalogInterface;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository Doctrine de Ticket
 * Implementa TicketCatalogInterface
 */
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
}