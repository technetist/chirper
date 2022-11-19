<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

it('tests that the reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request'));

    $response->assertStatus(200);
});

it('tests that a reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class);
});

it('tests that the reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get(route('password.reset', ['token' => $notification->token]));

        $response->assertStatus(200);

        return true;
    });
});

it('tests that a password can be reset with a valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), [
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post(route('password.store'), [
            'token'                 => $notification->token,
            'email'                 => $user->email,
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors();

        return true;
    });
});
