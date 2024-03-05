<?php

namespace Tests\Unit\Auth;

use App\DTO\Auth\UserRegisterDTO;
use App\Exceptions\Auth\UserAlreadyExistsException;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Auth\RegistrationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;
use Mockery;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Тестирование успешного входа
     * @return void
     */
    public function test_login_success(): void
    {
        // подготовка
        $email = 'test@example.com';
        $password = 'password';

        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        Auth::shouldReceive('attempt')->once()->andReturn(true);
        Auth::shouldReceive('user')->once()->andReturn($user);

        // действие
        $service = new AuthService();
        $result = $service->login($email, $password);

        // проверка
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($email, $result->email);
    }

    /**
     * Тестирование неудачного входа
     * @return void
     */
    public function test_login_failure(): void
    {
        // подготовка
        Auth::shouldReceive('attempt')->once()->andReturn(false);

        // действие
        $service = new AuthService();
        $result = $service->login('wrong@example.com', 'password');

        // проверка
        $this->assertNull($result);
    }

    /**
     * Тестирование выхода
     * @return void
     */
    public function test_logout(): void
    {
        // подготовка
        $user = User::factory()->create();

        $accessToken = Mockery::mock(NewAccessToken::class);
        $accessToken->expects('delete');

        $user->withAccessToken($accessToken);

        // действие
        $service = new AuthService();
        $service->logout($user);

        // проверка
        $this->assertTrue(true); // не было ошибок
    }

    /**
     * Тестирование создания токена
     * @return void
     */
    public function test_create_token(): void
    {
        // подготовка
        $user = User::factory()->create();

        // действие
        $service = new AuthService();
        $tokenResult = $service->createToken($user);

        // проверка
        $this->assertNotEmpty($tokenResult->plainTextToken);
    }
}
