<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use InvalidArgumentException;

/**
 * Usuario mínimo para asignar incidencias (técnico)
 * Aún no implementamos autenticación
 */
#[ORM\Entity]
#[ORM\Table(name: 'users')]
final class User
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
        #[ORM\Column(length: 120)]
        private string $name,

        #[ORM\Column(length: 120, unique: true)]
        private string $email,

        #[ORM\Column(type: Types::DATE_IMMUTABLE)]
        private DateTimeImmutable $createdAt = new DateTimeImmutable()
    ){
        /* Normalizamos $name y $email */
        $normalizedName = trim($name);
        $normalizedEmail = strtolower(trim($email));

        // Validamos que el nombre de usuario no está vacío
        if ($normalizedName === '') {
            throw new InvalidArgumentException('El nombre de usuario no puede estar vacío');
        }

        // Validamos que email tiene un formato correcto
        if (filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('El email no es válido');
        }

        $this->name = $normalizedName;
        $this->email = $normalizedEmail;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}