<?php

declare(strict_types=1);

namespace App\Model;

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
        private string $id,
        private string $title,
        private string $priority,
    ) {    
    }

    /* Crear los getters, para cada una de las propiedades */
    public function getId(): string {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getPriority(): string {
        return $this->priority;
    }

    /* Crear un método llamado isUrgent() que retorne
    true si la prioridad es 'urgent', o false en caso contrario */
    public function isUrgent (){
        return $this->priority === 'urgent';
    }

    /* Este método crea temporalmente los tickets en memoria (Esto
    es una data de prueba). (Antes usábamos
    una constante de clase) Más adelante este trabajo lo hará una capa de
    persistencia/Repository conectado a Doctrine y MySQL 
    Un método estático puede ser llamado directamente desde la clase sin
    haber instanciado un objeto: Ticket::getAllTickets()
    */
    public static function getAllTickets(): array {
        return [
            new Ticket(
                id: 'INC-1001',
                title: 'No puedo iniciar sesión',
                priority: 'urgent',
            ),
            new Ticket(
                id: 'INC-1002',
                title: 'Error en la factura',
                priority: 'high',
            ),
            new Ticket(
                id: 'INC-1003',
                title: 'Actualizar datos de contacto',
                priority: 'normal',
            ),
        ];
    }
}