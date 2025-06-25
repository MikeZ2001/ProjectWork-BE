<?php

namespace Modules\Account\Models;

enum AccountType: string
{
    case CHECKINGS = 'checkings';
    case SAVING = 'saving';
    case INVESTMENT = 'investment';
}