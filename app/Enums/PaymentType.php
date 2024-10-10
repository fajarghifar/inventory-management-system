<?php

namespace App\Enums;

enum PaymentType: string
{
    case CASH = 'cash';

    case CHEQUE = 'cheque';

    case DUE = 'due';
}
