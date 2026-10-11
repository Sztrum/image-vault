<?php

declare(strict_types=1);

namespace App\Core\UI\Http\Controllers;

use Illuminate\Http\JsonResponse;

class HealthController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        return $this->responseData([
            'healthy' => true,
        ]);
    }
}
