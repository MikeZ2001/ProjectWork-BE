<?php

namespace Modules\Account\Http\Requests;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;
use Modules\Account\DataTransferObjects\AccountDTO;
use Modules\Account\Models\AccountStatus;
use Modules\Account\Models\AccountType;

/**
 * @method AccountDTO getDTO()
 *
 * @bodyParam name string The account name. Example: "Savings Account"
 * @bodyParam type string The account type. Example: "savings"
 * @bodyParam balance number The balance. Example: 1500.00
 * @bodyParam open_date string The opening date. Example: "2025-07-01"
 * @bodyParam close_date string The closing date. Example: "2025-12-31"
 * @bodyParam status string The status. Example: "inactive"
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