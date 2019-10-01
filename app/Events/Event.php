<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;
}

// vim: syntax=php sw=4 ts=4 et:
