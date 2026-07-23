<?php

namespace App\Enums;

enum CorrectRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
}
