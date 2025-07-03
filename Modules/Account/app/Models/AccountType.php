<?php

namespace Modules\Account\Models;

enum AccountType: string
{
    case CHECKING = 'checking';
    case SAVINGS = 'savings';
    case INVESTMENT = 'investment';
}