<?php

namespace App\Domain\CreditApplication\Entities;

enum ApplicationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
