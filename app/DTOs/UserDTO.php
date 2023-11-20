<?php

namespace App\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class UserDTO
{
    public function __construct(

        public readonly string $name,
        public readonly string $email,
    )
    {
    }

    public static function fromFormRequest(FormRequest $request): UserDTO
    {
        return new self(
            name: $request->validated('name'),
            email: $request->validated('email')
        );
    }

}
