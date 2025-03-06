<?php

namespace App\Shared\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateDevelopmentUserCommand extends Command
{
    protected $signature = 'create:super-admin';

    protected $description = 'Create a new user with unrestricted permissions';

    public function handle(): void
    {
        $this->output->title($this->description);

        $name = $this->ask("Name");
        $email = $this->ask('Email');
        $password = $this->secret("Password");
        $confirmPassword = $this->secret("Confirm Password");

        while ($password !== $confirmPassword) {
            $this->error("The confirmation password you entered does not match the initial password. Please, try Again");
            $password = $this->secret("Password");
            $confirmPassword = $this->secret("Confirm Password");
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        $user->markEmailAsVerified();

        $user->assignRole('Super Admin');

        $this->output->success("User '$name' created!");
    }
}
