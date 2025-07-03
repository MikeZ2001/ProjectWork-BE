<?php

namespace Modules\Account\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Modules\Account\DataTransferObjects\TransactionDTO;

/**
 * @method getDTO()
 */
class TransactionRequest extends BaseFormRequest
{
    protected function getDTOClassName(): string
    {
        return TransactionDTO::class;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string',
            'amount' => 'required|numeric',
            'date' => 'required|string',
            'description' => 'nullable|string',
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