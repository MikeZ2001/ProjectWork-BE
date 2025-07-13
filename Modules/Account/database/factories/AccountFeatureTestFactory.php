<?php

namespace Modules\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Account\Models\Account;
use Modules\Account\Models\AccountStatus;
use Modules\Account\Models\AccountType;

class AccountFeatureTestFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        $openDate = $this->faker->dateTimeBetween('-1 years', 'now');

        $status = $this->faker->randomElement(AccountStatus::cases());
        $closeDate = $status === 'closed'
            ? $this->faker->dateTimeBetween($openDate, 'now')
            : null;

        return [
            'name' => $this->faker->name(),
            'type'       => $this->faker->randomElement(AccountType::cases()),
            'balance'    => $this->faker->randomFloat(2, 0, 100000),
            'open_date'  => $openDate,
            'close_date' => $closeDate,
            'status'     => $status,
        ];
    }
}