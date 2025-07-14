<?php

namespace Modules\Account\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use Modules\Account\DataTransferObjects\AccountDTO;
use Modules\Account\Models\AccountStatus;
use Modules\Account\Models\AccountType;

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
            'name' => 'required|string',
            'type' => ['required', 'string', Rule::enum(AccountType::class)],
            'balance' => 'required|numeric|min:0|regex:/^\d{1,13}(\.\d{1,2})?$/',
            'open_date' => 'required|string',
            'close_date' => 'nullable|string|after_or_equal:open_date',
            'status' => ['required', 'string', Rule::enum(AccountStatus::class)],
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