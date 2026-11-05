<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Category;
use App\Model\TicketPriority;
use App\Model\TicketStatus;

/**
 * Criterios de búsqueda del listado de incidencias.
 * Este objeto representa el estado del formulario.
 */
final class TicketSearchCriteria
{
    // Texto libre para buscar por código o título
    public ?string $query = '';

    // Filtramos por prioridad
    public ?TicketPriority $priority = null;

    // Filtramos por estado
    public ?TicketStatus $status = null;

    // Filtramos por categoría
    public ?Category $category = null;
}