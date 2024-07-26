<?php

namespace App\Models;

use Illuminate\Support\Arr;

enum Status: string
{
    case Active = "A";
    case Complete = "C";
    case Hold = "H";
    case Cancel = "X";

    public static function allValues(): array
    {
        return Arr::pluck(self::cases(), "value");
    }
}
