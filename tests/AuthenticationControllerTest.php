<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthenticationControllerTest extends WebTestCase
{
    public function testSuccessfulRegistration()
{
    $formData = [
        'username' => 'johndoe',
        'email' => 'johndoe@example.com',
        'password' => 'testpassword',
        'plainPassword' => 'testpassword',
    ];

    $client = static::createClient();
    $crawler = $client->request('GET', '/register');
    $form = $crawler->selectButton('Zarejestruj się')->form();
    $form->setValues($formData);
    $client->submit($form);

    // Potwierdź przekierowanie do strony logowania
    $this->assertResponseRedirects('/login');

    // Potwierdź, że nowy użytkownik jest zapisany w bazie danych
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'johndoe']);
    $this->assertNotNull($user);
    $this->assertTrue($user->isActive());
}
public function testFailedRegistrationInvalidData()
{
    $formData = [
        'username' => 'johndoe', // Brak adresu email
        'password' => 'testpassword',
        'plainPassword' => 'testpassword',
    ];

    $client = static::createClient();
    $crawler = $client->request('GET', '/register');
    $form = $crawler->selectButton('Zarejestruj się')->form();
    $form->setValues($formData);
    $client->submit($form);

    // Potwierdź wyświetlenie błędów walidacji
    $this->assertSelectorTextContains('html', 'Ten adres e-mail jest nieprawidłowy.');
    $this->assertSelectorTextContains('html', 'Pole "Email" jest wymagane.');

    // Potwierdź, że użytkownik nie został zarejestrowany
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'johndoe']);
    $this->assertNull($user);
}
public function testFailedRegistrationExistingUser()
{
    $existingUser = new User();
    $existingUser->setUsername('johndoe');
    $existingUser->setEmail('johndoe@example.com');
    $existingUser->setPassword('hashedpassword');
    $existingUser->setActive(true);
    $this->entityManager->persist($existingUser);
    $this->entityManager->flush();

    $formData = [
        'username' => 'johndoe',
        'email' => 'johndoe@example.com',
        'password' => 'testpassword',
        'plainPassword' => 'testpassword',
    ];

    $client = static::createClient();
    $crawler = $client->request('GET', '/register');
    $form = $crawler->selectButton('Zarejestruj się')->form();
    $form->setValues($formData);
    $client->submit($form);

    // Potwierdź wyświetlenie komunikatu o błędzie
    $this->assertSelectorTextContains('html', 'Użytkownik o takiej nazwie użytkownika lub adresie e-mail już istnieje.');

    // Potwierdź, że użytkownik nie został ponownie zarejestrowany
    $users = $this->entityManager->getRepository(User::class)->findBy(['username' => 'johndoe']);
    $this->assertCount(1, $users);
}
}
