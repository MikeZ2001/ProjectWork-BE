<?php

namespace Modules\Account\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Modules\Account\DataTransferObjects\TransferDTO;

/**
 * @method getDTO()
 */
class TransferRequest extends BaseFormRequest
{
    protected function getDTOClassName(): string
    {
        return TransferDTO::class;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'from_account_id' => 'required|integer',
            'to_account_id' => 'required|integer|different:from_account_id',
            'amount' => 'required|numeric|gt:0|regex:/^\d{1,13}(\.\d{1,2})?$/',
            'description' => 'nullable|string|max:255',
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