<?php

namespace App\Enum;

enum UserRoles: string
{
    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';


    public static function toArray(): array
    {
        $enumArray = [];
        foreach (self::cases() as $enum) {
           $enumArray[$enum->name] = $enum->value;
        }

        return $enumArray;
    }

}