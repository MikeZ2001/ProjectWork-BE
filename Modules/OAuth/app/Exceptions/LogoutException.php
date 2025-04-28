<?php

namespace Modules\OAuth\Exceptions;

use App\Exceptions\BaseException;
use Symfony\Component\HttpFoundation\Response;

class LogoutException extends BaseException
{
    public function __construct(string $description = '', string $error = 'exception')
    {
        parent::__construct('Logout exception.', $description, Response::HTTP_BAD_REQUEST, $error);
    }
}
