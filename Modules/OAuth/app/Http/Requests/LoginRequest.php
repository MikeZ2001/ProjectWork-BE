<?php

namespace Modules\OAuth\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Modules\OAuth\DataTransferObjects\AuthenticationDTO;

/**
 * @method AuthenticationDTO getDTO()
 *
 * @bodyParam email string required The user’s email. Example: user@example.com
 * @bodyParam password string required The user’s password. Example: secret
 */
class LoginRequest extends BaseFormRequest {
    
    protected function getDTOClassName(): string
    {
        return AuthenticationDTO::class;
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
    
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }
}