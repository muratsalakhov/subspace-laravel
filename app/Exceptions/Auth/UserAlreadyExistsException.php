<?php

namespace App\Exceptions\Auth;

use Exception;

class UserAlreadyExistsException extends Exception
{
    protected $message = 'Пользователь уже существует';
    protected $code = 422;
}
