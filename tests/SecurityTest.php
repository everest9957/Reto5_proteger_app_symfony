<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityTest extends WebTestCase
{
    // ---------- AUTENTICACIÓN ----------

    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testDashboardRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/dashboard');

        $this->assertResponseRedirects('/login');
    }

    public function testLoginWithValidCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'user@bank.com',
            'password' => 'user123',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/dashboard');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form([
            'email' => 'user@bank.com',
            'password' => 'wrongpassword',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger, .invalid-feedback, [role=alert]');
    }

    // ---------- AUTORIZACIÓN: ADMIN ----------

    public function testUserCannotAccessAdminPanel(): void
    {
        $client = static::createClient();
        $user = $this->getUserByEmail('user@bank.com');
        $client->loginUser($user);

        // Symfony sigue el redirect internamente y devuelve la página de acceso denegado
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.error-code', '403');
        $this->assertSelectorTextContains('.error-title', 'Acceso Denegado');
    }

    public function testAdminCanAccessAdminPanel(): void
    {
        $client = static::createClient();
        $admin = $this->getUserByEmail('admin@bank.com');
        $client->loginUser($admin);

        $client->request('GET', '/admin');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Panel de Administración');
    }

    // ---------- AUTORIZACIÓN: CUENTAS ----------

    public function testUserCanViewOwnAccounts(): void
    {
        $client = static::createClient();
        $user = $this->getUserByEmail('user@bank.com');
        $client->loginUser($user);

        $client->request('GET', '/accounts');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Mis Cuentas');
    }

    public function testUserCannotViewOtherUsersAccount(): void
    {
        $client = static::createClient();
        $user = $this->getUserByEmail('user@bank.com');
        $client->loginUser($user);

        // Cuenta #3 pertenece al admin
        $client->request('GET', '/accounts/3');

        // Symfony sigue el redirect internamente y devuelve la página de acceso denegado
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.error-code', '403');
    }

    public function testAdminCanViewAnyAccount(): void
    {
        $client = static::createClient();
        $admin = $this->getUserByEmail('admin@bank.com');
        $client->loginUser($admin);

        // Cuenta #3 pertenece al admin mismo
        $client->request('GET', '/accounts/3');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Cuenta Empresa');
    }

    // ---------- HELPERS ----------

    private function getUserByEmail(string $email): User
    {
        $container = static::getContainer();
        /** @var UserRepository $userRepository */
        $userRepository = $container->get(UserRepository::class);
        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => $email]);

        return $user;
    }
}