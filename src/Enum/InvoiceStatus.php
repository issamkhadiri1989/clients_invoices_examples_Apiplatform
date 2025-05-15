<?php

declare(strict_types=1);

namespace App\Enum;

enum InvoiceStatus: string
{
    case CREATED = 'created';

    case PARTIALLY_PAID = 'partially_paid';

    case UNPAID = 'unpaid';

    case PAID = 'paid';
}
