<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class ErrorController
{
    public function show(Throwable $exception)
     {
        return new JsonResponse("smth went wrong");
     }
}
