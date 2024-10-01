<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class AccountStatus extends Enum
{
    const Trial  = 0;
    const Grace  = 1;
    const Active = 2;
    const Closed = 3;
}
