<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Subspace API",
 *     version="0.1"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @OA\Get(
     *     path="/test",
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     * todo: тестовый роут, удалить
     */
    public function getExample(): array
    {
        return [];
    }
}
