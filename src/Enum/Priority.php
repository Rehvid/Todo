<?php

namespace App\Enum;

enum Priority: int
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;
    case VERY_HIGH = 4;
}