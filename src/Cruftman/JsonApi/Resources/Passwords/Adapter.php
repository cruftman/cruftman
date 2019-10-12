<?php

namespace Cruftman\JsonApi\Resources\Passwords;

class Adapter extends \Cruftman\JsonApi\Eloquent\Adapter
{
    protected $modelClass = \Cruftman\Models\Password::class;

    protected function user()
    {
        return $this->hasOne();
    }
}
