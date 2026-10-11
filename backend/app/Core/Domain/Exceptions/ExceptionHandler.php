<?php

declare(strict_types=1);

namespace App\Core\Domain\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class ExceptionHandler extends Handler
{
    /** @var array<int, class-string<Throwable>> */
    protected $dontReport = [
        RouteNotFoundException::class,
    ];

    /** @var array<int, string> */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e): Response|JsonResponse|RedirectResponse
    {
        $isDebug = config('app.debug');

        return $isDebug
            ? $this->buildJsonResponse($e, true)
            : $this->buildJsonResponse($e);
    }

    private function buildJsonResponse(Throwable $e, bool $isDebug = false): JsonResponse
    {
        if ($e instanceof ThrottleRequestsException) {
            return $this->respond(['status' => Response::HTTP_TOO_MANY_REQUESTS, 'message' => __('core::validation.throttle')], $e, $isDebug);
        }

        if ($e instanceof ValidationException) {
            return $this->respond([
                'status' => $e->status,
                'message' => __('core::validation.invalid_data'),
                'errors' => $e->errors(),
            ], $e, $isDebug);
        }

        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return $this->respond(['status' => Response::HTTP_NOT_FOUND, 'message' => __('core::domain.model_not_found')], $e, $isDebug);
        }

        if ($e instanceof HttpException) {
            return $this->respond(['status' => $e->getStatusCode(), 'message' => $e->getMessage()], $e, $isDebug);
        }

        if ($e instanceof AuthenticationException) {
            return $this->respond(['status' => Response::HTTP_UNAUTHORIZED, 'message' => __('core::domain.unauthenticated')], $e, $isDebug);
        }

        return $this->buildDefaultResponse($e, $isDebug);
    }

    private function buildDefaultResponse(Throwable $e, bool $isDebug): JsonResponse
    {
        $status = $this->resolveHttpStatusCode($e);

        $responseData = [
            'status' => $status,
            'message' => $e->getMessage(),
        ];

        if ($isDebug) {
            $responseData['data'] = $this->getDebugData($e);
        }

        return new JsonResponse($responseData, $status, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return array<string, mixed>
     */
    private function getDebugData(Throwable $e): array
    {
        return [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace(),
        ];
    }

    /**
     * @param  array<string, mixed>  $responseData
     */
    private function respond(array $responseData, Throwable $e, bool $isDebug): JsonResponse
    {
        if ($isDebug) {
            $responseData['data'] = $this->getDebugData($e);
        }

        $status = isset($responseData['status']) && is_int($responseData['status'])
            ? $responseData['status']
            : Response::HTTP_INTERNAL_SERVER_ERROR;

        return new JsonResponse($responseData, $status, [], JSON_UNESCAPED_UNICODE);
    }

    private function resolveHttpStatusCode(Throwable $e): int
    {
        $code = $e->getCode();

        if (is_int($code) && $code >= 100 && $code <= 599) {
            return $code;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
