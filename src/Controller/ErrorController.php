<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


class ErrorController
{
    public function show(HttpExceptionInterface $exception)
     {
        return new JsonResponse([
            "httpErrorCode"=>$exception->getStatusCode(), 
            "message"=>$exception->getMessage()]);
     }
}
