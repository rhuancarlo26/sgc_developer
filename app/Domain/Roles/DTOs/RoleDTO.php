<?php

namespace App\Domain\Roles\DTOs;

use Illuminate\Foundation\Http\FormRequest;

class RoleDTO
{
    public function __construct(

        public readonly string $name,
        public readonly string $guardName,
    )
    {
    }


    public static function fromFormRequest(FormRequest $request): RoleDTO
    {
        return new self(
            name: $request->validated('name'),
            guardName: $request->validated('guard_name', 'web')
        );
    }
}
