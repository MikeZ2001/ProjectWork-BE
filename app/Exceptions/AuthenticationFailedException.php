<?php
    
    namespace App\Exceptions;
    
    use Symfony\Component\HttpFoundation\Response;
    
    class AuthenticationFailedException extends BaseException
    {
        public function __construct(string $description = '', string $error = 'exception')
        {
            parent::__construct('Authentication failed exception.', $description, Response::HTTP_BAD_REQUEST, $error);
        }
    }
