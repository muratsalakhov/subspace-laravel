<?php

namespace Tests\Unit\Auth;

use App\DTO\Auth\UserRegisterDTO;
use App\Exceptions\Auth\UserAlreadyExistsException;
use App\Models\User;
use App\Services\Auth\RegistrationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Тест успешной регистрации пользователя
     * @return void
     * @throws UserAlreadyExistsException
     */
    public function test_user_can_be_registered_successfully(): void
    {
        // подготовка
        $name = 'Test User';
        $email = 'test@example.com';
        $password = 'password';

        $userRegisterDTO = new UserRegisterDTO(
            name: $name,
            email: $email,
            password: 'password'
        );

        $service = new RegistrationService();

        // действие
        $user = $service->register($userRegisterDTO);

        // проверка
        $this->assertEquals($name, $user->name);
        $this->assertEquals($email, $user->email);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * Тест на проверку уникальности email при регистрации.
     * @return void
     */
    public function test_registration_fails_if_email_already_taken(): void
    {
        // подготовка
        $email = 'test@example.com';

        User::create([
            'name' => 'Existing User',
            'email' => $email,
            'password' => Hash::make('password')
        ]);

        $userRegisterDTO = new UserRegisterDTO(
            name: 'New User',
            email: $email,
            password: 'password'
        );

        $service = new RegistrationService();

        // действие + проверка
        $this->expectException(UserAlreadyExistsException::class);
        $service->register($userRegisterDTO);
    }
}
