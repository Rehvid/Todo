<?php

namespace App\Enum;

enum Status: int
{
    case OPEN = 1;
    case IN_PROGRESS = 2;
    case IN_REVIEW = 3;
    case DONE = 4;
    case CANCELLED = 5;
}
