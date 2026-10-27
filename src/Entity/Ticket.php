<?php

declare(strict_types=1);

namespace App\Entity;

use App\Model\TicketPriority;
use App\Model\TicketStatus;
use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use InvalidArgumentException;

/**
 * Entity Doctrine de una incidencia de IssueFlow.
 *
 * Sigue siendo un objeto PHP con reglas de dominio. Los atributos #[ORM\...]
 * añaden el mapping necesario para que Doctrine sepa cómo persistirlo.
 */
#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
final class Ticket {
    /**
     * Identificador técnico de base de datos.
     *
     * Empieza en null porque una Entity recién creada todavía no ha sido
     * insertada. Doctrine/la base asignarán el valor al persistirla.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT, options: ['unsigned' => true])]
    private ?int $id = null;

    /**
     * Constructor Property Promotion (PHP moderno).
     *
     * Al escribir "private string $code" dentro del constructor, PHP hace dos
     * cosas: declara la propiedad y asigna automáticamente el argumento recibido.
     * Esto evita repetir la declaración y la asignación manual de cada propiedad.
     */
    public function __construct(
        #[ORM\Column(length: 8, unique: true)]
        private readonly string $code,

        #[ORM\Column(length: 180)]
        private string $title,

        #[ORM\Column(length: 20, enumType: TicketPriority::class)]
        private TicketPriority $priority,

        #[ORM\Column(length: 20,enumType: TicketStatus::class)]
        private TicketStatus $status = TicketStatus::Open
    ) {
        /* Verificamos la forma del id comparando con una regex (expresiones regulare) */
        if (preg_match('/^INC-\d{4}$/', $this->code) !== 1) {
            throw new InvalidArgumentException('El formato del código del ticket no es válido');
        }

        /* Hacemos un trim (eliminar espacios antes y después) del title */
        $normalizedTitle = trim($title);

        // Verificamos que la cadena de texto no esté vacía
        if ($normalizedTitle === '') {
            throw new InvalidArgumentException('El título no puede estar vacío');
        }

        $this->title = $normalizedTitle;
    }

    /* Crear los getters, para cada una de las propiedades */
    public function getId(): ?int {
        return $this->id;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getPriority(): TicketPriority {
        return $this->priority;
    }

    public function getStatus(): TicketStatus {
        return $this->status;
    }

    /* Crear un método llamado isUrgent() que invoca a través
    del atributo priority al método isUrgent() de TicketPriority, el
    cual retorna true si la prioridad es 'urgent', o false en caso contrario */
    public function isUrgent(): bool {
        return $this->priority->isUrgent();
    }

    /* Crear un método llamado isOpen() que invoca a través
    del atributo status al método isOpen() de TicketStatus, el
    cual retorna true si la prioridad es 'open', o false en caso contrario */
    public function isOpen(): bool {
        return $this->status->isOpen(); 
    }
}