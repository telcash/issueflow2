<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Category;
use App\Entity\Ticket;
use App\Entity\User;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Comandos de apoyo para los datos de demostración de IssueFlow.
 *
 * En la Clase 20 ampliamos el seed para crear categorías y usuarios y para
 * relacionar los tickets ya persistidos con esas nuevas Entities.
 */
final class TicketCommands
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TicketRepository $ticketRepository,
    ) {
    }

    #[AsCommand(
        name: 'app:ticket:seed',
        description: 'Carga y relaciona los datos iniciales de IssueFlow.',
    )]
    public function seed(OutputInterface $output): int
    {
        // 1) Nos aseguramos de que existan los tres tickets de las clases previas.
        $definitions = [
            ['INC-1001', 'No puedo iniciar sesión', TicketPriority::Urgent, TicketStatus::Open],
            ['INC-1002', 'Error en la factura', TicketPriority::High, TicketStatus::InProgress],
            ['INC-1003', 'Actualizar datos de contacto', TicketPriority::Normal, TicketStatus::Closed],
        ];

        foreach ($definitions as [$code, $title, $priority, $status]) {
            if ($this->ticketRepository->findByCode($code) !== null) {
                continue;
            }

            $this->entityManager->persist(new Ticket(
                code: $code,
                title: $title,
                priority: $priority,
                status: $status,
            ));
        }

        // 2) El repository genérico de Doctrine es suficiente para este seed.
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var Category|null $accountCategory */
        $accountCategory = $categoryRepository->findOneBy(['name' => 'Cuenta']);

        if ($accountCategory === null) {
            $accountCategory = new Category('Cuenta');
            $this->entityManager->persist($accountCategory);
        }

        /** @var Category|null $billingCategory */
        $billingCategory = $categoryRepository->findOneBy(['name' => 'Facturación']);

        if ($billingCategory === null) {
            $billingCategory = new Category('Facturación');
            $this->entityManager->persist($billingCategory);
        }

        /** @var User|null $ana */
        $ana = $userRepository->findOneBy(['email' => 'ana@issueflow.local']);

        if ($ana === null) {
            $ana = new User('Ana Soporte', 'ana@issueflow.local');
            $this->entityManager->persist($ana);
        }

        // Un flush intermedio hace que las Entities nuevas queden gestionadas y
        // con id técnico disponible antes de inspeccionar las relaciones.
        $this->entityManager->flush();

        // 3) Recuperamos tickets gestionados por Doctrine y cambiamos relaciones.
        $ticket1001 = $this->ticketRepository->findByCode('INC-1001');
        $ticket1002 = $this->ticketRepository->findByCode('INC-1002');
        $ticket1003 = $this->ticketRepository->findByCode('INC-1003');

        if ($ticket1001 !== null) {
            // addTicket() mantiene también Ticket::$category sincronizado.
            $accountCategory->addTicket($ticket1001);
            $ticket1001->assignTo($ana);
        }

        if ($ticket1002 !== null) {
            $billingCategory->addTicket($ticket1002);
            $ticket1002->assignTo($ana);
        }

        if ($ticket1003 !== null) {
            $accountCategory->addTicket($ticket1003);
            // Dejamos este ticket sin asignar para demostrar que la relación es nullable.
            $ticket1003->assignTo(null);
        }

        // Doctrine inspecciona el owning side de las relaciones durante flush().
        $this->entityManager->flush();

        $output->writeln('Datos demo relacionados correctamente.');
        $output->writeln('INC-1001 -> Cuenta / Ana Soporte');
        $output->writeln('INC-1002 -> Facturación / Ana Soporte');
        $output->writeln('INC-1003 -> Cuenta / sin asignar');

        return Command::SUCCESS;
    }
}
