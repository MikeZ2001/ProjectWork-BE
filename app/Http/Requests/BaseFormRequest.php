<?php
    
    namespace App\Http\Requests;
    
    use App\DataTransferObjects\DTO;
    use Illuminate\Foundation\Http\FormRequest;
    use RuntimeException;
    
    abstract class BaseFormRequest extends FormRequest
    {
        /**
         * Return the name of the DTO class to build and return whenever exporting to DTO.
         *
         * @return string
         */
        abstract protected function getDTOClassName(): string;
        
        /**
         * Generate a DTO based on request parameters received.
         *
         * @param string $baseClass
         *
         * @return DTO
         */
        protected function makeDTO(string $baseClass): DTO
        {
            $dto = new $baseClass(...$this->validated());
            if (!($dto instanceof DTO)) {
                throw new RuntimeException('Cannot create instance of a non-DTO object.');
            }
            return $dto;
        }
        
        /**
         * Return a DTO created based on request parameters received.
         *
         * @return DTO
         */
        public function getDTO(): DTO
        {
            return $this->makeDTO($this->getDTOClassName());
        }
    }
