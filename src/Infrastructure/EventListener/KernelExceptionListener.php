<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use App\Infrastructure\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class KernelExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        if (str_starts_with($event->getRequest()->getPathInfo(), '/admin')) {
            return;
        }
        // You get the exception object from the received event
        $exception = $event->getThrowable();
        $response = $this->handleException($exception);
        // sends the modified response object to the event
        $event->setResponse($response);
    }

    private function handleException(\Throwable $exception): Response
    {
        $data['exception'] = [
            'message' => $exception->getMessage(),
            'code' => $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : $exception->getCode(),
        ];

        if ($exception instanceof ValidationException) {
            foreach ($exception->getErrors() as $field => $error) {
                if (\is_string($error)) {
                    $error = [$error];
                }

                if (\is_array($error)) {
                    $data['exception']['violations'][$field] = $error;
                }
            }
        }
        // Customize your response object to display the exception details
        $response = new JsonResponse($data);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}
