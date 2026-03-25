<?php

namespace App\Shared\Traits;

trait ArrayTransform
{
    public function toArray(): array
    {
        return get_object_vars($this);
    }

}
