<?php

namespace Modules\OAuth\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Modules\OAuth\DataTransferObjects\UserDTO;

/**
 * @method UserDTO getDTO()
 */
class UserRequest extends BaseFormRequest
{
    protected function getDTOClassName(): string
    {
        return UserDTO::class;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
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