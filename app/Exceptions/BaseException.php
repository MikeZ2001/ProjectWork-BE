<?php
    
    namespace App\Exceptions;
    
    use Exception;
    use Illuminate\Http\JsonResponse;
    use Throwable;
    
    class BaseException extends Exception
    {
        public function __construct(
            string $message = '',
            private readonly string $description = '',
            int $code = 500,
            private readonly string $error = 'exception',
            ?Throwable $previous = null
        ) {
            parent::__construct($message, $code, $previous);
        }
        
        /**
         * Report the exception.
         */
        public function report(): void
        {
        }
        
        /**
         * @return string
         */
        public function getDescription(): string
        {
            return $this->description;
        }
        
        /**
         * @return string
         */
        public function getError(): string
        {
            return $this->error;
        }
        
        /**
         * Render the exception as an HTTP response.
         */
        public function render(): JsonResponse
        {
            return response()->json([
                'error' => $this->getError(),
                'error_description' => $this->getDescription(),
                'message' => $this->getMessage(),
            ], $this->getCode());
        }
    }
