<?php
    
    namespace App\Exceptions;
    
    use Symfony\Component\HttpFoundation\Response;
    use Throwable;
    
    class ResourceNotUpdatedException extends BaseException
    {
        public const  ERROR_IDENTIFIER = 'resource_not_updated';
        public const  ERROR_MESSAGE = 'Resource not updated';
        
        public function __construct(
            string $description = '',
            string $error = self::ERROR_IDENTIFIER,
            int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
            ?Throwable $previous = null
        ) {
            parent::__construct(self::ERROR_MESSAGE, $description, $code, $error, $previous);
        }
    }
