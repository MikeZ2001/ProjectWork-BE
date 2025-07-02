<?php

namespace Modules\Account\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Modules\Account\DataTransferObjects\AccountDTO;

/**
 * @method AccountDTO getDTO()
 */
class AccountRequest extends BaseFormRequest
{

    protected function getDTOClassName(): string
    {
        return AccountDTO::class;
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
            'balance' => 'required|numeric',
            'open_date' => 'required|string',
            'close_date' => 'nullable|string',
            'status' => 'required|string',
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