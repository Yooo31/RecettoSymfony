<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide.')]
    #[Assert\Length(min: 3, minMessage: 'Ce champ est trop court.', max: 100, maxMessage: 'Ce champ est trop long.')]
    public string $name = '';

    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide.')]
    #[Assert\Email(message: 'Veuillez saisir une adresse email valide.')]
    public string $email = '';

    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide.')]
    public string $service = '';

    #[Assert\NotBlank(message: 'Ce champ ne doit pas être vide.')]
    #[Assert\Length(min: 10, minMessage: 'Ce champ est trop court.', max: 1000, maxMessage: 'Ce champ est trop long.')]
    public string $message = '';
}