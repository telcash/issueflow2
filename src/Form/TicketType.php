<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use App\Form\Model\TicketCreateData;
use App\Model\TicketPriority;
use App\Model\TicketStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Describe los campos necesarios para crear un Ticket.
 * El Form Type no guarda nada en la base de datos. Solo
 * define cómo se transforman y presentan los datos del formulario
 */
final class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [ // TextType indica que es un campo de texto en el formulario
                'label' => 'Código', // etiqueta del campo de texto
                'attr' => [
                    'placeholder' => 'INC-1234', // Sugerencia de entrada
                    'pattern' => 'INC-[0-9]{4}', // Patrón de entrada esperado
                    'maxlength' => 8, // Longitud máxima aceptada
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Título',
                'attr' => [
                    'maxlength' => 180,
                ]
            ])
            ->add('priority', EnumType::class, [ // EnumType construye un selector basado en un enum PHP
                'class' => TicketPriority::class, // Indicamos el enum que se va a usar para construir el selector
                'label' => 'Prioridad',
                'choice_label' => static fn (TicketPriority $priority): string => $priority->getLabel() // Seleccionamos label para que sea la información que se muestra en las opciones del selector
            ])
            ->add('status', EnumType::class, [
                'class' => TicketStatus::class,
                'label' => 'Estado',
                'choice_label' => static fn (TicketStatus $status): string => $status->getLabel()
            ])
            ->add('category', EntityType::class, [ // EntityType construye un selector basado en una clase PHP, entidad Doctrine
                'class' => Category::class, // Indicamos la entidad que se va a usar para construir el selector
                'label' => 'Categoría',
                'choice_label' => 'name', // Indicamos el atributo de la entidad que se mostrará en las opciones del selector
                'required' => false,
                'placeholder' => 'Sin asignar',
            ])
            ->add('assignee', EntityType::class, [
                'class' => User::class,
                'label' => 'Asignado a',
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Sin asignar'
            ])
            ->add('save', SubmitType::class, [ // SubmitType crea un botón de envío
                'label' => 'Crear incidencia',
            ]);
    }

    // Symfony sepa que el objeto de datos esperado es TicketCreateData
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketCreateData::class,
        ]);
    }
}