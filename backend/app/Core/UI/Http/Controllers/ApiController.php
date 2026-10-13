<?php

declare(strict_types=1);

namespace App\Core\UI\Http\Controllers;

use App\Core\Application\Command\CommandBusInterface;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends BaseController
{
    public function __construct(
        protected readonly ResponseFactory $responseFactory,
        protected readonly CommandBusInterface $commandBus,
    ) {}

    protected function responseMessage(
        string $message,
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return $this->responseFactory->json([
            'status' => $statusCode,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function responseData(
        array $data,
        ?string $message = null,
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        $array = ['status' => $statusCode];

        if ($message) {
            $array['message'] = $message;
        }

        $array['data'] = $data;

        return $this->responseFactory->json($array, $statusCode);
    }
}
