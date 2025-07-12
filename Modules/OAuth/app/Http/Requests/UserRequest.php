<?php

namespace Modules\OAuth\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Modules\OAuth\DataTransferObjects\UserDTO;

/**
 * @method UserDTO getDTO()
 *
 * @bodyParam first_name string required The user’s first name. Example: John
 * @bodyParam last_name  string required The user’s last name. Example: Doe
 * @bodyParam email      string required The user’s email address. Example: jane.doe@example.com
 * @bodyParam password   string required The user’s password (min 8 chars). Example: secret1234
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

    /**
     * Provide example responses for successful and error cases.
     *
     * @return array<string, mixed>
     */
    public function responses(): array
    {
        return [
            '200' => [
                'description' => 'User created successfully',
                'body' => [
                    'id'         => 1,
                    'first_name' => 'John',
                    'last_name'  => 'Doe',
                    'email'      => 'jane.doe@example.com',
                ],
            ],
            '422' => [
                'description' => 'Validation Error',
                'body' => [
                    'message' => 'Validation failed',
                    'errors'  => [
                        'email'    => ['The email field is required.'],
                        'password' => ['The password must be at least 8 characters.'],
                    ],
                ],
            ],
        ];
    }
}