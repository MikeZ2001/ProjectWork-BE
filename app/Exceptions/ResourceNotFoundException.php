<?php
    
    namespace App\Exceptions;
    
    use Symfony\Component\HttpFoundation\Response;
    use Throwable;
    
    class ResourceNotFoundException extends BaseException
    {
        public const string ERROR_IDENTIFIER = 'resource_not_found';
        public const string ERROR_MESSAGE = 'Resource not found';
        
        public function __construct(
            string $description = '',
            string $error = self::ERROR_IDENTIFIER,
            int $code = Response::HTTP_NOT_FOUND,
            ?Throwable $previous = null
        ) {
            parent::__construct(self::ERROR_MESSAGE, $description, $code, $error, $previous);
        }
    }
