<?php

namespace App\Service\Users;

use App\Entity\Users\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function newSubmit(Users $users){

        if ($users->getUsername() === null){
            return new JsonResponse(['message' => 'Please fill the username'],400);
        }
        if ($users->getPassword() === null){
            return new JsonResponse(['message' => 'Please fill the password'],400);
        }
        if ($users->getEmail() === null){
            return new JsonResponse(['message' => 'Please fill the email'],400);
        }

        $existingEmail = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $users->getEmail()]);
        $existingUsername = $this->entityManager->getRepository(Users::class)->findOneBy(['username' => $users->getUsername()]);

        if ($existingEmail instanceof Users || $existingUsername instanceof Users){
            return new JsonResponse(['message' => 'Email or Username is already in use'],400);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($users, $users->getPassword());
        $users->setPassword($hashedPassword);

        $users->setRoles(["ROLE_USER"]);
        $this->entityManager->persist($users);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Creation successful', 'redirect' => '/login'], 200);
    }

    public function updateUser(Users $users, Request $request){

        $request = $request->request->all();

        $username = trim(htmlspecialchars($request['update_users']['username']));
        $password = trim(htmlspecialchars($request['update_users']['password']));
        $email = trim(htmlspecialchars($request['update_users']['email']));


        if ($username === ''){
            return new JsonResponse(['message' => 'Please fill the username'],400);
        }
        if ($email === ''){
            return new JsonResponse(['message' => 'Please fill the email'],400);
        }


        $existingEmail = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);
        $existingUsername = $this->entityManager->getRepository(Users::class)->findOneBy(['username' => $username]);

        if ($existingEmail instanceof Users){
            if ($existingEmail->getEmail() !== $users->getEmail()) {
                //L'email à changer mais n'est pas dispo
                return new JsonResponse(['message' => 'Email or Username is already in use'], 400);
            }
        }
        $users->setEmail($email);

        if ($existingUsername instanceof Users){
            if ($existingUsername->getUsername() !== $users->getUsername()) {
                //Le username à changer mais n'est pas dispo
                return new JsonResponse(['message' => 'Email or Username is already in use'], 400);
            }
        }
        $users->setUsername($username);

        if ($password !== ''){
            $hashedPassword = $this->passwordHasher->hashPassword($users, $password);
            $users->setPassword($hashedPassword);
        }

        $this->entityManager->persist($users);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Update successful', 'redirect' => '/'], 200);
    }
}