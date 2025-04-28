<?php
    
    namespace Modules\OAuth\DataTransferObjects;
    
    use App\DataTransferObjects\DTO;
    
    readonly class AuthenticationDTO extends DTO
    {
        public function __construct(
            protected ?string $email,
            protected ?string $password
        ) {
        }
        
        /**
         * @return string|null
         */
        public function getEmail(): ?string
        {
            return $this->email;
        }
        
        /**
         * @return string|null
         */
        public function getPassword(): ?string
        {
            return $this->password;
        }
    }
