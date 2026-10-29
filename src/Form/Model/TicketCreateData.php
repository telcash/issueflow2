<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Category;
use App\Entity\User;
use App\Model\TicketPriority;
use App\Model\TicketStatus;

/**
 * Datos de entrada para crear una incidencia desde un formulario.
 *
 * No es una Entity y Doctrine no la persiste. Su única responsabilidad es
 * recibir los datos que Symfony Form transforma desde la Request.
 *
 * Usamos propiedades públicas deliberadamente: este objeto es una estructura
 * simple de entrada, no nuestro modelo de dominio.
 */
final class TicketCreateData
{
    public string $code = '';
    public string $title = '';
    public TicketPriority $priority = TicketPriority::Normal;
    public TicketStatus $status = TicketStatus::Open;
    public ?Category $category = null;
    public ?User $assignee = null;
}