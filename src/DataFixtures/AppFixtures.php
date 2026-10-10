<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // --- ADMIN ---
        $admin = new User();
        $admin->setEmail('admin@bank.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // --- USER ---
        $user = new User();
        $user->setEmail('user@bank.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user123'));
        $manager->persist($user);

        // --- CUENTAS para el USER ---
        $account1 = new Account();
        $account1->setName('Cuenta Corriente');
        $account1->setBalance('1500.50');
        $account1->setOwner($user);
        $manager->persist($account1);

        $account2 = new Account();
        $account2->setName('Cuenta de Ahorros');
        $account2->setBalance('8500.00');
        $account2->setOwner($user);
        $manager->persist($account2);

        // --- CUENTAS para el ADMIN ---
        $account3 = new Account();
        $account3->setName('Cuenta Empresa');
        $account3->setBalance('50000.00');
        $account3->setOwner($admin);
        $manager->persist($account3);

        $manager->flush();
    }
}