<?php

namespace App\Exception;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Exception\ItemNotFoundException;
use ApiPlatform\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CustomExceptionFilter
{
  public function onKernelException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();

    $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
    $message = 'Une erreur inattendue s\'est produite. Veuillez réessayer plus tard.';

    if ($exception instanceof HttpExceptionInterface) {
      $statusCode = $exception->getStatusCode();
      $message = $exception->getMessage();
    } elseif ($exception instanceof NotFoundHttpException) {
      $statusCode = Response::HTTP_NOT_FOUND;
      $message = $exception->getMessage();
    } elseif ($exception instanceof AccessDeniedHttpException || $exception instanceof AccessDeniedException) {
      $statusCode = Response::HTTP_FORBIDDEN;
      $message = 'Vous n\'êtes pas autorisé à accéder à cette ressource.';
    } elseif ($exception instanceof ItemNotFoundException) {
      $statusCode = Response::HTTP_NOT_FOUND;
      $message = 'La ressource demandée est introuvable.';
    } elseif ($exception instanceof InvalidArgumentException || $exception instanceof RuntimeException) {
      $statusCode = Response::HTTP_BAD_REQUEST;
      $message = $exception->getMessage();
    }

    $response = new JsonResponse([
      'statusCode' => $statusCode,
      'message' => $message,
    ], $statusCode);

    $event->setResponse($response);
  }
}
