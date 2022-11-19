<?php

use App\Jobs\SendEmailVerificationMessage;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

use function Pest\Faker\faker;

it('test that the registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

it('tests that new users can register', function () {

    $password = faker()->password(8);

    $response = $this->post('/register', [
        'name'                  => faker()->name,
        'email'                 => faker()->safeEmail,
        'password'              => $password,
        'password_confirmation' => $password,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

it('tests that a verification email is sent on registration', function () {
    Notification::fake();

    $password = faker()->password(8);

    $this->post('/register', [
        'name'                  => faker()->name,
        'email'                 => faker()->safeEmail,
        'password'              => $password,
        'password_confirmation' => $password,
    ]);

    Notification::assertSentTo(auth()->user(), VerifyEmail::class);
});

it('tests that a verification email is queued on registration', function () {
    Queue::fake();

    $password = faker()->password(8);

    $this->post('/register', [
        'name'                  => faker()->name,
        'email'                 => faker()->safeEmail,
        'password'              => $password,
        'password_confirmation' => $password,
    ]);

    Queue::assertPushed(SendEmailVerificationMessage::class, 1);
});
