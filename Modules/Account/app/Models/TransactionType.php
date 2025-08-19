<?php

namespace Modules\Account\Models;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case TransferDeposit = 'transfer_deposit';
    case TransferWithdrawal = 'transfer_withdrawal';
}
