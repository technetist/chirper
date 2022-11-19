<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

it(
    'can render the login screen',
    fn() => get(route('login'))
        ->assertStatus(200)
);

it(
    'allows users to authenticate using the login screen',
    function () {
        $user = User::factory()->create();
        $response = post(route('login'), [
            'email'    => $user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
);

it(
    'does not allow users to authenticate with an invalid password',
    function () {
        $user = User::factory()->create();
        post(route('login'), [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
);
