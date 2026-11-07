<?php

namespace App\Demo;

use App\Models\User;
use Xelqun\Panels\Auth\LoginForm;

/**
 * Anyone arriving at the demo can sign in with one click.
 */
class DemoLoginForm extends LoginForm
{
    /**
     * @return array{email: string, password: string}
     */
    protected function getFillSource(): array
    {
        return ['email' => User::DEMO_EMAIL, 'password' => User::DEMO_PASSWORD];
    }
}
