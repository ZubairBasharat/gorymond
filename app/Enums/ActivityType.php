<?php

namespace App\Enums;

enum ActivityType: int
{
    case Call = 1;
    case Event = 2;
    case Timer = 3;
    case Location = 4;
    case Text = 5;
}
