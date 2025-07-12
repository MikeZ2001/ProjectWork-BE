<?php
    
    namespace App\Exceptions;
    
    use Symfony\Component\HttpFoundation\Response;
    use Throwable;
    
    class ResourceNotCreatedException extends BaseException
    {
        public const ERROR_IDENTIFIER = 'resource_not_created';
        public const ERROR_MESSAGE = 'Resource not created';
        
        public function __construct(
            string $description = '',
            string $error = self::ERROR_IDENTIFIER,
            int $code = Response::HTTP_INTERNAL_SERVER_ERROR,
            ?Throwable $previous = null
        ) {
            parent::__construct(self::ERROR_MESSAGE, $description, $code, $error, $previous);
        }
    }
