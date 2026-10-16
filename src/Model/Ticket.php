<?php

declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;

/**
 * Representa una incidencia de IssueFlow como un objeto PHP.
 *
 * Hasta la clase anterior cada ticket era un array asociativo. Ahora damos
 * un paso hacia POO: todos los datos y comportamientos de una incidencia
 * quedan agrupados bajo una misma clase.
 *
 * Esta clase NO es todavía una entidad Doctrine. La persistencia llegará
 * más adelante; hoy queremos comprender primero el modelo orientado a objetos.
 */
final class Ticket {
    /**
     * Constructor Property Promotion (PHP moderno).
     *
     * Al escribir "private string $id" dentro del constructor, PHP hace dos
     * cosas: declara la propiedad y asigna automáticamente el argumento recibido.
     * Esto evita repetir la declaración y la asignación manual de cada propiedad.
     */
    public function __construct(
        private readonly string $id,
        private string $title,
        private TicketPriority $priority,
        private TicketStatus $status
    ) {
        /* Verificamos la forma del id comparando con una regex (expresiones regulare) */
        if (preg_match('/^INC-\d{4}$/', $this->id) !== 1) {
            throw new InvalidArgumentException('El formato del id del ticket no es válido');
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
    public function getId(): string {
        return $this->id;
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