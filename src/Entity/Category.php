<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use InvalidArgumentException;

/**
 * Entidad de categorías
 */
#[ORM\Entity]
#[ORM\Table(name: 'categories')]
final class Category
{
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

    public function __construct(
        #[ORM\Column(length: 120, unique: true)]
        private string $name,
    )
    {
        /* Normalizamos $name */
        $normalizedName = trim($name);

        // Validamos que el nombre de usuario no está vacío
        if ($normalizedName === '') {
            throw new InvalidArgumentException('El nombre de usuario no puede estar vacío');
        }

        $this->name = $normalizedName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}