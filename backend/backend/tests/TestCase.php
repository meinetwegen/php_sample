<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    /** Send the next requests as the given user (JWT bearer token). */
    protected function asUser(User $user): static
    {
        $this->app['auth']->forgetGuards();

        return $this->withToken(JWTAuth::fromUser($user));
    }
}
