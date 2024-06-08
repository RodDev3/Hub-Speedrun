<?php

namespace App\Service\Users;

use App\Entity\Users\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function newSubmit(Users $users){

        //TODO VERIF SUR EMAIL ET USERNAME UNIQUE
        //TODO Verif sur syntaxe d'email Validator ???
        if ($users->getUsername() === ""){
            return new JsonResponse(['message' => 'Please fill the username'],400);
        }
        if ($users->getPassword() === ""){
            return new JsonResponse(['message' => 'Please fill the password'],400);
        }
        if ($users->getEmail() === ""){
            return new JsonResponse(['message' => 'Please fill the email'],400);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($users, $users->getPassword());
        $users->setPassword($hashedPassword);

        $users->setRoles(["ROLE_USER"]);
        $this->entityManager->persist($users);
        $this->entityManager->flush();

        //TODO Redireaction
        return new JsonResponse(['message' => 'Creation successful'], 200);
    }
}